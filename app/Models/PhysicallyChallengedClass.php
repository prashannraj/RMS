<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PhysicallyChallengedClass extends Model
{
    use HasFactory;

    protected $fillable = [
        'class_name_np',
        'class_name_en',
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
        return $this->hasMany(CandidateExtraDetail::class, 'physically_challenged_class_id');
    }


}
