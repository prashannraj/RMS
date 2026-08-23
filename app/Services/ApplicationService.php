<?php

namespace App\Services;

use App\Models\Application;
use App\Models\ApplicationStatusHistory;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class ApplicationService
{
    public function getPaginated(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Application::query()->with(['candidate', 'verifiedBy']);

        if (!empty($filters['advertisement_code'])) {
            $query->where('advertisement_code', $filters['advertisement_code']);
        }

        if (!empty($filters['payment_status'])) {
            $query->where('payment_status', $filters['payment_status']);
        }

        if (!empty($filters['result_status'])) {
            $query->where('result_status', $filters['result_status']);
        }

        if (!empty($filters['candidate_id'])) {
            $query->where('candidate_id', $filters['candidate_id']);
        }

        if (!empty($filters['search'])) {
            $term = $filters['search'];
            $query->where(function ($q) use ($term) {
                $q->where('advertisement_number', 'like', "%{$term}%")
                  ->orWhere('advertisement_code', 'like', "%{$term}%");
            });
        }

        return $query->orderByDesc('id')->paginate($perPage);
    }

    public function findOrFail(int $id): Application
    {
        return Application::with([
            'candidate',
            'verifiedBy',
            'challans.verifiedBy',
            'statusHistory.changedBy',
        ])->findOrFail($id);
    }

    public function create(int $candidateId, array $data): Application
    {
        return DB::transaction(function () use ($candidateId, $data) {
            $application = Application::create([
                ...$data,
                'candidate_id' => $candidateId,
                'payment_status' => $data['payment_status'] ?? 'unpaid',
                'is_active' => true,
            ]);

            ApplicationStatusHistory::create([
                'application_id' => $application->id,
                'from_status' => null,
                'to_status' => 'draft',
                'reason' => 'Application created.',
                'changed_by' => auth()->id(),
                'changed_at' => now(),
            ]);

            return $application;
        });
    }

    public function update(Application $application, array $data): Application
    {
        if (in_array($application->payment_status, ['paid', 'verified'], true)) {
            throw new \Exception('Paid applications cannot be modified.');
        }

        $application->update($data);

        return $application->fresh();
    }

    public function submit(Application $application): Application
    {
        if ($application->submitted_at !== null) {
            throw new \Exception('Application has already been submitted.');
        }

        return DB::transaction(function () use ($application) {
            $fromStatus = $this->currentStatus($application);
            $application->update([
                'submitted_at' => now(),
                'payment_status' => $application->payment_status ?? 'unpaid',
            ]);

            ApplicationStatusHistory::create([
                'application_id' => $application->id,
                'from_status' => $fromStatus,
                'to_status' => 'submitted',
                'reason' => 'Application submitted by candidate.',
                'changed_by' => auth()->id(),
                'changed_at' => now(),
            ]);

            return $application->fresh();
        });
    }

    public function verify(Application $application, int $verifierId): Application
    {
        if ($application->verified_at !== null) {
            throw new \Exception('Application has already been verified.');
        }

        return DB::transaction(function () use ($application, $verifierId) {
            $fromStatus = $this->currentStatus($application);
            $application->update([
                'verified_at' => now(),
                'verified_by' => $verifierId,
            ]);

            ApplicationStatusHistory::create([
                'application_id' => $application->id,
                'from_status' => $fromStatus,
                'to_status' => 'verified',
                'reason' => 'Application verified.',
                'changed_by' => $verifierId,
                'changed_at' => now(),
            ]);

            return $application->fresh(['verifiedBy']);
        });
    }

    public function getStatusHistory(int $applicationId)
    {
        return ApplicationStatusHistory::with('changedBy')
            ->where('application_id', $applicationId)
            ->orderBy('changed_at')
            ->get();
    }

    private function currentStatus(Application $application): ?string
    {
        return $application->statusHistory()->latest('changed_at')->value('to_status');
    }
}