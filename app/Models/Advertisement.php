<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Advertisement extends Model
{
    use HasFactory;

    protected $fillable = [
        'advertisementcode',
        'advertisementnumber',
        'quota_id',
        'status',
        'vacancy',
        'requisition_id',
        'published_date_en',
        'published_date_np',
        'application_start_at',
        'application_end_at',
        'double_fee_deadline_at',
        'description',
        'description_np',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'vacancy' => 'integer',
        ];
    }

    // ─── Relationships ──────────────────────────────────────────────────
    public function quota()
    {
        return $this->belongsTo(Quota::class);
    }

    public function requisition()
    {
        return $this->belongsTo(Requisition::class, 'requisition_id');
    }


}
