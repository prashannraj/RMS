<?php

namespace App\Services;

use App\Enums\RecordStatus;
use App\Models\Record;
use App\Events\RecordApproved;
use App\Events\RecordRejected;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class RecordService
{
    public function getPaginated(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Record::query()
            ->with(['resource', 'creator', 'approver']);

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['type'])) {
            $query->byType($filters['type']);
        }

        if (!empty($filters['resource_id'])) {
            $query->where('resource_id', $filters['resource_id']);
        }

        if (!empty($filters['search'])) {
            $query->search($filters['search']);
        }

        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        $sortField = $filters['sort_by'] ?? 'created_at';
        $sortOrder = $filters['sort_order'] ?? 'desc';
        $query->orderBy($sortField, $sortOrder);

        return $query->paginate($perPage);
    }

    public function create(array $data, int $userId): Record
    {
        return DB::transaction(function () use ($data, $userId) {
            $record = Record::create([
                ...$data,
                'created_by' => $userId,
                'status' => $data['status'] ?? RecordStatus::DRAFT->value,
            ]);

            return $record->load('resource', 'creator');
        });
    }

    public function findOrFail(int $id): Record
    {
        return Record::with(['resource', 'creator', 'approver'])
            ->findOrFail($id);
    }

    public function update(Record $record, array $data): Record
    {
        // Only allow editing drafts
        if ($record->status !== RecordStatus::DRAFT) {
            throw new \Exception('Only draft records can be edited.');
        }

        $record->update($data);
        return $record->fresh(['resource', 'creator']);
    }

    public function submitForApproval(Record $record): Record
    {
        if ($record->status !== RecordStatus::DRAFT) {
            throw new \Exception('Only draft records can be submitted.');
        }

        $record->update(['status' => RecordStatus::PENDING]);
        return $record->fresh();
    }

    public function approve(Record $record, int $approverId): Record
    {
        if ($record->status !== RecordStatus::PENDING) {
            throw new \Exception('Only pending records can be approved.');
        }

        $record->update([
            'status' => RecordStatus::APPROVED,
            'approved_by' => $approverId,
            'approved_at' => now(),
        ]);

        event(new RecordApproved($record));

        return $record->fresh(['approver']);
    }

    public function reject(Record $record, int $approverId, string $reason): Record
    {
        if ($record->status !== RecordStatus::PENDING) {
            throw new \Exception('Only pending records can be rejected.');
        }

        $record->update([
            'status' => RecordStatus::REJECTED,
            'approved_by' => $approverId,
            'rejection_reason' => $reason,
        ]);

        event(new RecordRejected($record));

        return $record->fresh(['approver']);
    }

    public function delete(Record $record): bool
    {
        if (!in_array($record->status, [RecordStatus::DRAFT, RecordStatus::REJECTED])) {
            throw new \Exception('Only draft or rejected records can be deleted.');
        }

        return $record->delete();
    }
}