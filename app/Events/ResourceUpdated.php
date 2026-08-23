<?php

namespace App\Events;

use App\Models\Resource;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ResourceUpdated
{
    use Dispatchable, SerializesModels;

    public function __construct(public Resource $resource) {}
}