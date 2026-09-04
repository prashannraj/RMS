<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\RecordController;
use App\Http\Controllers\Api\ResourceController;
use App\Http\Controllers\Api\V1\AdminController;
use App\Http\Controllers\Api\V1\ApplicationController;
use App\Http\Controllers\Api\V1\CandidateController;
use App\Http\Controllers\Api\V1\DocumentController;
use App\Http\Controllers\Api\V1\ExamController;
use App\Http\Controllers\Api\V1\AdvertisementController;
use App\Http\Controllers\Api\V1\MasterDataController;
use App\Http\Controllers\Api\V1\NotificationController;
use App\Http\Controllers\Api\V1\PaymentController;
use App\Http\Controllers\Api\V1\ReportController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes — /api/v1
|--------------------------------------------------------------------------
*/

// ─── Public: Auth ───
Route::prefix('v1/auth')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);
});

// ─── Public: Master data & advertisements ───
Route::prefix('v1')->group(function () {
    Route::get('master-data/{type}', [MasterDataController::class, 'index']);
    Route::get('master-configurations', [MasterDataController::class, 'configurations']);
    Route::get('advertisements', [AdvertisementController::class, 'index']);
    Route::get('advertisements/{id}', [AdvertisementController::class, 'show']);
    Route::get('advertisements/{id}/vacancies', [AdvertisementController::class, 'vacancies']);
    Route::get('qualifications', [MasterDataController::class, 'index'])->defaults('type', 'qualifications');
});

