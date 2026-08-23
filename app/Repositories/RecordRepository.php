<?php

namespace App\Repositories;

use App\Enums\RecordStatus;
use App\Models\Record;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

class RecordRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct(new Record());
    }

    public function getPaginated(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->query()
            ->with(['resource', 'creator', 'approver']);

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (!empty($filters['resource_id'])) {
            $query->where('resource_id', $filters['resource_id']);
        }

        if (!empty($filters['search'])) {
            $query->where(function (Builder $q) use ($filters) {
                $q->where('title', 'like', "%{$filters['search']}%")
                  ->orWhere('content', 'like', "%{$filters['search']}%");
            });
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

    public function findWithRelations(int $id): Record
    {
        return $this->query()
            ->with(['resource', 'creator', 'approver'])
            ->findOrFail($id);
    }

    public function pending(): Builder
    {
        return $this->query()->where('status', RecordStatus::PENDING);
    }

    public function approved(): Builder
    {
        return $this->query()->where('status', RecordStatus::APPROVED);
    }
}