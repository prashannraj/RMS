<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterDivision extends Model
{
    use HasFactory;

    protected $fillable = [
        'division_name',
        'remarks',
        'master_division_type',
        'parent_id',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    // ─── Relationships ──────────────────────────────────────────────────
    public function parent()
    {
        return $this->belongsTo(MasterDivision::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(MasterDivision::class, 'parent_id');
    }


}
