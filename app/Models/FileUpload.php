<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FileUpload extends Model
{
    use HasFactory;

    protected $fillable = [
        'document_id',
        'disk',
        'path',
        'original_name',
        'stored_name',
        'mime_type',
        'size',
        'checksum',
        'visibility',
    ];

    protected function casts(): array
    {
        return [
            'size' => 'integer',
        ];
    }

    // ─── Relationships ──────────────────────────────────────────────────
    public function document()
    {
        return $this->belongsTo(Document::class, 'document_id');
    }


}
