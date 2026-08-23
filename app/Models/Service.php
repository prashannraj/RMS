<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_name_np',
        'service_name_en',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    // ─── Relationships ──────────────────────────────────────────────────
    public function postCombinations()
    {
        return $this->hasMany(PostCombination::class, 'service_id');
    }

    public function postCombination()
    {
        return $this->hasOne(PostCombination::class, 'service_id');
    }


}
