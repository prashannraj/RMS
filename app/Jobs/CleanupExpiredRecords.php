<?php

namespace App\Jobs;

use App\Models\Record;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class CleanupExpiredRecords implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public int $olderThanDays = 365
    ) {}

    public function handle(): void
    {
        $cutoffDate = now()->subDays($this->olderThanDays);

        $count = Record::where('status', 'archived')
            ->where('updated_at', '<', $cutoffDate)
            ->onlyTrashed()
            ->forceDelete();

        // Also archive expired approved records
        $expired = Record::where('status', 'approved')
            ->whereNotNull('expiry_date')
            ->where('expiry_date', '<', now())
            ->update(['status' => 'archived']);

        Log::info('CleanupExpiredRecords job completed.', [
            'force_deleted' => $count,
            'archived_expired' => $expired,
        ]);
    }
}