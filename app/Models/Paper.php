<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Paper extends Model
{
    use HasFactory;

    protected $fillable = [
        'curriculum_id',
        'paper_code',
        'paper_name',
        'paper_type',
        'subject_id',
        'fullmarks',
        'minimum_passing_percentage',
        'duration',
        'exam_option',
        'test_type',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    // ─── Relationships ──────────────────────────────────────────────────
    public function masterCurriculum()
    {
        return $this->belongsTo(MasterDataCurriculum::class, 'curriculum_id');
    }

    public function examSchedulings()
    {
        return $this->hasMany(ExamScheduling::class, 'paper_id');
    }


}
