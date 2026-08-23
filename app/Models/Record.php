<?php

namespace App\Models;

use App\Enums\RecordStatus;
use App\Enums\RecordType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Record extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'resource_id',
        'created_by',
        'approved_by',
        'title',
        'content',
        'type',
        'status',
        'amount',
        'effective_date',
        'expiry_date',
        'attachments',
        'tags',
        'rejection_reason',
        'approved_at',
    ];

    protected function casts(): array
    {
        return [
            'type' => RecordType::class,
            'status' => RecordStatus::class,
            'amount' => 'decimal:2',
            'effective_date' => 'date',
            'expiry_date' => 'date',
            'approved_at' => 'datetime',
            'attachments' => 'array',
            'tags' => 'array',
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($record) {
            if (empty($record->uuid)) {
                $record->uuid = Str::uuid()->toString();
            }
        });
    }

    // ─── Relationships ───
    public function resource()
    {
        return $this->belongsTo(Resource::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // ─── Scopes ───
    public function scopePending($query)
    {
        return $query->where('status', RecordStatus::PENDING);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', RecordStatus::APPROVED);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeSearch($query, ?string $term)
    {
        if ($term) {
            $query->where(function ($q) use ($term) {
                $q->where('title', 'like', "%{$term}%")
                  ->orWhere('content', 'like', "%{$term}%");
            });
        }
        return $query;
    }
}