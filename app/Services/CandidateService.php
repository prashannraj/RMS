<?php

namespace App\Services;

use App\Models\Candidate;
use App\Models\CandidateAddress;
use App\Models\CandidateEducationDetail;
use App\Models\CandidateExtraDetail;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class CandidateService
{
    public function getPaginated(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Candidate::query()->with(['user', 'issueDistrict']);

        if (!empty($filters['search'])) {
            $term = $filters['search'];
            $query->where(function ($q) use ($term) {
                $q->where('first_name', 'like', "%{$term}%")
                  ->orWhere('last_name', 'like', "%{$term}%")
                  ->orWhere('citizenship_no', 'like', "%{$term}%")
                  ->orWhere('national_id', 'like', "%{$term}%");
            });
        }

        if (!empty($filters['gender'])) {
            $query->where('gender', $filters['gender']);
        }

        if (!empty($filters['district_id'])) {
            $query->where('district_id', $filters['district_id']);
        }

        return $query->orderByDesc('created_date')->paginate($perPage);
    }

    public function getProfile(int $userId): ?Candidate
    {
        // Auto-create an empty profile so new users don't get 404s.
        $this->ensureProfileExists($userId);

        return Candidate::with([
            'user',
            'issueDistrict',
            'address.district',
            'address.localBody',
            'address.state',
            'extraDetails.caste',
            'extraDetails.religion',
            'extraDetails.motherTongue',
            'educationDetails',
        ])->where('user_id', $userId)->first();
    }

    /**
     * Create a placeholder candidate record for the user if none exists.
     * The full name is derived from the linked user account.
     */
    public function ensureProfileExists(int $userId): Candidate
    {
        return Candidate::firstOrCreate(
            ['user_id' => $userId],
            [
                'first_name' => $this->guessFirstName($userId),
                'last_name' => $this->guessLastName($userId),
                'date_of_birth_ad' => '2000-01-01',
                'date_of_birth_bs' => '2056-09-17',
                'citizenship_no' => '',
                'gender' => '',
                'is_active' => true,
            ]
        );
    }

    private function guessFirstName(int $userId): string
    {
        $name = optional(User::find($userId))->name ?? 'New';
        $parts = preg_split('/\s+/', trim($name));

        return $parts[0] ?: 'New';
    }

    private function guessLastName(int $userId): string
    {
        $name = optional(User::find($userId))->name ?? 'Candidate';
        $parts = preg_split('/\s+/', trim($name));

        return count($parts) > 1 ? end($parts) : 'Candidate';
    }

    public function findOrFail(int $id): Candidate
    {
        return Candidate::with(['user', 'issueDistrict', 'address', 'extraDetails', 'educationDetails'])
            ->findOrFail($id);
    }

    public function updateProfile(int $userId, array $data): Candidate
    {
        return DB::transaction(function () use ($userId, $data) {
            $candidate = Candidate::firstOrCreate(
                ['user_id' => $userId],
                ['is_active' => true]
            );

            $candidate->update($data);

            return $candidate->fresh(['user', 'issueDistrict']);
        });
    }

    public function updateOrCreateAddress(int $candidateId, array $data): CandidateAddress
    {
        return DB::transaction(function () use ($candidateId, $data) {
            $address = CandidateAddress::updateOrCreate(
                ['candidate_id' => $candidateId],
                [...$data, 'candidate_id' => $candidateId]
            );

            return $address->fresh(['district', 'localBody', 'state']);
        });
    }

    public function updateOrCreateExtraDetails(int $candidateId, array $data): CandidateExtraDetail
    {
        return DB::transaction(function () use ($candidateId, $data) {
            $details = CandidateExtraDetail::updateOrCreate(
                ['candidate_id' => $candidateId],
                [...$data, 'candidate_id' => $candidateId]
            );

            return $details->fresh(['caste', 'religion', 'motherTongue', 'physicallyChallengedClass']);
        });
    }

    public function addEducation(int $candidateId, array $data): CandidateEducationDetail
    {
        return CandidateEducationDetail::create([...$data, 'candidate_id' => $candidateId]);
    }

    public function updateEducation(int $id, array $data): CandidateEducationDetail
    {
        $education = CandidateEducationDetail::findOrFail($id);
        $education->update($data);

        return $education->fresh();
    }

    public function deleteEducation(int $id): bool
    {
        return CandidateEducationDetail::findOrFail($id)->delete();
    }
}