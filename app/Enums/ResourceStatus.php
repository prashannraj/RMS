<?php

namespace App\Enums;

enum ResourceStatus: string
{
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
    case MAINTENANCE = 'maintenance';
    case RETIRED = 'retired';

    public function label(): string
    {
        return match ($this) {
            self::ACTIVE => 'Active',
            self::INACTIVE => 'Inactive',
            self::MAINTENANCE => 'Under Maintenance',
            self::RETIRED => 'Retired',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::ACTIVE => 'green',
            self::INACTIVE => 'gray',
            self::MAINTENANCE => 'yellow',
            self::RETIRED => 'red',
        };
    }
}