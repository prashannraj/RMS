<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CandidateEducationDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'candidate_id',
        'university_or_board_name',
        'level',
        'faculty',
        'percentage',
        'major_subject',
        'description',
        'education_type',
        'passed_date_ad',
        'passed_date_bs',
        'transcript',
        'character_certificate',
        'equivalent_certificate',
        'division',
    ];

    protected function casts(): array
    {
        return [
            'percentage' => 'decimal:2',
            'passed_date_ad' => 'date',
        ];
    }

    // ─── Relationships ──────────────────────────────────────────────────
    public function candidate()
    {
        return $this->belongsTo(Candidate::class, 'candidate_id');
    }


}
