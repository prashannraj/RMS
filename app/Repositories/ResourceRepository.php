<?php

namespace App\Repositories;

use App\Enums\ResourceStatus;
use App\Models\Resource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

class ResourceRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct(new Resource());
    }

    public function getPaginated(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->query()
            ->with(['category', 'user'])
            ->withCount('records');

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        if (!empty($filters['priority'])) {
            $query->where('priority', $filters['priority']);
        }

        if (!empty($filters['search'])) {
            $query->where(function (Builder $q) use ($filters) {
                $q->where('name', 'like', "%{$filters['search']}%")
                  ->orWhere('code', 'like', "%{$filters['search']}%")
                  ->orWhere('description', 'like', "%{$filters['search']}%")
                  ->orWhere('serial_number', 'like', "%{$filters['search']}%");
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

    public function findWithRelations(int $id): Resource
    {
        return $this->query()
            ->with(['category', 'user', 'records.creator'])
            ->withCount('records')
            ->findOrFail($id);
    }

    public function findByUuidWithRelations(string $uuid): Resource
    {
        return $this->query()
            ->with(['category', 'user'])
            ->where('uuid', $uuid)
            ->firstOrFail();
    }

    public function active(): Builder
    {
        return $this->query()->where('status', ResourceStatus::ACTIVE);
    }
}