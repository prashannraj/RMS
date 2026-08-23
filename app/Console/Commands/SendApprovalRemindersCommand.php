<?php

namespace App\Console\Commands;

use App\Jobs\SendPendingApprovalReminders;
use Illuminate\Console\Command;

class SendApprovalRemindersCommand extends Command
{
    protected $signature = 'records:send-approval-reminders {--days=3 : Remind for records pending longer than this many days}';

    protected $description = 'Send reminders for records pending approval';

    public function handle(): int
    {
        $days = (int) $this->option('days');

        $this->info("Dispatching approval reminders (pending longer than {$days} days)...");

        SendPendingApprovalReminders::dispatch($days);

        $this->info('Reminder job dispatched successfully.');

        return self::SUCCESS;
    }
}