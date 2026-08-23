<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RecordResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'title' => $this->title,
            'content' => $this->content,
            'type' => $this->type->value,
            'status' => $this->status->value,
            'status_label' => $this->status->label(),
            'amount' => $this->amount ? (float) $this->amount : null,
            'effective_date' => $this->effective_date?->toDateString(),
            'expiry_date' => $this->expiry_date?->toDateString(),
            'attachments' => $this->attachments,
            'tags' => $this->tags,
            'rejection_reason' => $this->rejection_reason,
            'approved_at' => $this->approved_at?->toDateTimeString(),
            'resource' => new ResourceResource($this->whenLoaded('resource')),
            'creator' => new UserResource($this->whenLoaded('creator')),
            'approver' => new UserResource($this->whenLoaded('approver')),
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
        ];
    }
}