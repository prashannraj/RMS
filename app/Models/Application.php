<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    use HasFactory;

    protected $fillable = [
        'advertisement_code',
        'advertisement_number',
        'candidate_id',
        'deposited_fee',
        'total_fee',
        'payment_status',
        'result_status',
        'remarks',
        'submitted_at',
        'verified_at',
        'verified_by',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'deposited_fee' => 'decimal:2',
            'total_fee' => 'decimal:2',
            'submitted_at' => 'datetime',
            'verified_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    // ─── Relationships ──────────────────────────────────────────────────
    public function candidate()
    {
        return $this->belongsTo(Candidate::class, 'candidate_id');
    }

    public function verifiedBy()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function challans()
    {
        return $this->hasMany(Challan::class, 'application_id');
    }

    public function statusHistory()
    {
        return $this->hasMany(ApplicationStatusHistory::class, 'application_id');
    }


}
