<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'avatar',
        'address',
        'date_of_birth',
        'is_active',
        'last_login_at',
        'last_login_ip',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'date_of_birth' => 'date',
            'last_login_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    // ─── Relationships ───
    public function candidate()
    {
        return $this->hasOne(Candidate::class);
    }

    public function resources()
    {
        return $this->hasMany(Resource::class);
    }

    public function records()
    {
        return $this->hasMany(Record::class, 'created_by');
    }

    public function approvedRecords()
    {
        return $this->hasMany(Record::class, 'approved_by');
    }

    public function reports()
    {
        return $this->hasMany(Report::class);
    }

    // ─── Scopes ───
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // ─── Helpers ───
    public function updateLastLogin(string $ip): void
    {
        $this->update([
            'last_login_at' => now(),
            'last_login_ip' => $ip,
        ]);
    }
}