<?php

namespace App\Events;

use App\Models\Resource;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ResourceCreated
{
    use Dispatchable, SerializesModels;

    public function __construct(public Resource $resource) {}
}