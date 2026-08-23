<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\DocumentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DocumentController extends Controller
{
    public function __construct(private DocumentService $service) {}

    public function index(Request $request): JsonResponse
    {
        $documents = $this->service->getPaginated(
            $request->only(['candidate_id', 'document_type', 'status']),
            (int) $request->query('per_page', 15)
        );

        return response()->json([
            'success' => true,
            'data' => $documents->items(),
            'meta' => [
                'current_page' => $documents->currentPage(),
                'last_page' => $documents->lastPage(),
                'per_page' => $documents->perPage(),
                'total' => $documents->total(),
            ],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'candidate_id' => ['required', 'integer', 'exists:candidates,id'],
            'document_type' => ['required', 'string', 'max:100'],
            'file' => ['required', 'file', 'max:10240'], // max 10 MB
        ]);

        try {
            $document = $this->service->upload((int) $data['candidate_id'], $data['document_type'], $request->file('file'));
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Document uploaded successfully.',
            'data' => $document,
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $this->service->findOrFail($id),
        ]);
    }

    public function download(int $id): Response
    {
        try {
            $file = $this->service->download($id);
        } catch (\Exception $e) {
            abort(404, $e->getMessage());
        }

        return response($file['content'], 200, [
            'Content-Type' => $file['mime_type'],
            'Content-Disposition' => 'attachment; filename="' . $file['original_name'] . '"',
        ]);
    }

    public function verify(int $id, Request $request): JsonResponse
    {
        $status = $request->validate([
            'status' => ['nullable', 'in:verified,rejected'],
        ])['status'] ?? 'verified';

        try {
            $document = $this->service->verify($this->service->findOrFail($id), (int) auth()->id(), $status);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }

        return response()->json([
            'success' => true,
            'message' => "Document {$status} successfully.",
            'data' => $document,
        ]);
    }
}