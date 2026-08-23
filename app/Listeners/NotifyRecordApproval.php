<?php

namespace App\Listeners;

use App\Events\RecordApproved;
use App\Notifications\RecordApprovedNotification;

class NotifyRecordApproval
{
    public function handle(RecordApproved $event): void
    {
        $creator = $event->record->creator;

        if ($creator) {
            $creator->notify(new RecordApprovedNotification($event->record));
        }
    }
}