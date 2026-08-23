<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class District extends Model
{
    use HasFactory;

    protected $fillable = [
        'district_name_np',
        'district_name_en',
        'state_id',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    // ─── Relationships ──────────────────────────────────────────────────
    public function state()
    {
        return $this->belongsTo(State::class);
    }

    public function localBodies()
    {
        return $this->hasMany(LocalBody::class);
    }

    public function candidates()
    {
        return $this->hasMany(Candidate::class, 'district_id');
    }


}
