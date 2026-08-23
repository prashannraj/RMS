<?php

namespace App\Services;

use App\Models\Report;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ReportService
{
    public function getPaginated(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Report::query()->with('user');

        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->orderByDesc('id')->paginate($perPage);
    }

    public function findOrFail(int $id): Report
    {
        return Report::with('user')->findOrFail($id);
    }

    /**
     * Generate a report synchronously and store the CSV output.
     */
    public function generate(int $userId, array $data): Report
    {
        return DB::transaction(function () use ($userId, $data) {
            $report = Report::create([
                'user_id' => $userId,
                'title' => $data['title'],
                'type' => $data['type'],
                'status' => 'processing',
                'filters' => $data['filters'] ?? [],
            ]);

            try {
                $rows = $this->buildRows($report->type, $report->filters ?? []);

                $fileName = "report_{$report->uuid}.csv";
                $filePath = "reports/{$fileName}";

                $csv = $this->toCsv($rows);
                Storage::disk('local')->put($filePath, $csv);

                $report->update([
                    'status' => 'completed',
                    'file_path' => $filePath,
                    'file_name' => $fileName,
                    'file_size' => strlen($csv),
                    'completed_at' => now(),
                ]);
            } catch (\Throwable $e) {
                $report->update([
                    'status' => 'failed',
                    'error_message' => $e->getMessage(),
                ]);
            }

            return $report->fresh('user');
        });
    }

    public function download(Report $report): array
    {
        if ($report->status !== 'completed' || !$report->file_path) {
            throw new \Exception('Report file is not available.');
        }

        if (!Storage::disk('local')->exists($report->file_path)) {
            throw new \Exception('Report file not found on disk.');
        }

        return [
            'content' => Storage::disk('local')->get($report->file_path),
            'mime_type' => 'text/csv',
            'file_name' => $report->file_name,
        ];
    }

    private function buildRows(string $type, array $filters): array
    {
        return match ($type) {
            'applications' => $this->applicationRows($filters),
            'candidates' => $this->candidateRows($filters),
            'payments' => $this->paymentRows($filters),
            default => throw new \InvalidArgumentException("Unknown report type: {$type}"),
        };
    }

    private function applicationRows(array $filters): array
    {
        $query = DB::table('applications')
            ->join('candidates', 'candidates.id', '=', 'applications.candidate_id')
            ->select(
                'applications.id',
                'applications.advertisement_code',
                DB::raw("CONCAT(candidates.first_name, ' ', candidates.last_name) AS candidate_name"),
                'applications.payment_status',
                'applications.result_status',
                'applications.submitted_at'
            );

        if (!empty($filters['advertisement_code'])) {
            $query->where('applications.advertisement_code', $filters['advertisement_code']);
        }

        return [['ID', 'Advt Code', 'Candidate', 'Payment Status', 'Result Status', 'Submitted At']]
            + $query->get()->map(fn ($r) => (array) $r)->toArray();
    }

    private function candidateRows(array $filters): array
    {
        $query = DB::table('candidates')
            ->select('id', 'first_name', 'last_name', 'gender', 'citizenship_no', 'date_of_birth_ad');

        if (!empty($filters['gender'])) {
            $query->where('gender', $filters['gender']);
        }

        return [['ID', 'First Name', 'Last Name', 'Gender', 'Citizenship No', 'DOB (AD)']]
            + $query->limit(5000)->get()->map(fn ($r) => (array) $r)->toArray();
    }

    private function paymentRows(array $filters): array
    {
        $query = DB::table('challans')
            ->select('id', 'advt_code', 'voucher_no', 'name', 'amount', 'status', 'paid_at');

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return [['ID', 'Advt Code', 'Voucher No', 'Name', 'Amount', 'Status', 'Paid At']]
            + $query->limit(5000)->get()->map(fn ($r) => (array) $r)->toArray();
    }

    private function toCsv(array $rows): string
    {
        $output = fopen('php://temp', 'r+');
        foreach ($rows as $row) {
            fputcsv($output, $row);
        }
        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);

        return $csv;
    }
}