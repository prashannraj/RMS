<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Challan extends Model
{
    use HasFactory;

    protected $fillable = [
        'advt_code',
        'amount',
        'challan_date',
        'challan_time',
        'name',
        'office',
        'status',
        'username',
        'voucher_no',
        'application_id',
        'paid_at',
        'verified_at',
        'verified_by',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'challan_date' => 'date',
            'paid_at' => 'datetime',
            'verified_at' => 'datetime',
        ];
    }

    // ─── Relationships ──────────────────────────────────────────────────
    public function application()
    {
        return $this->belongsTo(Application::class, 'application_id');
    }

    public function verifiedBy()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }


}
