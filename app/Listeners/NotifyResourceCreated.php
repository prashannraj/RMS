<?php

namespace App\Listeners;

use App\Events\ResourceCreated;
use App\Notifications\ResourceCreatedNotification;
use App\Models\User;

class NotifyResourceCreated
{
    public function handle(ResourceCreated $event): void
    {
        // Notify all admins and managers
        $managers = User::role(['admin', 'manager'])->get();

        foreach ($managers as $manager) {
            $manager->notify(new ResourceCreatedNotification($event->resource));
        }
    }
}