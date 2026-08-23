<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LocalBody extends Model
{
    use HasFactory;

    protected $fillable = [
        'local_body_name_np',
        'local_body_name_en',
        'district_id',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    // ─── Relationships ──────────────────────────────────────────────────
    public function district()
    {
        return $this->belongsTo(District::class);
    }


}
