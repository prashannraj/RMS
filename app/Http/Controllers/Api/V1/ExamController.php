<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\ExamService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ExamController extends Controller
{
    public function __construct(private ExamService $service) {}

    // ─── Exams ───
    public function index(Request $request): JsonResponse
    {
        $exams = $this->service->getPaginatedExams(
            $request->only(['status', 'exam_date']),
            (int) $request->query('per_page', 15)
        );

        return response()->json([
            'success' => true,
            'data' => $exams->items(),
            'meta' => [
                'current_page' => $exams->currentPage(),
                'last_page' => $exams->lastPage(),
                'per_page' => $exams->perPage(),
                'total' => $exams->total(),
            ],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'category' => ['nullable', 'string', 'max:100'],
            'starttime' => ['nullable', 'string', 'max:20'],
            'endtime' => ['nullable', 'string', 'max:20'],
            'exam_date' => ['required', 'date'],
            'paper_id' => ['nullable', 'integer', 'exists:papers,id'],
            'requisition_id' => ['nullable', 'integer', 'exists:requisitions,id'],
        ]);

        $exam = \App\Models\ExamScheduling::create([...$data, 'status' => 'scheduled']);

        return response()->json([
            'success' => true,
            'message' => 'Exam scheduled successfully.',
            'data' => $exam,
        ], 201);
    }

    // ─── Exam Centers ───
    public function centersIndex(Request $request): JsonResponse
    {
        $centers = $this->service->getPaginatedCenters(
            $request->only(['state_id', 'district_id', 'search']),
            (int) $request->query('per_page', 15)
        );

        return response()->json([
            'success' => true,
            'data' => $centers->items(),
            'meta' => [
                'current_page' => $centers->currentPage(),
                'last_page' => $centers->lastPage(),
                'per_page' => $centers->perPage(),
                'total' => $centers->total(),
            ],
        ]);
    }

    public function centersStore(Request $request): JsonResponse
    {
        $data = $request->validate([
            'exam_center_name_np' => ['required', 'string', 'max:255', 'unique:exam_centers,exam_center_name_np'],
            'exam_center_name_en' => ['nullable', 'string', 'max:255'],
            'state_id' => ['required', 'integer', 'exists:states,id'],
            'district_id' => ['required', 'integer', 'exists:districts,id'],
            'address' => ['required', 'string', 'max:255'],
            'contact_person_name_np' => ['required', 'string', 'max:150'],
            'contact_person_name_en' => ['nullable', 'string', 'max:150'],
            'contact_number' => ['required', 'string', 'max:30'],
            'contact_email' => ['required', 'email', 'max:150'],
            'center_capacity' => ['required', 'integer', 'min:1'],
        ]);

        $center = $this->service->createCenter($data);

        return response()->json([
            'success' => true,
            'message' => 'Exam center created successfully.',
            'data' => $center,
        ], 201);
    }

    public function centersUpdate(int $id, Request $request): JsonResponse
    {
        $center = \App\Models\ExamCenter::findOrFail($id);

        $data = $request->validate([
            'exam_center_name_en' => ['sometimes', 'string', 'max:255'],
            'address' => ['sometimes', 'string', 'max:255'],
            'contact_person_name_en' => ['nullable', 'string', 'max:150'],
            'contact_number' => ['sometimes', 'string', 'max:30'],
            'contact_email' => ['sometimes', 'email', 'max:150'],
            'center_capacity' => ['sometimes', 'integer', 'min:1'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $updated = $this->service->updateCenter($center, $data);

        return response()->json([
            'success' => true,
            'message' => 'Exam center updated successfully.',
            'data' => $updated,
        ]);
    }

    // ─── Exam Candidates ───
    public function candidates(int $examId, Request $request): JsonResponse
    {
        $candidates = $this->service->getCandidatesForExam(
            $examId,
            $request->only(['attendance_status', 'result_status']),
            (int) $request->query('per_page', 15)
        );

        return response()->json([
            'success' => true,
            'data' => $candidates->items(),
            'meta' => [
                'current_page' => $candidates->currentPage(),
                'last_page' => $candidates->lastPage(),
                'per_page' => $candidates->perPage(),
                'total' => $candidates->total(),
            ],
        ]);
    }

    // ─── Allocation ───
    public function allocate(int $advertisementCodeId, Request $request): JsonResponse
    {
        $data = $request->validate([
            'candidate_ids' => ['required', 'array', 'min:1'],
            'candidate_ids.*' => ['integer', 'exists:candidates,id'],
        ]);

        try {
            $count = $this->service->allocateCandidates($advertisementCodeId, $data['candidate_ids']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }

        return response()->json([
            'success' => true,
            'message' => "{$count} candidate(s) allocated to exam centers.",
            'data' => ['allocated_count' => $count],
        ]);
    }

    // ─── Admit Cards ───
    public function admitCardsIndex(Request $request): JsonResponse
    {
        $admitCards = $this->service->getPaginatedAdmitCards(
            $request->only(['advt_code', 'status']),
            (int) $request->query('per_page', 15)
        );

        return response()->json([
            'success' => true,
            'data' => $admitCards->items(),
            'meta' => [
                'current_page' => $admitCards->currentPage(),
                'last_page' => $admitCards->lastPage(),
                'per_page' => $admitCards->perPage(),
                'total' => $admitCards->total(),
            ],
        ]);
    }

    public function admitCardsIssue(Request $request): JsonResponse
    {
        $data = $request->validate([
            'candidate_exam_id' => ['required', 'integer', 'exists:candidate_exams,id'],
        ]);

        try {
            $admitCard = $this->service->issueAdmitCard((int) $data['candidate_exam_id'], (int) auth()->id());
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Admit card issued successfully.',
            'data' => $admitCard,
        ], 201);
    }
}