// ─── Authenticated (Sanctum) ───
Route::prefix('v1')->middleware('auth:sanctum')->group(function () {

    // Auth
    Route::prefix('auth')->group(function () {
        Route::get('me', [AuthController::class, 'me']);
        Route::post('logout', [AuthController::class, 'logout']);
    });

    // Candidate profile & details
    Route::prefix('candidates')->group(function () {
        Route::get('profile', [CandidateController::class, 'profile']);
        Route::put('profile', [CandidateController::class, 'updateProfile']);
        Route::get('{id}', [CandidateController::class, 'show']);

        Route::get('{id}/address', [CandidateController::class, 'address']);
        Route::put('{id}/address', [CandidateController::class, 'address']);
        Route::get('{id}/extra-details', [CandidateController::class, 'extraDetails']);
        Route::put('{id}/extra-details', [CandidateController::class, 'extraDetails']);

        Route::get('{id}/education', [CandidateController::class, 'educationIndex']);
        Route::post('{id}/education', [CandidateController::class, 'educationStore']);
        Route::put('{id}/education/{educationId}', [CandidateController::class, 'educationUpdate']);
        Route::delete('{id}/education/{educationId}', [CandidateController::class, 'educationDestroy']);
    });

    // Vacancy eligibility (authenticated)
    Route::get('vacancies/{id}/eligibility', [AdvertisementController::class, 'eligibility']);

    // Dashboard stats for all authenticated users
    Route::get('dashboard', [App\Http\Controllers\Api\V1\AdminController::class, 'dashboard']);

    // Applications
    Route::prefix('applications')->group(function () {
        Route::get('/', [ApplicationController::class, 'index']);
        Route::post('/', [ApplicationController::class, 'store']);
        Route::get('{id}', [ApplicationController::class, 'show']);
        Route::put('{id}', [ApplicationController::class, 'update']);
        Route::post('{id}/submit', [ApplicationController::class, 'submit']);
        Route::post('{id}/verify', [ApplicationController::class, 'verify']);
        Route::get('{id}/status', [ApplicationController::class, 'status']);
        Route::get('{id}/history', [ApplicationController::class, 'history']);
    });

    // Payments
    Route::prefix('payments')->group(function () {
        Route::post('calculate', [PaymentController::class, 'calculate']);
        Route::post('double-fee', [PaymentController::class, 'doubleFee']);
        Route::get('/', [PaymentController::class, 'index']);
        Route::post('/', [PaymentController::class, 'store']);
        Route::get('{id}', [PaymentController::class, 'show']);
        Route::get('{id}/voucher', [PaymentController::class, 'voucher']);
        Route::get('{id}/transaction', [PaymentController::class, 'transaction']);
        Route::post('{id}/pay', [PaymentController::class, 'markPaid']);
        Route::post('{id}/verify', [PaymentController::class, 'verify']);
        Route::get('{id}/receipt', [PaymentController::class, 'receipt']);
    });

    // Exams
    Route::prefix('exams')->group(function () {
        Route::get('/', [ExamController::class, 'index']);
        Route::post('/', [ExamController::class, 'store']);
        Route::get('{examId}/candidates', [ExamController::class, 'candidates']);
    });

    // Exam centers
    Route::prefix('exam-centers')->group(function () {
        Route::get('/', [ExamController::class, 'centersIndex']);
        Route::post('/', [ExamController::class, 'centersStore']);
        Route::put('{id}', [ExamController::class, 'centersUpdate']);
    });

    // Exam allocation
    Route::post('exams/{advertisementCodeId}/allocate', [ExamController::class, 'allocate']);

    // Admit cards
    Route::prefix('admit-cards')->group(function () {
        Route::get('/', [ExamController::class, 'admitCardsIndex']);
        Route::post('issue', [ExamController::class, 'admitCardsIssue']);
    });

    // Documents
    Route::prefix('documents')->group(function () {
        Route::get('/', [DocumentController::class, 'index']);
        Route::post('/', [DocumentController::class, 'store']);
        Route::get('{id}', [DocumentController::class, 'show']);
        Route::get('{id}/download', [DocumentController::class, 'download']);
        Route::post('{id}/verify', [DocumentController::class, 'verify']);
    });

    // Notifications
    Route::prefix('notifications')->group(function () {
        Route::get('/', [NotificationController::class, 'index']);
        Route::get('unread', [NotificationController::class, 'unread']);
        Route::post('{id}/read', [NotificationController::class, 'markRead']);
        Route::post('read-all', [NotificationController::class, 'markAllRead']);
    });

    // Reports
    Route::prefix('reports')->group(function () {
        Route::get('/', [ReportController::class, 'index']);
        Route::post('/', [ReportController::class, 'store']);
        Route::get('{id}', [ReportController::class, 'show']);
        Route::get('{id}/download', [ReportController::class, 'download']);
    });

    // Legacy demo modules (records/resources)
    Route::apiResource('records', RecordController::class)->only(['index', 'store', 'show', 'update', 'destroy']);
    Route::post('records/{id}/approve', [RecordController::class, 'approve']);
    Route::post('records/{id}/reject', [RecordController::class, 'reject']);
    Route::apiResource('resources', ResourceController::class)->only(['index', 'store', 'show', 'update', 'destroy']);

    // Admin-only
    Route::prefix('admin')->middleware('role:admin')->group(function () {
        Route::get('dashboard', [AdminController::class, 'dashboard']);

        Route::prefix('users')->group(function () {
            Route::get('/', [AdminController::class, 'usersIndex']);
            Route::post('/', [AdminController::class, 'usersStore']);
            Route::put('{id}', [AdminController::class, 'usersUpdate']);
            Route::post('{id}/toggle-active', [AdminController::class, 'usersToggle']);
        });

        Route::prefix('roles')->group(function () {
            Route::get('/', [AdminController::class, 'rolesIndex']);
            Route::post('/', [AdminController::class, 'rolesStore']);
            Route::put('{id}', [AdminController::class, 'rolesUpdate']);
        });

        Route::prefix('permissions')->group(function () {
            Route::get('/', [AdminController::class, 'permissionsIndex']);
            Route::post('/', [AdminController::class, 'permissionsStore']);
        });

        Route::get('audit-logs', [AdminController::class, 'auditLogs']);
    });
});