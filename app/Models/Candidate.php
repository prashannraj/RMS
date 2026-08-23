<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Candidate extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'first_name',
        'first_name_nepali',
        'middle_name',
        'middle_name_nepali',
        'last_name',
        'last_name_nepali',
        'date_of_birth_ad',
        'date_of_birth_bs',
        'citizenship_no',
        'national_id',
        'district_id',
        'issued_date',
        'citizenship_issued_date_bs',
        'gender',
        'candidate_photograph',
        'candidate_signature',
        'candidate_citizenship',
        'candidate_citizenship_backside',
        'husband_wife_first_name',
        'husband_wife_middle_name',
        'husband_wife_last_name',
        'father_first_name',
        'father_middle_name',
        'father_last_name',
        'grand_father_first_name',
        'grand_father_middle_name',
        'grand_father_last_name',
        'mother_first_name',
        'mother_middle_name',
        'mother_last_name',
        'created_date',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth_ad' => 'date',
            'is_active' => 'boolean',
        ];
    }

    // ─── Relationships ──────────────────────────────────────────────────
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function issueDistrict()
    {
        return $this->belongsTo(District::class, 'district_id');
    }

    public function address()
    {
        return $this->hasOne(CandidateAddress::class, 'candidate_id');
    }

    public function extraDetails()
    {
        return $this->hasOne(CandidateExtraDetail::class, 'candidate_id');
    }

    public function educationDetails()
    {
        return $this->hasMany(CandidateEducationDetail::class, 'candidate_id');
    }

    public function samuhaBargas()
    {
        return $this->hasMany(CandidateSamuhaBarga::class, 'candidate_id');
    }

    public function applications()
    {
        return $this->hasMany(Application::class, 'candidate_id');
    }

    public function candidateExams()
    {
        return $this->hasMany(CandidateExam::class, 'candidate_id');
    }

    public function documents()
    {
        return $this->hasMany(Document::class, 'candidate_id');
    }

    public function admitCards()
    {
        return $this->hasMany(AdmitCard::class, 'candidate_id');
    }


}
