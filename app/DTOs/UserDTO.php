<?php

namespace App\DTOs;

class UserDTO extends BaseDTO
{
    public ?int $id = null;
    public ?string $name = null;
    public ?string $email = null;
    public ?string $phone = null;
    public ?string $avatar = null;
    public ?string $address = null;
    public ?string $date_of_birth = null;
    public ?bool $is_active = null;
    public ?string $last_login_at = null;
    public ?string $last_login_ip = null;
    public ?array $roles = null;
    public ?string $created_at = null;
    public ?string $updated_at = null;

    public static function fromModel(\App\Models\User $user): self
    {
        $dto = self::fromArray($user->toArray());
        $dto->roles = $user->roles->pluck('name')->toArray();
        return $dto;
    }
}