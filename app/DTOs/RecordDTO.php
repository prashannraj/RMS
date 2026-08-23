<?php

namespace App\DTOs;

class RecordDTO extends BaseDTO
{
    public ?int $id = null;
    public ?string $uuid = null;
    public ?int $resource_id = null;
    public ?int $created_by = null;
    public ?int $approved_by = null;
    public ?string $title = null;
    public ?string $content = null;
    public ?string $type = null;
    public ?string $status = null;
    public ?float $amount = null;
    public ?string $effective_date = null;
    public ?string $expiry_date = null;
    public ?array $attachments = null;
    public ?array $tags = null;
    public ?string $rejection_reason = null;
    public ?string $approved_at = null;
    public ?string $created_at = null;
    public ?string $updated_at = null;

    public static function fromModel(\App\Models\Record $record): self
    {
        return self::fromArray($record->toArray());
    }
}