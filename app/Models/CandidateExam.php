<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CandidateExam extends Model
{
    use HasFactory;

    protected $fillable = [
        'advertisement_code',
        'candidate_id',
        'application_number',
        'age_on_last_date_day',
        'age_on_last_date_month',
        'age_on_last_date_year',
        'examination_center_id',
        'roll_no',
        'room_allocated_id',
        'attendance_status',
        'payment_status',
        'result_status',
        'exam_results_status',
        'interview_date',
        'interview_marks_allocation_status',
        'interview_scheduled_status',
        'marks_obtained',
        'total_marks',
        'scrutiny_status',
        'remarks',
    ];

    protected function casts(): array
    {
        return [
            'marks_obtained' => 'decimal:2',
            'total_marks' => 'decimal:2',
        ];
    }

    // ─── Relationships ──────────────────────────────────────────────────
    public function candidate()
    {
        return $this->belongsTo(Candidate::class, 'candidate_id');
    }

    public function room()
    {
        return $this->belongsTo(Room::class, 'room_allocated_id');
    }

    public function admitCards()
    {
        return $this->hasMany(AdmitCard::class, 'candidate_exam_id');
    }


}
