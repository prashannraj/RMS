<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SamuhaBarga extends Model
{
    use HasFactory;

    protected $fillable = [
        'samuha_barga_name',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    // ─── Relationships ──────────────────────────────────────────────────
    public function candidateSamuhaBargas()
    {
        return $this->hasMany(CandidateSamuhaBarga::class, 'samuha_barga_id');
    }


}
