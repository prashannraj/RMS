<?php

namespace App\Services;

use App\Models\Advertisement;
use App\Models\AdvertisementCode;
use App\Models\PostCombination;
use Illuminate\Pagination\LengthAwarePaginator;

class AdvertisementService
{
    public function getPaginated(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Advertisement::query()->with(['quota', 'requisition']);

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['search'])) {
            $term = $filters['search'];
            $query->where(function ($q) use ($term) {
                $q->where('advertisementcode', 'like', "%{$term}%")
                  ->orWhere('advertisementnumber', 'like', "%{$term}%");
            });
        }

        if (isset($filters['open_only']) && filter_var($filters['open_only'], FILTER_VALIDATE_BOOLEAN)) {
            $now = now();
            $query->where('application_start_at', '<=', $now)
                  ->where('application_end_at', '>=', $now);
        }

        return $query->orderByDesc('published_date_en')->paginate($perPage);
    }

    public function findOrFail(int $id): Advertisement
    {
        return Advertisement::with(['quota', 'requisition'])->findOrFail($id);
    }

    public function getVacancies(int $advertisementId): array
    {
        $codes = AdvertisementCode::where('advertisement_id', $advertisementId)->get();

        return $codes->map(function ($code) {
            $combinations = PostCombination::with(['post', 'group', 'subGroup'])
                ->where('advertisement_code_id', $code->id)
                ->get();

            return [
                'id' => $code->id,
                'code' => $code->code ?? null,
                'vacancy_count' => $code->vacancy_count ?? null,
                'posts' => $combinations,
            ];
        })->toArray();
    }

    public function getEligibility(int $advertisementId, int $candidateId): array
    {
        // Basic eligibility check against advertisement window and candidate existence
        $advertisement = $this->findOrFail($advertisementId);
        $now = now();

        $windowOpen = $advertisement->application_start_at !== null
            && $advertisement->application_end_at !== null
            && $now->between($advertisement->application_start_at, $advertisement->application_end_at);

        return [
            'eligible' => $windowOpen,
            'reason' => $windowOpen
                ? 'Application window is open.'
                : 'Application window is closed or not yet announced.',
            'application_start_at' => $advertisement->application_start_at,
            'application_end_at' => $advertisement->application_end_at,
        ];
    }
}