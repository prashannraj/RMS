<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function __construct(private PaymentService $service) {}

    public function index(Request $request): JsonResponse
    {
        $payments = $this->service->getPaginated(
            $request->only(['status', 'advt_code', 'search']),
            (int) $request->query('per_page', 15)
        );

        return response()->json([
            'success' => true,
            'data' => $payments->items(),
            'meta' => [
                'current_page' => $payments->currentPage(),
                'last_page' => $payments->lastPage(),
                'per_page' => $payments->perPage(),
                'total' => $payments->total(),
            ],
        ]);
    }

    public function calculate(Request $request): JsonResponse
    {
        $data = $request->validate([
            'base_fee' => ['nullable', 'numeric', 'min:0'],
            'double_fee' => ['nullable', 'boolean'],
        ]);

        return response()->json([
            'success' => true,
            'data' => $this->service->calculateFee($data),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'application_id' => ['required', 'integer', 'exists:applications,id'],
            'amount' => ['required', 'numeric', 'min:0'],
            'name' => ['nullable', 'string', 'max:150'],
            'office' => ['nullable', 'string', 'max:150'],
        ]);

        $challan = $this->service->createChallan((int) $data['application_id'], $data);

        return response()->json([
            'success' => true,
            'message' => 'Payment challan created successfully.',
            'data' => $challan,
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $this->service->findOrFail($id),
        ]);
    }

    public function voucher(int $id): JsonResponse
    {
        $challan = $this->service->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => [
                'voucher_no' => $challan->voucher_no,
                'challan_date' => $challan->challan_date,
                'challan_time' => $challan->challan_time,
                'amount' => $challan->amount,
                'name' => $challan->name,
                'office' => $challan->office,
                'status' => $challan->status,
                'advt_code' => $challan->advt_code,
            ],
        ]);
    }

    public function transaction(int $id): JsonResponse
    {
        $challan = $this->service->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => [
                'challan_id' => $challan->id,
                'voucher_no' => $challan->voucher_no,
                'username' => $challan->username,
                'amount' => $challan->amount,
                'paid_at' => $challan->paid_at,
                'verified_at' => $challan->verified_at,
                'verified_by' => $challan->verifiedBy?->name,
                'status' => $challan->status,
            ],
        ]);
    }

    public function markPaid(int $id, Request $request): JsonResponse
    {
        $data = $request->validate([
            'voucher_no' => ['nullable', 'string', 'max:100'],
            'challan_date' => ['nullable', 'date'],
            'challan_time' => ['nullable', 'string', 'max:20'],
            'username' => ['nullable', 'string', 'max:100'],
        ]);

        try {
            $challan = $this->service->markPaid($this->service->findOrFail($id), $data);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Payment recorded successfully.',
            'data' => $challan,
        ]);
    }

    public function verify(int $id): JsonResponse
    {
        try {
            $challan = $this->service->verify($this->service->findOrFail($id), (int) auth()->id());
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Payment verified successfully.',
            'data' => $challan,
        ]);
    }

    public function receipt(int $id): JsonResponse
    {
        $challan = $this->service->findOrFail($id);

        if ($challan->status !== 'verified') {
            return response()->json([
                'success' => false,
                'message' => 'Receipt is only available for verified payments.',
            ], 422);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'receipt_no' => 'RCP-' . str_pad((string) $challan->id, 8, '0', STR_PAD_LEFT),
                'issued_at' => now()->toDateTimeString(),
                'candidate' => $challan->application?->candidate?->first_name . ' ' . $challan->application?->candidate?->last_name,
                'advertisement_code' => $challan->advt_code,
                'voucher_no' => $challan->voucher_no,
                'amount' => $challan->amount,
                'paid_at' => $challan->paid_at,
                'verified_by' => $challan->verifiedBy?->name,
            ],
        ]);
    }

    public function doubleFee(Request $request): JsonResponse
    {
        $data = $request->validate([
            'application_id' => ['required', 'integer', 'exists:applications,id'],
            'amount' => ['required', 'numeric', 'min:0'],
        ]);

        $application = \App\Models\Application::findOrFail($data['application_id']);
        $challan = $this->service->applyDoubleFee($application, (float) $data['amount']);

        return response()->json([
            'success' => true,
            'message' => 'Double fee challan created successfully.',
            'data' => $challan,
        ], 201);
    }
}