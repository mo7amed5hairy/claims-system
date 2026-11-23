<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('backup:project')
    ->dailyAt((string) config('project_backup.daily_at', '02:30'))
    ->withoutOverlapping()
    ->onOneServer()
    ->when(fn () => (bool) config('project_backup.enabled', false));
