<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostCombination extends Model
{
    use HasFactory;

    protected $fillable = [
        'post_id',
        'service_id',
        'group_id',
        'sub_group_id',
    ];

    protected function casts(): array
    {
        return [];
    }

    // ─── Relationships ──────────────────────────────────────────────────
    public function post()
    {
        return $this->belongsTo(Post::class, 'post_id');
    }

    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    public function group()
    {
        return $this->belongsTo(Group::class, 'group_id');
    }

    public function subGroup()
    {
        return $this->belongsTo(SubGroup::class, 'sub_group_id');
    }


}
