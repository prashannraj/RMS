<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\MasterDataService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MasterDataController extends Controller
{
    public function __construct(private MasterDataService $service) {}

    public function index(string $type, Request $request): JsonResponse
    {
        try {
            $data = $this->service->get($type, $request->only(['search', 'is_active']));
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    public function configurations(Request $request): JsonResponse
    {
        $configurations = $this->service->getConfigurations(
            $request->only(['post_id', 'key']),
            (int) $request->query('per_page', 50)
        );

        return response()->json([
            'success' => true,
            'data' => $configurations->items(),
            'meta' => [
                'current_page' => $configurations->currentPage(),
                'last_page' => $configurations->lastPage(),
                'per_page' => $configurations->perPage(),
                'total' => $configurations->total(),
            ],
        ]);
    }
}