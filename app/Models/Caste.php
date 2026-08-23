<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Caste extends Model
{
    use HasFactory;

    protected $fillable = [
        'caste_name_np',
        'caste_name_en',
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
        return $this->hasMany(CandidateExtraDetail::class, 'caste_id');
    }


}
