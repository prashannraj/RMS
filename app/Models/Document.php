<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;

    protected $fillable = [
        'candidate_id',
        'document_type',
        'status',
        'verified_at',
        'verified_by',
    ];

    protected function casts(): array
    {
        return [
            'verified_at' => 'datetime',
        ];
    }

    // ─── Relationships ──────────────────────────────────────────────────
    public function candidate()
    {
        return $this->belongsTo(Candidate::class, 'candidate_id');
    }

    public function verifiedBy()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function fileUploads()
    {
        return $this->hasMany(FileUpload::class, 'document_id');
    }


}
