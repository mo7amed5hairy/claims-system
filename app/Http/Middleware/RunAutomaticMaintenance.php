<?php

namespace App\Http\Middleware;

use App\Services\ProjectBackupService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class RunAutomaticMaintenance
{
    public function __construct(private ProjectBackupService $backup)
    {
    }

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (auth()->check() && $request->isMethod('GET') && ! $request->expectsJson()) {
            try {
                if ($this->backup->isDue() && \Illuminate\Support\Facades\Cache::add('project_backup:auto_lock', 1, 600)) {
                    defer(function () {
                        try {
                            $this->backup->runIfDue();
                        } catch (Throwable $e) {
                            Log::error('Automatic project backup failed: ' . $e->getMessage());
                        } finally {
                            \Illuminate\Support\Facades\Cache::forget('project_backup:auto_lock');
                        }
                    }, 'project-backup');
                }
            } catch (Throwable $e) {
                Log::error('Automatic maintenance middleware failed: ' . $e->getMessage());
            }
        }

        return $response;
    }
}
