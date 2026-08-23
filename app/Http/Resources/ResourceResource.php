<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ResourceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'name' => $this->name,
            'description' => $this->description,
            'code' => $this->code,
            'status' => $this->status->value,
            'status_label' => $this->status->label(),
            'priority' => $this->priority,
            'cost' => (float) $this->cost,
            'location' => $this->location,
            'serial_number' => $this->serial_number,
            'purchase_date' => $this->purchase_date?->toDateString(),
            'warranty_expiry' => $this->warranty_expiry?->toDateString(),
            'metadata' => $this->metadata,
            'tags' => $this->tags,
            'category' => new CategoryResource($this->whenLoaded('category')),
            'user' => new UserResource($this->whenLoaded('user')),
            'records_count' => $this->whenCounted('records'),
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
        ];
    }
}