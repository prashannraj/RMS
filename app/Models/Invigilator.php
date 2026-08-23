<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invigilator extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_no',
        'first_name_np',
        'middle_name_np',
        'last_name_np',
        'first_name_en',
        'middle_name_en',
        'last_name_en',
        'mobile_no',
        'address',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    // ─── Relationships ──────────────────────────────────────────────────
    public function invigilatorAllocations()
    {
        return $this->hasMany(InvigilatorAllocation::class, 'invigilator_id');
    }


}
