<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CandidateExtraDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'candidate_id',
        'religion_other',
        'marital_status',
        'physically_challenged',
        'physically_challenged_description',
        'father_education',
        'father_education_level',
        'mother_education',
        'mother_education_level',
        'father_occupation',
        'father_occupation_other',
        'mother_occupation',
        'mother_occupation_other',
        'area',
        'caste_id',
        'caste_other',
        'mother_tongue_id',
        'mother_tongue_other',
        'physically_challenged_class_id',
        'religion_id',
        'employment_status',
        'employment_status_other',
    ];

    protected function casts(): array
    {
        return [
            'physically_challenged' => 'boolean',
        ];
    }

    // ─── Relationships ──────────────────────────────────────────────────
    public function candidate()
    {
        return $this->belongsTo(Candidate::class, 'candidate_id');
    }

    public function caste()
    {
        return $this->belongsTo(Caste::class, 'caste_id');
    }

    public function religion()
    {
        return $this->belongsTo(Religion::class, 'religion_id');
    }

    public function motherTongue()
    {
        return $this->belongsTo(MotherTongue::class, 'mother_tongue_id');
    }

    public function physicallyChallengedClass()
    {
        return $this->belongsTo(PhysicallyChallengedClass::class, 'physically_challenged_class_id');
    }


}
