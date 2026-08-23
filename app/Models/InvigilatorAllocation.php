<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvigilatorAllocation extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_center_allocation_id',
        'invigilator_post_id',
        'invigilator_id',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    // ─── Relationships ──────────────────────────────────────────────────
    public function examCenterAllocation()
    {
        return $this->belongsTo(ExamCenterAllocation::class, 'exam_center_allocation_id');
    }

    public function invigilatorPost()
    {
        return $this->belongsTo(InvigilatorPost::class, 'invigilator_post_id');
    }

    public function invigilator()
    {
        return $this->belongsTo(Invigilator::class, 'invigilator_id');
    }


}
