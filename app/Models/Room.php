<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_center_allocation_id',
        'room_no',
        'allocated_count',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'allocated_count' => 'integer',
        ];
    }

    // ─── Relationships ──────────────────────────────────────────────────
    public function examCenterAllocation()
    {
        return $this->belongsTo(ExamCenterAllocation::class, 'exam_center_allocation_id');
    }

    public function candidateExams()
    {
        return $this->hasMany(CandidateExam::class, 'room_allocated_id');
    }


}
