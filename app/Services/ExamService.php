<?php

namespace App\Services;

use App\Models\AdmitCard;
use App\Models\CandidateExam;
use App\Models\ExamCenter;
use App\Models\ExamScheduling;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class ExamService
{
    public function getPaginatedExams(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = ExamScheduling::query()->with(['paper', 'requisition']);

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['exam_date'])) {
            $query->whereDate('exam_date', $filters['exam_date']);
        }

        return $query->orderBy('exam_date')->paginate($perPage);
    }

    public function getPaginatedCenters(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = ExamCenter::query()->with(['state', 'district']);

        if (!empty($filters['state_id'])) {
            $query->where('state_id', $filters['state_id']);
        }

        if (!empty($filters['district_id'])) {
            $query->where('district_id', $filters['district_id']);
        }

        if (!empty($filters['search'])) {
            $term = $filters['search'];
            $query->where(function ($q) use ($term) {
                $q->where('exam_center_name_np', 'like', "%{$term}%")
                  ->orWhere('exam_center_name_en', 'like', "%{$term}%");
            });
        }

        return $query->orderBy('exam_center_name_en')->paginate($perPage);
    }

    public function createCenter(array $data): ExamCenter
    {
        return ExamCenter::create($data);
    }

    public function updateCenter(ExamCenter $center, array $data): ExamCenter
    {
        $center->update($data);

        return $center->fresh(['state', 'district']);
    }

    public function allocateCandidates(int $advertisementCodeId, array $candidateIds): int
    {
        return DB::transaction(function () use ($advertisementCodeId, $candidateIds) {
            // Distribute candidates across active centers round-robin by capacity
            $centers = ExamCenter::where('is_active', true)->orderBy('id')->get();

            if ($centers->isEmpty()) {
                throw new \Exception('No active exam centers available.');
            }

            $index = 0;
            foreach ($candidateIds as $candidateId) {
                $center = $centers[$index % $centers->count()];
                $index++;

                CandidateExam::updateOrCreate(
                    [
                        'candidate_id' => $candidateId,
                        'advertisement_code' => (string) $advertisementCodeId,
                    ],
                    ['examination_center_id' => (string) $center->id]
                );
            }

            return count($candidateIds);
        });
    }

    public function getCandidatesForExam(int $examId, array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $exam = ExamScheduling::findOrFail($examId);

        $query = CandidateExam::query()
            ->with(['candidate'])
            ->where('advertisement_code', (string) $exam->requisition_id);

        if (!empty($filters['attendance_status'])) {
            $query->where('attendance_status', $filters['attendance_status']);
        }

        if (!empty($filters['result_status'])) {
            $query->where('result_status', $filters['result_status']);
        }

        return $query->paginate($perPage);
    }

    public function issueAdmitCard(int $candidateExamId, int $issuedBy): AdmitCard
    {
        return DB::transaction(function () use ($candidateExamId, $issuedBy) {
            $candidateExam = CandidateExam::findOrFail($candidateExamId);

            $existing = AdmitCard::where('candidate_exam_id', $candidateExamId)
                ->where('status', 'issued')
                ->first();

            if ($existing) {
                throw new \Exception('Admit card already issued for this candidate exam.');
            }

            return AdmitCard::create([
                'candidate_exam_id' => $candidateExamId,
                'candidate_id' => $candidateExam->candidate_id,
                'advt_code' => $candidateExam->advertisement_code,
                'admit_card_number' => 'AC-' . strtoupper(uniqid()),
                'roll_number' => $candidateExam->roll_no,
                'issued_at' => now(),
                'issued_by' => $issuedBy,
                'status' => 'issued',
            ]);
        });
    }

    public function getPaginatedAdmitCards(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = AdmitCard::query()->with(['candidate', 'candidateExam']);

        if (!empty($filters['advt_code'])) {
            $query->where('advt_code', $filters['advt_code']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->orderByDesc('id')->paginate($perPage);
    }
}