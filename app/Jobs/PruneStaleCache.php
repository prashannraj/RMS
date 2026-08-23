<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class PruneStaleCache implements ShouldQueue
{
    use Queueable;

    public function handle(): void
    {
        $keys = [
            'dashboard_stats',
            'dashboard_charts',
            'resource_stats',
        ];

        foreach ($keys as $key) {
            Cache::forget($key);
        }

        Log::info('PruneStaleCache job completed.', [
            'cleared_keys' => $keys,
        ]);
    }
}