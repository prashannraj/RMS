<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdmitCard extends Model
{
    use HasFactory;

    protected $fillable = [
        'candidate_exam_id',
        'candidate_id',
        'advt_code',
        'admit_card_number',
        'roll_number',
        'issued_at',
        'issued_by',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'issued_at' => 'datetime',
        ];
    }

    // ─── Relationships ──────────────────────────────────────────────────
    public function candidateExam()
    {
        return $this->belongsTo(CandidateExam::class, 'candidate_exam_id');
    }

    public function candidate()
    {
        return $this->belongsTo(Candidate::class, 'candidate_id');
    }

    public function issuedBy()
    {
        return $this->belongsTo(User::class, 'issued_by');
    }


}
