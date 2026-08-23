<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CandidateSamuhaBarga extends Model
{
    use HasFactory;

    protected $fillable = [
        'candidate_id',
        'samuha_barga_id',
        'candidate_samuha_barga_doc',
        'candidate_samuha_barga_other',
    ];

    protected function casts(): array
    {
        return [];
    }

    // ─── Relationships ──────────────────────────────────────────────────
    public function candidate()
    {
        return $this->belongsTo(Candidate::class, 'candidate_id');
    }

    public function samuhaBarga()
    {
        return $this->belongsTo(SamuhaBarga::class, 'samuha_barga_id');
    }


}
