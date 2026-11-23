<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Force HTTPS in production
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        $this->app->terminating(function () {
            $this->maybeRunDailyBackup();
        });
    }

    private function maybeRunDailyBackup(): void
    {
        try {
            if ($this->app->runningInConsole()) return;
            if (!config('project_backup.enabled')) return;

            $now = now();
            $dailyAt = (string) config('project_backup.daily_at', '02:30');
            if (!preg_match('/^\d{2}:\d{2}$/', $dailyAt)) $dailyAt = '02:30';
            $dueAt = $now->copy()->setTimeFromTimeString($dailyAt);
            if ($now->lt($dueAt)) return;

            $cacheKey = 'project_backup_daily_ran_' . $now->toDateString();
            $lockKey = 'project_backup_daily_lock';
            if (\Illuminate\Support\Facades\Cache::has($cacheKey)) return;
            if (!\Illuminate\Support\Facades\Cache::add($lockKey, 1, 600)) return;

            \Illuminate\Support\Facades\Cache::put($cacheKey, true, $now->copy()->endOfDay());

            $php = PHP_BINARY ?: 'php';
            $artisan = base_path('artisan');
            $cmd = sprintf('"%s" "%s" backup:project --no-ansi', $php, $artisan);

            if (DIRECTORY_SEPARATOR === '\\') {
                $full = 'start "" /B ' . $cmd . ' > NUL 2>&1';
                @pclose(@popen($full, 'r'));
            } else {
                $full = $cmd . ' > /dev/null 2>&1 &';
                @exec($full);
            }
        } catch (\Throwable $e) {
            // Never break user requests because of backup scheduling
        }
    }
}
