<?php

namespace App\Models;

use App\Enums\ResourceStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Resource extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'user_id',
        'category_id',
        'name',
        'description',
        'code',
        'status',
        'priority',
        'cost',
        'location',
        'serial_number',
        'purchase_date',
        'warranty_expiry',
        'metadata',
        'tags',
    ];

    protected function casts(): array
    {
        return [
            'status' => ResourceStatus::class,
            'cost' => 'decimal:2',
            'purchase_date' => 'date',
            'warranty_expiry' => 'date',
            'metadata' => 'array',
            'tags' => 'array',
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($resource) {
            if (empty($resource->uuid)) {
                $resource->uuid = Str::uuid()->toString();
            }
            if (empty($resource->code)) {
                $resource->code = 'RES-' . strtoupper(Str::random(8));
            }
        });
    }

    // ─── Relationships ───
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function records()
    {
        return $this->hasMany(Record::class);
    }

    // ─── Scopes ───
    public function scopeActive($query)
    {
        return $query->where('status', ResourceStatus::ACTIVE);
    }

    public function scopeByCategory($query, int $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    public function scopeSearch($query, ?string $term)
    {
        if ($term) {
            $query->where(function ($q) use ($term) {
                $q->where('name', 'like', "%{$term}%")
                  ->orWhere('code', 'like', "%{$term}%")
                  ->orWhere('description', 'like', "%{$term}%")
                  ->orWhere('serial_number', 'like', "%{$term}%");
            });
        }
        return $query;
    }
}