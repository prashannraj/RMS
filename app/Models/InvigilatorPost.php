<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvigilatorPost extends Model
{
    use HasFactory;

    protected $fillable = [
        'post_name_np',
        'post_name_en',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    // ─── Relationships ──────────────────────────────────────────────────
    public function invigilatorAllocations()
    {
        return $this->hasMany(InvigilatorAllocation::class, 'invigilator_post_id');
    }


}
