<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;

    protected $fillable = [
        'group_name_np',
        'group_name_en',
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
        return $this->hasMany(PostCombination::class, 'group_id');
    }

    public function masterCurriculums()
    {
        return $this->hasMany(MasterDataCurriculum::class, 'master_division_id');
    }


}
