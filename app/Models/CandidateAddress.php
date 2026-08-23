<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CandidateAddress extends Model
{
    use HasFactory;

    protected $fillable = [
        'candidate_id',
        'ward_no',
        'tole_name',
        'marga',
        'house_no',
        'phone_no',
        'mobile_no',
        'email',
        'mailing_address',
        'district_id',
        'local_body_id',
        'state_id',
    ];

    protected function casts(): array
    {
        return [];
    }

    // ─── Relationships ──────────────────────────────────────────────────
    public function candidate()
    {
        return $this->belongsTo(Candidate::class, 'candidate_id');
    }

    public function district()
    {
        return $this->belongsTo(District::class, 'district_id');
    }

    public function localBody()
    {
        return $this->belongsTo(LocalBody::class, 'local_body_id');
    }

    public function state()
    {
        return $this->belongsTo(State::class, 'state_id');
    }


}
