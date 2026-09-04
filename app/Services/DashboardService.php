<?php

namespace App\Services;

use App\Models\Advertisement;
use App\Models\Application;
use App\Models\Candidate;
use App\Models\Challan;
use App\Models\ExamScheduling;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

class DashboardService
{
    public function getStats(): array
    {
        return Cache::remember('ppsc_dashboard_stats', 300, function () {
            return [
                'total_advertisements' => Advertisement::count(),
                'open_advertisements' => Advertisement::query()
                    ->where('application_start_at', '<=', now())
                    ->where('application_end_at', '>=', now())
                    ->count(),
                'total_candidates' => Candidate::count(),
                'total_users' => User::active()->count(),
                'total_applications' => Application::count(),
                'submitted_applications' => Application::whereNotNull('submitted_at')->count(),
                'verified_applications' => Application::whereNotNull('verified_at')->count(),
                'pending_payments' => Challan::where('status', 'pending')->count(),
                'verified_payments' => Challan::where('status', 'verified')->count(),
                'total_revenue' => (float) Challan::where('status', 'verified')->sum('amount'),
                'upcoming_exams' => ExamScheduling::where('exam_date', '>=', now()->toDateString())->count(),
                'recent_applications' => Application::with('candidate')
                    ->latest('id')
                    ->take(5)
                    ->get(),
                'recent_advertisements' => Advertisement::with('quota')
                    ->latest('id')
                    ->take(5)
                    ->get(),
            ];
        });
    }

    public function getChartData(): array
    {
        return Cache::remember('ppsc_dashboard_charts', 600, function () {
            $monthlyApplications = Application::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, count(*) as count')
                ->where('created_at', '>=', now()->subYear())
                ->groupBy('month')
                ->orderBy('month')
                ->get();

            $applicationsByPaymentStatus = Application::selectRaw('payment_status, count(*) as count')
                ->groupBy('payment_status')
                ->get();

            $challansByStatus = Challan::selectRaw('status, count(*) as count')
                ->groupBy('status')
                ->get();

            $topAdvertisements = Application::selectRaw('advertisement_code, count(*) as count')
                ->whereNotNull('advertisement_code')
                ->groupBy('advertisement_code')
                ->orderByDesc('count')
                ->take(5)
                ->get();

            return [
                'monthly_applications' => $monthlyApplications,
                'applications_by_payment_status' => $applicationsByPaymentStatus,
                'challans_by_status' => $challansByStatus,
                'top_advertisements' => $topAdvertisements,
            ];
        });
    }
}