<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRecordRequest;
use App\Http\Requests\UpdateRecordRequest;
use App\Http\Resources\RecordResource;
use App\Services\RecordService;
use Illuminate\Http\JsonResponse;

class RecordController extends Controller
{
    public function __construct(private RecordService $service) {}

    public function index(): JsonResponse
    {
        $records = $this->service->getPaginated(
            request()->only(['type', 'status', 'resource_id', 'search']),
            request('per_page', 15)
        );

        return response()->json([
            'success' => true,
            'data' => RecordResource::collection($records),
            'meta' => [
                'current_page' => $records->currentPage(),
                'last_page' => $records->lastPage(),
                'per_page' => $records->perPage(),
                'total' => $records->total(),
            ],
        ]);
    }

    public function store(StoreRecordRequest $request): JsonResponse
    {
        $record = $this->service->create($request->validated(), auth()->id());

        return response()->json([
            'success' => true,
            'data' => new RecordResource($record),
            'message' => 'Record created successfully.',
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        $record = $this->service->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => new RecordResource($record),
        ]);
    }

    public function update(UpdateRecordRequest $request, int $id): JsonResponse
    {
        $record = $this->service->findOrFail($id);
        $updated = $this->service->update($record, $request->validated(), auth()->id());

        return response()->json([
            'success' => true,
            'data' => new RecordResource($updated),
            'message' => 'Record updated successfully.',
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $record = $this->service->findOrFail($id);
        $this->service->delete($record);

        return response()->json([
            'success' => true,
            'message' => 'Record deleted successfully.',
        ]);
    }

    public function approve(int $id): JsonResponse
    {
        $record = $this->service->findOrFail($id);
        $approved = $this->service->approve($record, auth()->id());

        return response()->json([
            'success' => true,
            'data' => new RecordResource($approved),
            'message' => 'Record approved.',
        ]);
    }

    public function reject(int $id): JsonResponse
    {
        $record = $this->service->findOrFail($id);
        $rejected = $this->service->reject($record, auth()->id());

        return response()->json([
            'success' => true,
            'data' => new RecordResource($rejected),
            'message' => 'Record rejected.',
        ]);
    }
}