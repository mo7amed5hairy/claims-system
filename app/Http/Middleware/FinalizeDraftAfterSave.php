<?php

namespace App\Http\Middleware;

use App\Models\Draft;
use App\Support\DraftClosure;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

/**
 * Auto-close draft on successful real save.
 * Client sends X-Draft-UUID header (fetch/AJAX) or draft_uuid input (normal form post).
 */
class FinalizeDraftAfterSave
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($request->isMethod('GET') || !auth()->check() || $request->is('drafts/*')) {
            return $response;
        }

        $uuid = (string) $request->header('X-Draft-UUID', '');
        if ($uuid === '' || !Str::isUuid($uuid)) {
            $uuid = (string) $request->input('draft_uuid', '');
        }
        if ($uuid === '' || !Str::isUuid($uuid)) {
            return $response;
        }

        $status = $response->getStatusCode();
        $isRedirect = $response->isRedirect();
        if ($status < 200 || ($status >= 300 && !$isRedirect)) {
            return $response;
        }

        if ($isRedirect) {
            $session = $request->hasSession() ? $request->session() : null;
            if ($session) {
                $errors = $session->get('errors');
                if ($errors && (is_object($errors) ? $errors->any() : !empty($errors))) {
                    return $response;
                }
                // If controller explicitly flashed an error and not a success, don't close.
                if ($session->has('error') && !$session->has('success')) {
                    return $response;
                }
            }
            // Only treat 302/303 (typical post-save redirects) as success.
            if (!in_array($status, [302, 303], true)) {
                return $response;
            }
        }

        if ($response instanceof JsonResponse) {
            $body = $response->getData(true);
            $isSuccess = ($body['ok'] ?? null) === true
                || ($body['success'] ?? null) === true
                || ($body['status'] ?? null) === 'success';
            $isExplicitFailure = ($body['ok'] ?? null) === false
                || ($body['success'] ?? null) === false
                || ($body['status'] ?? null) === 'error';
            if ($isExplicitFailure || isset($body['errors'])) {
                return $response;
            }
        }

        $userId = (int) auth()->id();

        DB::transaction(function () use ($userId, $uuid) {
            DraftClosure::close($userId, $uuid, 'saved');

            Draft::query()
                ->where('user_id', $userId)
                ->where('uuid', $uuid)
                ->where('status', Draft::STATUS_PENDING)
                ->delete();
        });

        return $response;
    }
}
