<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\ApplicationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApplicationController extends Controller
{
    public function __construct(private ApplicationService $service) {}

    public function index(Request $request): JsonResponse
    {
        $applications = $this->service->getPaginated(
            $request->only(['advertisement_code', 'payment_status', 'result_status', 'candidate_id', 'search']),
            (int) $request->query('per_page', 15)
        );

        return response()->json([
            'success' => true,
            'data' => $applications->items(),
            'meta' => [
                'current_page' => $applications->currentPage(),
                'last_page' => $applications->lastPage(),
                'per_page' => $applications->perPage(),
                'total' => $applications->total(),
            ],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'advertisement_code' => ['required', 'string', 'max:50'],
            'advertisement_number' => ['nullable', 'string', 'max:50'],
            'total_fee' => ['nullable', 'numeric', 'min:0'],
        ]);

        // Candidates apply for themselves; admins may pass candidate_id
        $candidateId = (int) ($request->input('candidate_id') ?: auth()->user()?->candidate?->id ?? 0);

        if ($candidateId <= 0 && !auth()->user()?->hasRole('admin')) {
            return response()->json([
                'success' => false,
                'message' => 'Candidate profile is required before applying.',
            ], 422);
        }

        $application = $this->service->create($candidateId, $data);

        return response()->json([
            'success' => true,
            'message' => 'Application created successfully.',
            'data' => $application,
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $this->service->findOrFail($id),
        ]);
    }

    public function update(int $id, Request $request): JsonResponse
    {
        $data = $request->validate([
            'advertisement_code' => ['sometimes', 'string', 'max:50'],
            'advertisement_number' => ['nullable', 'string', 'max:50'],
            'total_fee' => ['nullable', 'numeric', 'min:0'],
            'remarks' => ['nullable', 'string'],
        ]);

        try {
            $application = $this->service->update($this->service->findOrFail($id), $data);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Application updated successfully.',
            'data' => $application,
        ]);
    }

    public function submit(int $id): JsonResponse
    {
        try {
            $application = $this->service->submit($this->service->findOrFail($id));
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Application submitted successfully.',
            'data' => $application,
        ]);
    }

    public function verify(int $id): JsonResponse
    {
        try {
            $application = $this->service->verify($this->service->findOrFail($id), (int) auth()->id());
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Application verified successfully.',
            'data' => $application,
        ]);
    }

    public function status(int $id): JsonResponse
    {
        $application = $this->service->findOrFail($id);
        $latest = $application->statusHistory()->latest('changed_at')->first();

        return response()->json([
            'success' => true,
            'data' => [
                'application_id' => $application->id,
                'payment_status' => $application->payment_status,
                'result_status' => $application->result_status,
                'submitted_at' => $application->submitted_at,
                'verified_at' => $application->verified_at,
                'latest_history' => $latest,
            ],
        ]);
    }

    public function history(int $id): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $this->service->getStatusHistory($id),
        ]);
    }
}