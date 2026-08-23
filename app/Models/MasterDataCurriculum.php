<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterDataCurriculum extends Model
{
    use HasFactory;

    protected $fillable = [
        'class_id',
        'master_division_id',
        'post_id',
        'post_type',
        'is_active',
        'category',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    // ─── Relationships ──────────────────────────────────────────────────
    public function class()
    {
        return $this->belongsTo(Post::class, 'class_id');
    }

    public function group()
    {
        return $this->belongsTo(Group::class, 'master_division_id');
    }

    public function post()
    {
        return $this->belongsTo(Post::class, 'post_id');
    }

    public function requisitions()
    {
        return $this->hasMany(Requisition::class, 'master_id');
    }

    public function papers()
    {
        return $this->hasMany(Paper::class, 'curriculum_id');
    }

    public function advertisementCodes()
    {
        return $this->hasMany(AdvertisementCode::class, 'master_curri_id');
    }


}
