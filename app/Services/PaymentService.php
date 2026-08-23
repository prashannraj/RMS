<?php

namespace App\Services;

use App\Models\Application;
use App\Models\Challan;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class PaymentService
{
    public function getPaginated(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Challan::query()->with(['application.candidate', 'verifiedBy']);

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['advt_code'])) {
            $query->where('advt_code', $filters['advt_code']);
        }

        if (!empty($filters['search'])) {
            $term = $filters['search'];
            $query->where(function ($q) use ($term) {
                $q->where('voucher_no', 'like', "%{$term}%")
                  ->orWhere('name', 'like', "%{$term}%")
                  ->orWhere('username', 'like', "%{$term}%");
            });
        }

        return $query->orderByDesc('id')->paginate($perPage);
    }

    public function findOrFail(int $id): Challan
    {
        return Challan::with(['application.candidate', 'verifiedBy'])->findOrFail($id);
    }

    public function calculateFee(array $data): array
    {
        $baseFee = (float) ($data['base_fee'] ?? 500.00);
        $isDouble = !empty($data['double_fee']) && filter_var($data['double_fee'], FILTER_VALIDATE_BOOLEAN);

        return [
            'base_fee' => $baseFee,
            'double_fee_applied' => $isDouble,
            'total_fee' => $isDouble ? round($baseFee * 2, 2) : $baseFee,
            'currency' => 'NPR',
        ];
    }

    public function createChallan(int $applicationId, array $data): Challan
    {
        return DB::transaction(function () use ($applicationId, $data) {
            $application = Application::findOrFail($applicationId);

            return Challan::create([
                ...$data,
                'application_id' => $applicationId,
                'advt_code' => $data['advt_code'] ?? $application->advertisement_code,
                'status' => $data['status'] ?? 'pending',
            ]);
        });
    }

    public function markPaid(Challan $challan, array $data): Challan
    {
        return DB::transaction(function () use ($challan, $data) {
            $challan->update([
                'status' => 'paid',
                'paid_at' => now(),
                ...$data,
            ]);

            // Update application payment status when fully paid
            $application = $challan->application;
            if ($application && (float) $application->deposited_fee < (float) $application->total_fee) {
                $application->increment('deposited_fee', (float) $challan->amount);
            }
            if ($application && (float) $application->deposited_fee >= (float) $application->total_fee) {
                $application->update(['payment_status' => 'paid']);
            }

            return $challan->fresh(['application']);
        });
    }

    public function verify(Challan $challan, int $verifierId): Challan
    {
        if ($challan->verified_at !== null) {
            throw new \Exception('Challan has already been verified.');
        }

        return DB::transaction(function () use ($challan, $verifierId) {
            $challan->update([
                'status' => 'verified',
                'verified_at' => now(),
                'verified_by' => $verifierId,
            ]);

            $challan->application?->update(['payment_status' => 'verified']);

            return $challan->fresh(['application.candidate', 'verifiedBy']);
        });
    }

    public function applyDoubleFee(Application $application, float $amount): Challan
    {
        return DB::transaction(function () use ($application, $amount) {
            return Challan::create([
                'advt_code' => $application->advertisement_code,
                'amount' => $amount,
                'name' => $application->candidate?->first_name . ' ' . $application->candidate?->last_name,
                'office' => 'PPSC',
                'status' => 'pending',
                'application_id' => $application->id,
            ]);
        });
    }
}