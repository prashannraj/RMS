<?php

namespace App\Console\Commands;

use App\Jobs\CleanupExpiredRecords;
use Illuminate\Console\Command;

class CleanupExpiredRecordsCommand extends Command
{
    protected $signature = 'records:cleanup-expired {--days=365 : Days after which archived records are purged}';

    protected $description = 'Archive expired records and purge old archived records';

    public function handle(): int
    {
        $days = (int) $this->option('days');

        $this->info("Dispatching cleanup of expired records (older than {$days} days)...");

        CleanupExpiredRecords::dispatch($days);

        $this->info('Cleanup job dispatched successfully.');

        return self::SUCCESS;
    }
}