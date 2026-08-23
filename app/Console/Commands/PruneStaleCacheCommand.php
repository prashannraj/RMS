<?php

namespace App\Console\Commands;

use App\Jobs\PruneStaleCache;
use Illuminate\Console\Command;

class PruneStaleCacheCommand extends Command
{
    protected $signature = 'cache:prune-stale';

    protected $description = 'Clear stale application cache keys (dashboard stats, charts, etc.)';

    public function handle(): int
    {
        $this->info('Dispatching stale cache prune job...');

        PruneStaleCache::dispatch();

        $this->info('Cache prune job dispatched successfully.');

        return self::SUCCESS;
    }
}