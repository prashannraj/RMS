<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    use HasFactory;

    protected $fillable = [
        'parent_id',
        'organization_name_np',
        'organization_name_en',
        'organization_code',
        'can_schedule_exam',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'can_schedule_exam' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    // ─── Relationships ──────────────────────────────────────────────────
    public function parent()
    {
        return $this->belongsTo(Organization::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Organization::class, 'parent_id');
    }

    public function requisitions()
    {
        return $this->hasMany(Requisition::class, 'requesting_office_id');
    }

    public function masterConfigurations()
    {
        return $this->hasMany(MasterConfiguration::class, 'office_id');
    }

    public function advertisementCodes()
    {
        return $this->hasMany(AdvertisementCode::class, 'requesting_office_id');
    }


}
