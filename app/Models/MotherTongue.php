<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MotherTongue extends Model
{
    use HasFactory;

    protected $fillable = [
        'mother_tongue_name',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    // ─── Relationships ──────────────────────────────────────────────────
    public function candidateExtraDetails()
    {
        return $this->hasMany(CandidateExtraDetail::class, 'mother_tongue_id');
    }


}
