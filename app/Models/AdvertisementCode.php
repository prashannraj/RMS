<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdvertisementCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'advertisement_code',
        'advertisement_published_date_en',
        'advertisement_published_date_np',
        'block_range_firstcode',
        'block_range_secondcode',
        'master_curri_id',
        'last_date_for_submission',
        'last_date_for_submission_np',
        'lifecycle_status',
        'memorandum_number',
        'payment_last_date_en',
        'payment_last_date_np',
        'pending_at',
        'postexam_status',
        'question_selection_status',
        'requesting_office_id',
        'status_of_lastdate_of_submission',
        'exam_scheduling_status',
        'remarks',
        'cut_off_date',
        'cut_off_date_np',
        'scrutiny_status',
        'double_fee_last_date_en',
        'double_fee_last_date_np',
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
        return $this->belongsTo(MasterDataCurriculum::class, 'master_curri_id');
    }

    public function requestingOffice()
    {
        return $this->belongsTo(Organization::class, 'requesting_office_id');
    }

    public function examCenterAllocations()
    {
        return $this->hasMany(ExamCenterAllocation::class, 'advertisement_code_id');
    }

    public function candidateExams()
    {
        return $this->hasMany(CandidateExam::class, 'advertisement_code');
    }


}
