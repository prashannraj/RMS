<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Requisition extends Model
{
    use HasFactory;

    protected $fillable = [
        'fiscal_year',
        'requesting_office_id',
        'master_id',
        'demand_office',
        'requisition_remarks',
        'remarks_for_cancellation',
        'work_summary',
        'requested_date',
        'total_vacancy',
        'letter_url',
        'status',
        'distribution_flag',
        'exam_scheduling_flag',
        'exam_scheduling_remarks',
        'exam_skippable',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'requested_date' => 'datetime',
            'exam_skippable' => 'boolean',
            'is_active' => 'boolean',
            'total_vacancy' => 'integer',
        ];
    }

    // ─── Relationships ──────────────────────────────────────────────────
    public function requestingOffice()
    {
        return $this->belongsTo(Organization::class, 'requesting_office_id');
    }

    public function masterCurriculum()
    {
        return $this->belongsTo(MasterDataCurriculum::class, 'master_id');
    }

    public function advertisements()
    {
        return $this->hasMany(Advertisement::class, 'requisition_id');
    }

    public function examSchedulings()
    {
        return $this->hasMany(ExamScheduling::class, 'requisition_id');
    }


}
