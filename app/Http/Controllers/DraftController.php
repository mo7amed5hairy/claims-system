<?php

namespace App\Http\Controllers;

use App\Models\Draft;
use App\Services\DraftFinalizer;
use App\Support\DraftClosure;
use App\Support\DraftErrorTranslator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class DraftController extends Controller
{
    public function __construct(private DraftFinalizer $finalizer)
    {
        $this->middleware('auth');
    }

    /**
     * List current user's pending drafts (JSON for client sync).
     */
    public function mine(Request $request)
    {
        $drafts = Draft::forUser(Auth::id())
            ->pending()
            ->orderByDesc('updated_at')
            ->get(['id','uuid','type','title','payload','revision','updated_at']);

        return response()->json(['drafts' => $drafts]);
    }

    /**
     * Upsert a draft from client autosave.
     */
    public function sync(Request $request)
    {
        $data = $request->validate([
            'uuid' => ['required','string','max:64'],
            'type' => ['required','string', Rule::in(array_keys(Draft::TYPES))],
            'payload' => ['nullable','array'],
            'revision' => ['required','integer','min:1'],
        ]);

        $userId = Auth::id();
        $uuid = $data['uuid'];
        if (!Str::isUuid($uuid)) {
            return response()->json(['ok' => false, 'error' => 'UUID غير صالح'], 422);
        }

        if (DraftClosure::isClosed($userId, $uuid)) {
            return response()->json(['ok' => false, 'closed' => true]);
        }

        $payload = $this->sanitizePayload($data['payload'] ?? []);
        $title = Draft::titleFromPayload($data['type'], $payload);

        try {
            $draft = DB::transaction(function () use ($userId, $uuid, $data, $payload, $title) {
                $draft = Draft::where('uuid', $uuid)->lockForUpdate()->first();
                if ($draft) {
                    if ((int)$draft->user_id !== (int)$userId) {
                        abort(403);
                    }
                    if ($draft->status !== Draft::STATUS_PENDING) {
                        return $draft; // closed/finalized, ignore
                    }
                    if ((int)$data['revision'] < (int)$draft->revision) {
                        return $draft; // stale
                    }
                    $draft->type = $data['type'];
                    $draft->payload = $payload;
                    $draft->title = $title;
                    $draft->revision = max((int)$draft->revision, (int)$data['revision']);
                    $draft->save();
                    return $draft;
                }

                return Draft::create([
                    'uuid' => $uuid,
                    'user_id' => $userId,
                    'type' => $data['type'],
                    'title' => $title,
                    'payload' => $payload,
                    'revision' => (int)$data['revision'],
                    'status' => Draft::STATUS_PENDING,
                ]);
            });
        } catch (\Throwable $e) {
            report($e);
            return response()->json(['ok' => false, 'error' => DraftErrorTranslator::translate($e)], 500);
        }

        if ($draft->status !== Draft::STATUS_PENDING) {
            return response()->json(['ok' => false, 'closed' => true]);
        }

        return response()->json([
            'ok' => true,
            'draft' => [
                'uuid' => $draft->uuid,
                'revision' => $draft->revision,
                'updated_at' => $draft->updated_at?->toIso8601String(),
            ],
        ]);
    }

    /**
     * Drafts page (My Drafts).
     */
    public function index(Request $request)
    {
        $drafts = Draft::forUser(Auth::id())
            ->pending()
            ->orderByDesc('updated_at')
            ->paginate(30);

        return view('drafts.index', compact('drafts'));
    }

    /**
     * Finalize a draft into a real record.
     */
    public function finalize(Request $request, Draft $draft)
    {
        $this->authorizeDraft($draft);
        try {
            [$model, $redirect] = $this->finalizer->finalize($draft);
            if ($request->expectsJson()) {
                return response()->json(['ok' => true, 'redirect' => $redirect]);
            }
            return redirect($redirect)->with('success', 'تم حفظ المسودة بنجاح.');
        } catch (ValidationException $e) {
            if ($request->expectsJson()) {
                return response()->json(['ok' => false, 'errors' => $e->errors()], 422);
            }
            return back()->withErrors($e->errors());
        } catch (\Throwable $e) {
            report($e);
            $msg = DraftErrorTranslator::translate($e);
            if ($request->expectsJson()) {
                return response()->json(['ok' => false, 'error' => $msg], 500);
            }
            return back()->with('error', $msg);
        }
    }

    /**
     * Discard a draft.
     */
    public function discard(Request $request, Draft $draft)
    {
        $this->authorizeDraft($draft);
        if ($draft->status === Draft::STATUS_PENDING) {
            $draft->status = Draft::STATUS_DISCARDED;
            $draft->save();
        }
        DraftClosure::close($draft->user_id, $draft->uuid);

        if ($request->expectsJson()) {
            return response()->json(['ok' => true]);
        }
        return back()->with('success', 'تم حذف المسودة.');
    }

    /**
     * Update draft payload from drafts page (quick edit).
     */
    public function update(Request $request, Draft $draft)
    {
        $this->authorizeDraft($draft);
        if (!$draft->isPending()) {
            return response()->json(['ok' => false, 'error' => 'المسودة غير نشطة'], 409);
        }
        $data = $request->validate([
            'payload' => ['required','array'],
            'revision' => ['nullable','integer','min:1'],
        ]);
        $payload = $this->sanitizePayload($data['payload']);
        $draft->payload = array_merge($draft->payload ?? [], $payload);
        $draft->title = Draft::titleFromPayload($draft->type, $draft->payload);
        $draft->revision = max((int)$draft->revision + 1, (int)($data['revision'] ?? 0));
        $draft->save();

        return response()->json(['ok' => true, 'revision' => $draft->revision]);
    }

    /**
     * Show a draft for continuation - redirects to the appropriate form with draft_uuid.
     */
    public function continue(Request $request, Draft $draft)
    {
        $this->authorizeDraft($draft);
        if (!$draft->isPending()) {
            return redirect()->route('drafts.index')->with('error', 'المسودة غير نشطة.');
        }

        $payload = $draft->payload ?? [];
        $route = $this->resolveContinueRoute($draft, $payload);
        if (!$route) {
            return redirect()->route('drafts.index')->with('error', 'لا يمكن فتح هذه المسودة.');
        }

        // Store draft payload in session for the form to prefill
        session()->flash('draft_restore_uuid', $draft->uuid);
        session()->flash('draft_restore_payload', $payload);
        return redirect($route);
    }

    private function resolveContinueRoute(Draft $draft, array $payload): ?string
    {
        return match ($draft->type) {
            'claim_create' => route('claims.create', ['draft_uuid' => $draft->uuid]),
            'claim_edit' => isset($payload['_claim_id'])
                ? route('claims.edit', ['claim' => $payload['_claim_id'], 'draft_uuid' => $draft->uuid])
                : null,
            'prepaid_claim_create' => route('prepaid-claims.create', ['draft_uuid' => $draft->uuid]),
            'prepaid_claim_edit' => isset($payload['_claim_id'])
                ? route('prepaid-claims.edit', ['claim' => $payload['_claim_id'], 'draft_uuid' => $draft->uuid])
                : null,
            'payment_create' => route('payments.create', ['draft_uuid' => $draft->uuid]),
            'payment_edit' => isset($payload['_payment_id'])
                ? route('payments.edit', ['payment' => $payload['_payment_id'], 'draft_uuid' => $draft->uuid])
                : null,
            'prepaid_payment_create' => route('prepaid-payments.create', ['draft_uuid' => $draft->uuid]),
            'prepaid_payment_edit' => isset($payload['_payment_id'])
                ? route('prepaid-payments.edit', ['payment' => $payload['_payment_id'], 'draft_uuid' => $draft->uuid])
                : null,
            default => null,
        };
    }

    private function authorizeDraft(Draft $draft): void
    {
        $user = Auth::user();
        if ((int)$draft->user_id !== (int)$user->id && $user->role !== 'admin') {
            abort(403);
        }
    }

    private function sanitizePayload(array $payload): array
    {
        // Remove file inputs / internal tokens - never trust client to set user_id etc.
        unset($payload['_token'], $payload['_method'], $payload['attachments'], $payload['delivery_attachments']);
        // limit size defensively
        $json = json_encode($payload);
        if ($json === false || strlen($json) > 200000) {
            // keep only scalar values
            $clean = [];
            foreach ($payload as $k => $v) {
                if (is_scalar($v) || is_null($v)) $clean[$k] = $v;
            }
            return $clean;
        }
        return $payload;
    }

    /**
     * Discard by UUID (used by sendBeacon on form submit).
     */
    public function discardByUuid(Request $request)
    {
        $uuid = $request->input('uuid');
        $userId = Auth::id();
        if (!$uuid || !\Illuminate\Support\Str::isUuid($uuid)) return response()->json(['ok' => false], 422);
        $draft = Draft::where('uuid', $uuid)->where('user_id', $userId)->first();
        if ($draft && $draft->status === Draft::STATUS_PENDING) {
            $draft->status = Draft::STATUS_DISCARDED;
            $draft->save();
        }
        DraftClosure::close($userId, $uuid);
        return response()->json(['ok' => true]);
    }
}
