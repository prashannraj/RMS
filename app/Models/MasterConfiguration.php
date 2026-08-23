<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterConfiguration extends Model
{
    use HasFactory;

    protected $fillable = [
        'post_id',
        'office_id',
        'config_key',
        'config_value',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    // ─── Relationships ──────────────────────────────────────────────────
    public function post()
    {
        return $this->belongsTo(Post::class, 'post_id');
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class, 'office_id');
    }


}
