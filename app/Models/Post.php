<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'post_name',
        'remarks',
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
        return $this->hasMany(PostCombination::class, 'post_id');
    }

    public function masterConfigurations()
    {
        return $this->hasMany(MasterConfiguration::class, 'post_id');
    }

    public function masterCurriculums()
    {
        return $this->hasMany(MasterDataCurriculum::class, 'post_id');
    }

    public function classCurriculums()
    {
        return $this->hasMany(MasterDataCurriculum::class, 'class_id');
    }


}
