<?php

namespace App\Events;

use App\Models\Record;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RecordApproved
{
    use Dispatchable, SerializesModels;

    public function __construct(public Record $record) {}
}