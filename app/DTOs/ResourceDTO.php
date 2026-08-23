<?php

namespace App\DTOs;

class ResourceDTO extends BaseDTO
{
    public ?int $id = null;
    public ?string $uuid = null;
    public ?int $user_id = null;
    public ?int $category_id = null;
    public ?string $name = null;
    public ?string $description = null;
    public ?string $code = null;
    public ?string $status = null;
    public ?string $priority = null;
    public ?float $cost = null;
    public ?string $location = null;
    public ?string $serial_number = null;
    public ?string $purchase_date = null;
    public ?string $warranty_expiry = null;
    public ?array $metadata = null;
    public ?array $tags = null;
    public ?string $created_at = null;
    public ?string $updated_at = null;

    public static function fromModel(\App\Models\Resource $resource): self
    {
        return self::fromArray($resource->toArray());
    }
}