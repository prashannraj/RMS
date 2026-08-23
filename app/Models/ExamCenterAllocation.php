<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamCenterAllocation extends Model
{
    use HasFactory;

    protected $fillable = [
        'advertisement_code_id',
        'exam_center_id',
        'allocated_count',
        'room_allocation_finalised',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'room_allocation_finalised' => 'boolean',
            'is_active' => 'boolean',
            'allocated_count' => 'integer',
        ];
    }

    // ─── Relationships ──────────────────────────────────────────────────
    public function advertisementCode()
    {
        return $this->belongsTo(AdvertisementCode::class, 'advertisement_code_id');
    }

    public function examCenter()
    {
        return $this->belongsTo(ExamCenter::class, 'exam_center_id');
    }

    public function rooms()
    {
        return $this->hasMany(Room::class, 'exam_center_allocation_id');
    }

    public function invigilatorAllocations()
    {
        return $this->hasMany(InvigilatorAllocation::class, 'exam_center_allocation_id');
    }


}
