<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\CandidateService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CandidateController extends Controller
{
    public function __construct(private CandidateService $service) {}

    public function index(Request $request): JsonResponse
    {
        $candidates = $this->service->getPaginated(
            $request->only(['search', 'gender', 'district_id']),
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

    public function profile(): JsonResponse
    {
        $candidate = $this->service->getProfile((int) auth()->id());

        if (!$candidate) {
            return response()->json([
                'success' => false,
                'message' => 'Candidate profile not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Candidate profile retrieved successfully.',
            'data' => $candidate,
        ]);
    }

    public function updateProfile(Request $request): JsonResponse
    {
        $data = $request->validate([
            'first_name' => ['sometimes', 'string', 'max:100'],
            'first_name_nepali' => ['nullable', 'string', 'max:100'],
            'middle_name' => ['nullable', 'string', 'max:100'],
            'middle_name_nepali' => ['nullable', 'string', 'max:100'],
            'last_name' => ['sometimes', 'string', 'max:100'],
            'last_name_nepali' => ['nullable', 'string', 'max:100'],
            'date_of_birth_ad' => ['nullable', 'date'],
            'date_of_birth_bs' => ['nullable', 'string', 'max:20'],
            'citizenship_no' => ['nullable', 'string', 'max:50'],
            'national_id' => ['nullable', 'string', 'max:50'],
            'issued_date' => ['nullable', 'date'],
            'gender' => ['nullable', 'in:male,female,other'],
        ]);

        $candidate = $this->service->updateProfile((int) auth()->id(), $data);

        return response()->json([
            'success' => true,
            'message' => 'Candidate profile updated successfully.',
            'data' => $candidate,
        ]);
    }

    public function show(int $id): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $this->service->findOrFail($id),
        ]);
    }

    public function address(int $id, Request $request): JsonResponse
    {
        $candidate = $this->service->findOrFail($id);

        if ($request->isMethod('get')) {
            return response()->json([
                'success' => true,
                'data' => $candidate->address()->with(['district', 'localBody', 'state'])->first(),
            ]);
        }

        $data = $request->validate([
            'ward_no' => ['nullable', 'string', 'max:10'],
            'tole_name' => ['nullable', 'string', 'max:150'],
            'marga' => ['nullable', 'string', 'max:150'],
            'house_no' => ['nullable', 'string', 'max:50'],
            'phone_no' => ['nullable', 'string', 'max:20'],
            'mobile_no' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:150'],
            'mailing_address' => ['nullable', 'string', 'max:255'],
            'district_id' => ['nullable', 'integer', 'exists:districts,id'],
            'local_body_id' => ['nullable', 'integer', 'exists:local_bodies,id'],
            'state_id' => ['nullable', 'integer', 'exists:states,id'],
        ]);

        $address = $this->service->updateOrCreateAddress($candidate->id, $data);

        return response()->json([
            'success' => true,
            'message' => 'Candidate address saved successfully.',
            'data' => $address,
        ]);
    }

    public function extraDetails(int $id, Request $request): JsonResponse
    {
        $candidate = $this->service->findOrFail($id);

        if ($request->isMethod('get')) {
            return response()->json([
                'success' => true,
                'data' => $candidate->extraDetails()->with(['caste', 'religion', 'motherTongue'])->first(),
            ]);
        }

        $data = $request->validate([
            'marital_status' => ['nullable', 'in:single,married,widowed,divorced'],
            'physically_challenged' => ['nullable', 'boolean'],
            'physically_challenged_description' => ['nullable', 'string', 'max:255'],
            'father_education' => ['nullable', 'string', 'max:150'],
            'mother_education' => ['nullable', 'string', 'max:150'],
            'father_occupation' => ['nullable', 'string', 'max:150'],
            'mother_occupation' => ['nullable', 'string', 'max:150'],
            'area' => ['nullable', 'in:urban,rural'],
            'caste_id' => ['nullable', 'integer', 'exists:castes,id'],
            'mother_tongue_id' => ['nullable', 'integer', 'exists:mother_tongues,id'],
            'religion_id' => ['nullable', 'integer', 'exists:religions,id'],
            'employment_status' => ['nullable', 'string', 'max:100'],
        ]);

        $details = $this->service->updateOrCreateExtraDetails($candidate->id, $data);

        return response()->json([
            'success' => true,
            'message' => 'Candidate extra details saved successfully.',
            'data' => $details,
        ]);
    }

    public function educationIndex(int $id): JsonResponse
    {
        $candidate = $this->service->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $candidate->educationDetails()->orderByDesc('passed_date_ad')->get(),
        ]);
    }

    public function educationStore(int $id, Request $request): JsonResponse
    {
        $data = $request->validate([
            'university_or_board_name' => ['required', 'string', 'max:255'],
            'level' => ['required', 'string', 'max:100'],
            'faculty' => ['nullable', 'string', 'max:150'],
            'percentage' => ['nullable', 'numeric', 'between:0,100'],
            'major_subject' => ['nullable', 'string', 'max:150'],
            'description' => ['nullable', 'string'],
            'education_type' => ['nullable', 'string', 'max:100'],
            'passed_date_ad' => ['nullable', 'date'],
            'passed_date_bs' => ['nullable', 'string', 'max:20'],
            'division' => ['nullable', 'string', 'max:50'],
        ]);

        $education = $this->service->addEducation($id, $data);

        return response()->json([
            'success' => true,
            'message' => 'Education detail added successfully.',
            'data' => $education,
        ], 201);
    }

    public function educationUpdate(int $candidateId, int $educationId, Request $request): JsonResponse
    {
        $data = $request->validate([
            'university_or_board_name' => ['sometimes', 'string', 'max:255'],
            'level' => ['sometimes', 'string', 'max:100'],
            'faculty' => ['nullable', 'string', 'max:150'],
            'percentage' => ['nullable', 'numeric', 'between:0,100'],
            'major_subject' => ['nullable', 'string', 'max:150'],
            'passed_date_ad' => ['nullable', 'date'],
            'division' => ['nullable', 'string', 'max:50'],
        ]);

        $education = $this->service->updateEducation($educationId, $data);

        return response()->json([
            'success' => true,
            'message' => 'Education detail updated successfully.',
            'data' => $education,
        ]);
    }

    public function educationDestroy(int $candidateId, int $educationId): JsonResponse
    {
        $this->service->deleteEducation($educationId);

        return response()->json([
            'success' => true,
            'message' => 'Education detail deleted successfully.',
        ]);
    }
}