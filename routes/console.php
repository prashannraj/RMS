<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// ─── Scheduled Jobs ───

// Send reminders for records pending approval (daily at 8:00 AM)
Schedule::command('records:send-approval-reminders --days=3')
    ->dailyAt('08:00')
    ->timezone(config('app.timezone'))
    ->withoutOverlapping()
    ->onOneServer();

// Archive expired records and purge old archived records (daily at 1:00 AM)
Schedule::command('records:cleanup-expired --days=365')
    ->dailyAt('01:00')
    ->timezone(config('app.timezone'))
    ->withoutOverlapping()
    ->onOneServer();

// Clear stale dashboard/stats cache every 6 hours
Schedule::command('cache:prune-stale')
    ->everySixHours()
    ->withoutOverlapping()
    ->onOneServer();

// Prune stale database queue jobs and failed jobs (weekly on Sunday at 2:00 AM)
Schedule::command('queue:prune-batches --hours=48')->weeklyOn(0, '02:00');
Schedule::command('queue:prune-failed --hours=720')->weeklyOn(0, '02:10');

// Prune old cache entries from database cache store (daily at 2:30 AM)
Schedule::command('cache:prune-stale-tags')->dailyAt('02:30');