<?php

namespace App\Jobs;

use App\Models\Record;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class SendPendingApprovalReminders implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public int $olderThanDays = 3
    ) {}

    public function handle(): void
    {
        $cutoffDate = now()->subDays($this->olderThanDays);

        $pendingRecords = Record::where('status', 'pending')
            ->where('created_at', '<=', $cutoffDate)
            ->with('creator')
            ->get();

        foreach ($pendingRecords as $record) {
            // Notify the approvers/managers about pending records
            Log::info('Pending approval reminder', [
                'record_id' => $record->id,
                'title' => $record->title,
                'pending_since' => $record->created_at->toDateString(),
            ]);

            // TODO: Send notification to approvers once notification channels are configured
            // Notification::send($approvers, new PendingApprovalReminder($record));
        }

        Log::info('SendPendingApprovalReminders job completed.', [
            'reminders_sent' => $pendingRecords->count(),
        ]);
    }
}