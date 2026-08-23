// File: rms-backend/app/Http/Controllers/Api/ResourceController.php
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreResourceRequest;
use App\Http\Requests\UpdateResourceRequest;
use App\Http\Resources\ResourceResource;
use App\Services\ResourceService;
use Illuminate\Http\JsonResponse;

class ResourceController extends Controller
{
    public function __construct(private ResourceService $service) {}

    public function index(): JsonResponse
    {
        $resources = $this->service->getPaginated(
            request()->only(['status', 'category_id', 'search']),
            request('per_page', 15)
        );

        return response()->json([
            'success' => true,
            'data' => ResourceResource::collection($resources),
            'meta' => [
                'current_page' => $resources->currentPage(),
                'last_page' => $resources->lastPage(),
                'per_page' => $resources->perPage(),
                'total' => $resources->total(),
            ],
        ]);
    }

    public function store(StoreResourceRequest $request): JsonResponse
    {
        $resource = $this->service->create($request->validated(), auth()->id());

        return response()->json([
            'success' => true,
            'data' => new ResourceResource($resource),
            'message' => 'Resource created successfully.',
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        $resource = $this->service->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => new ResourceResource($resource),
        ]);
    }

    public function update(UpdateResourceRequest $request, int $id): JsonResponse
    {
        $resource = $this->service->findOrFail($id);
        $updated = $this->service->update($resource, $request->validated(), auth()->id());

        return response()->json([
            'success' => true,
            'data' => new ResourceResource($updated),
            'message' => 'Resource updated successfully.',
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $resource = $this->service->findOrFail($id);
        $this->service->delete($resource, auth()->id());

        return response()->json([
            'success' => true,
            'message' => 'Resource deleted successfully.',
        ]);
    }
}