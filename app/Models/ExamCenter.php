<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamCenter extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_center_name_np',
        'exam_center_name_en',
        'state_id',
        'district_id',
        'address',
        'contact_person_name_np',
        'contact_person_name_en',
        'contact_number',
        'contact_email',
        'center_capacity',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'center_capacity' => 'integer',
        ];
    }

    // ─── Relationships ──────────────────────────────────────────────────
    public function state()
    {
        return $this->belongsTo(State::class, 'state_id');
    }

    public function district()
    {
        return $this->belongsTo(District::class, 'district_id');
    }

    public function allocations()
    {
        return $this->hasMany(ExamCenterAllocation::class, 'exam_center_id');
    }


}
