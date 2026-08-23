<?php

namespace App\Services;

use App\Models\Document;
use App\Models\FileUpload;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DocumentService
{
    public function getPaginated(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Document::query()->with(['candidate', 'verifiedBy', 'fileUploads']);

        if (!empty($filters['candidate_id'])) {
            $query->where('candidate_id', $filters['candidate_id']);
        }

        if (!empty($filters['document_type'])) {
            $query->where('document_type', $filters['document_type']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->orderByDesc('id')->paginate($perPage);
    }

    public function findOrFail(int $id): Document
    {
        return Document::with(['candidate', 'verifiedBy', 'fileUploads'])->findOrFail($id);
    }

    public function upload(int $candidateId, string $documentType, $file): Document
    {
        return DB::transaction(function () use ($candidateId, $documentType, $file) {
            $document = Document::create([
                'candidate_id' => $candidateId,
                'document_type' => $documentType,
                'status' => 'pending',
            ]);

            $storedName = uniqid() . '_' . preg_replace('/[^A-Za-z0-9._-]/', '_', $file->getClientOriginalName());
            $path = $file->storeAs("documents/{$candidateId}", $storedName, 'local');

            FileUpload::create([
                'document_id' => $document->id,
                'disk' => 'local',
                'path' => $path,
                'original_name' => $file->getClientOriginalName(),
                'stored_name' => $storedName,
                'mime_type' => $file->getClientMimeType(),
                'size' => $file->getSize(),
                'checksum' => hash_file('sha256', $file->getRealPath()),
                'visibility' => 'private',
            ]);

            return $document->fresh(['fileUploads']);
        });
    }

    public function download(int $documentId): array
    {
        $document = Document::with('fileUploads')->findOrFail($documentId);
        $upload = $document->fileUploads()->latest('id')->first();

        if (!$upload) {
            throw new \Exception('No file attached to this document.');
        }

        if (!Storage::disk($upload->disk)->exists($upload->path)) {
            throw new \Exception('File not found on disk.');
        }

        return [
            'content' => Storage::disk($upload->disk)->get($upload->path),
            'mime_type' => $upload->mime_type,
            'original_name' => $upload->original_name,
        ];
    }

    public function verify(Document $document, int $verifierId, ?string $status = 'verified'): Document
    {
        return DB::transaction(function () use ($document, $verifierId, $status) {
            $document->update([
                'status' => $status,
                'verified_at' => now(),
                'verified_by' => $verifierId,
            ]);

            return $document->fresh(['candidate', 'verifiedBy']);
        });
    }
}