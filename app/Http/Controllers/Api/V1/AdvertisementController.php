<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\AdvertisementService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdvertisementController extends Controller
{
    public function __construct(private AdvertisementService $service) {}

    public function index(Request $request): JsonResponse
    {
        $advertisements = $this->service->getPaginated(
            $request->only(['status', 'search', 'open_only']),
            (int) $request->query('per_page', 15)
        );

        return response()->json([
            'success' => true,
            'data' => $advertisements->items(),
            'meta' => [
                'current_page' => $advertisements->currentPage(),
                'last_page' => $advertisements->lastPage(),
                'per_page' => $advertisements->perPage(),
                'total' => $advertisements->total(),
            ],
        ]);
    }

    public function show(int $id): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $this->service->findOrFail($id),
        ]);
    }

    public function vacancies(int $id): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Vacancies retrieved successfully.',
            'data' => $this->service->getVacancies($id),
        ]);
    }

    public function eligibility(int $id, Request $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $this->service->getEligibility($id, (int) auth()->id()),
        ]);
    }
}