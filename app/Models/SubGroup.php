<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'sub_group_name_np',
        'sub_group_name_en',
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
        return $this->hasMany(PostCombination::class, 'sub_group_id');
    }


}
