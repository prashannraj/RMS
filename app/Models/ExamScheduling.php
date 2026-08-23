<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamScheduling extends Model
{
    use HasFactory;

    protected $fillable = [
        'category',
        'starttime',
        'endtime',
        'exam_date',
        'paper_id',
        'status',
        'requisition_id',
    ];

    protected function casts(): array
    {
        return [
            'exam_date' => 'date',
        ];
    }

    // ─── Relationships ──────────────────────────────────────────────────
    public function paper()
    {
        return $this->belongsTo(Paper::class, 'paper_id');
    }

    public function requisition()
    {
        return $this->belongsTo(Requisition::class, 'requisition_id');
    }


}
