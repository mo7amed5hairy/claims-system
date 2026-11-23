<?php

namespace App\Services;

use App\Models\Draft;
use App\Modules\Claims\Models\Claim;
use App\Modules\Claims\Services\ClaimService;
use App\Support\DraftClosure;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class DraftFinalizer
{
    public function __construct(private ClaimService $claimService)
    {
    }

    /**
     * Finalize a draft into a real record. Returns [model, redirectRoute].
     * @throws ValidationException
     */
    public function finalize(Draft $draft): array
    {
        if (!$draft->isPending()) {
            throw ValidationException::withMessages(['draft' => 'هذه المسودة تم التعامل معها بالفعل.']);
        }

        return DB::transaction(function () use ($draft) {
            $result = match ($draft->type) {
                'claim_create' => $this->finalizeClaimCreate($draft),
                'claim_edit' => $this->finalizeClaimEdit($draft),
                default => throw ValidationException::withMessages(['type' => 'نوع المسودة غير مدعوم للحفظ النهائي.']),
            };

            DraftClosure::close($draft->user_id, $draft->uuid);
            $draft->delete();

            return $result;
        });
    }

    private function finalizeClaimCreate(Draft $draft): array
    {
        $data = $this->normalizeClaimPayload($draft->payload ?? [], null);
        $data['user_id'] = $draft->user_id;

        $rules = $this->claimRules($data, null);
        $validator = Validator::make($data, $rules);
        if ($validator->fails()) throw new ValidationException($validator);

        $claim = $this->claimService->createClaim($validator->validated() + ['user_id' => $draft->user_id]);
        return [$claim, route('claims.index')];
    }

    private function finalizeClaimEdit(Draft $draft): array
    {
        $payload = $draft->payload ?? [];
        $claimId = $payload['_claim_id'] ?? null;
        $claim = Claim::find($claimId);
        if (!$claim) {
            throw ValidationException::withMessages(['draft' => 'المطالبة الأصلية غير موجودة.']);
        }
        // Ownership/permission check: same user or admin
        $user = $draft->user;
        if ($user->role !== 'admin' && (int)$claim->user_id !== (int)$draft->user_id) {
            throw ValidationException::withMessages(['draft' => 'غير مصرح بتعديل هذه المطالبة.']);
        }

        $data = $this->normalizeClaimPayload($payload, $claim);
        $rules = $this->claimRules($data, $claim);
        $validator = Validator::make($data, $rules);
        if ($validator->fails()) throw new ValidationException($validator);

        $validated = $validator->validated();
        // preserve existing attachments - drafts don't carry files
        unset($validated['attachments'], $validated['delivery_attachments']);
        $this->claimService->updateClaim($claim->id, $validated);
        return [$claim, route('claims.index')];
    }

    private function normalizeClaimPayload(array $payload, ?Claim $claim): array
    {
        $allowed = [
            'claim_number','invoice_count','month','hospital_id','department_id','claim_date','claim_value',
            'reviewer_name','electronic_invoice_no','entity_id','insurance_claim_number','notes',
            'delivery_date','branch','location','beneficiary','claim_description','electronic_invoice_date',
        ];
        $data = [];
        foreach ($allowed as $k) {
            if (array_key_exists($k, $payload)) $data[$k] = $payload[$k];
        }
        // remove draft meta keys
        unset($data['_claim_id'], $data['draft_uuid']);
        return $data;
    }

    private function claimRules(array $data, ?Claim $claim): array
    {
        $hospitalId = $data['hospital_id'] ?? null;
        $isWaitingListHospital = $hospitalId && !is_numeric($hospitalId);
        $claimId = $claim?->id;

        $rules = [
            'claim_number' => ['required','string','max:255', $claimId ? Rule::unique('claims','claim_number')->ignore($claimId) : Rule::unique('claims','claim_number')],
            'invoice_count' => 'required|numeric|min:1',
            'month' => 'required|string',
            'claim_date' => 'required|date',
            'claim_value' => 'required|numeric|min:0',
            'reviewer_name' => 'nullable|string|max:255',
            'electronic_invoice_no' => ['nullable','string','max:255'],
            'entity_id' => 'required|exists:claim_entities,id',
            'insurance_claim_number' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'delivery_date' => 'nullable|date',
            'branch' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'beneficiary' => 'nullable|string|max:255',
            'claim_description' => 'nullable|string',
            'electronic_invoice_date' => 'nullable|date',
        ];
        if ($isWaitingListHospital) {
            $rules['hospital_id'] = 'required|string';
            $rules['department_id'] = 'required|string';
        } else {
            $rules['hospital_id'] = 'required|exists:hospitals,id';
            $rules['department_id'] = 'required|exists:departments,id';
        }
        return $rules;
    }
}
