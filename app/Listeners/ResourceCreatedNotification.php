<?php

namespace App\Notifications;

use App\Models\Resource;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class ResourceCreatedNotification extends Notification
{
    use Queueable;

    public function __construct(private Resource $resource) {}

    public function via($notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Resource Created: ' . $this->resource->name)
            ->greeting('Hello ' . $notifiable->name)
            ->line('A new resource has been created in the system.')
            ->line('Resource: ' . $this->resource->name)
            ->line('Code: ' . $this->resource->code)
            ->action('View Resource', url('/resources/' . $this->resource->id))
            ->line('Thank you for using RMS!');
    }

    public function toArray($notifiable): array
    {
        return [
            'title' => 'New Resource Created',
            'message' => "Resource '{$this->resource->name}' ({$this->resource->code}) has been created.",
            'resource_id' => $this->resource->id,
            'type' => 'resource_created',
        ];
    }
}