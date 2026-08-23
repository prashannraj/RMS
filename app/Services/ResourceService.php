<?php

namespace App\Services;

use App\Models\Resource;
use App\Events\ResourceCreated;
use App\Events\ResourceUpdated;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ResourceService
{
    public function getPaginated(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Resource::query()
            ->with(['category', 'user'])
            ->withCount('records');

        // Apply filters
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['category_id'])) {
            $query->byCategory($filters['category_id']);
        }

        if (!empty($filters['priority'])) {
            $query->where('priority', $filters['priority']);
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

        // Sorting
        $sortField = $filters['sort_by'] ?? 'created_at';
        $sortOrder = $filters['sort_order'] ?? 'desc';
        $query->orderBy($sortField, $sortOrder);

        return $query->paginate($perPage);
    }

    public function create(array $data, int $userId): Resource
    {
        return DB::transaction(function () use ($data, $userId) {
            $resource = Resource::create([
                ...$data,
                'user_id' => $userId,
            ]);

            event(new ResourceCreated($resource));

            $this->clearCache();

            return $resource->load('category', 'user');
        });
    }

    public function findOrFail(int $id): Resource
    {
        return Resource::with(['category', 'user', 'records.creator'])
            ->withCount('records')
            ->findOrFail($id);
    }

    public function findByUuid(string $uuid): Resource
    {
        return Resource::with(['category', 'user'])
            ->where('uuid', $uuid)
            ->firstOrFail();
    }

    public function update(Resource $resource, array $data): Resource
    {
        return DB::transaction(function () use ($resource, $data) {
            $resource->update($data);

            event(new ResourceUpdated($resource));

            $this->clearCache();

            return $resource->fresh(['category', 'user']);
        });
    }

    public function delete(Resource $resource): bool
    {
        $result = $resource->delete();
        $this->clearCache();
        return $result;
    }

    public function getStats(): array
    {
        return Cache::remember('resource_stats', 300, function () {
            return [
                'total' => Resource::count(),
                'active' => Resource::active()->count(),
                'maintenance' => Resource::where('status', 'maintenance')->count(),
                'retired' => Resource::where('status', 'retired')->count(),
                'total_cost' => Resource::sum('cost'),
                'by_category' => Resource::selectRaw('category_id, count(*) as count')
                    ->groupBy('category_id')
                    ->with('category:id,name')
                    ->get(),
            ];
        });
    }

    private function clearCache(): void
    {
        Cache::forget('resource_stats');
    }
}