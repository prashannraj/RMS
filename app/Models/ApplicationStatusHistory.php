<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApplicationStatusHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'application_id',
        'from_status',
        'to_status',
        'reason',
        'changed_by',
        'changed_at',
    ];

    protected function casts(): array
    {
        return [
            'changed_at' => 'datetime',
        ];
    }

    // ─── Relationships ──────────────────────────────────────────────────
    public function application()
    {
        return $this->belongsTo(Application::class, 'application_id');
    }

    public function changedBy()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }


}
