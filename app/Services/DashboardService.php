<?php

namespace App\Services;

use App\Models\Resource;
use App\Models\Record;
use App\Models\User;
use App\Enums\RecordStatus;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    public function getStats(): array
    {
        return Cache::remember('dashboard_stats', 300, function () {
            return [
                'total_resources' => Resource::count(),
                'active_resources' => Resource::active()->count(),
                'total_records' => Record::count(),
                'pending_approvals' => Record::pending()->count(),
                'approved_this_month' => Record::approved()
                    ->whereMonth('approved_at', now()->month)
                    ->count(),
                'total_users' => User::active()->count(),
                'total_cost' => Resource::sum('cost'),
                'recent_resources' => Resource::with('category')
                    ->latest()
                    ->take(5)
                    ->get(),
                'recent_records' => Record::with(['resource', 'creator'])
                    ->latest()
                    ->take(5)
                    ->get(),
            ];
        });
    }

    public function getChartData(): array
    {
        return Cache::remember('dashboard_charts', 600, function () {
            // Monthly records for last 12 months
            $monthlyRecords = Record::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, count(*) as count')
                ->where('created_at', '>=', now()->subYear())
                ->groupBy('month')
                ->orderBy('month')
                ->get();

            // Records by type
            $recordsByType = Record::selectRaw('type, count(*) as count')
                ->groupBy('type')
                ->get();

            // Resources by status
            $resourcesByStatus = Resource::selectRaw('status, count(*) as count')
                ->groupBy('status')
                ->get();

            // Top categories
            $topCategories = Resource::selectRaw('categories.name, count(resources.id) as count')
                ->join('categories', 'resources.category_id', '=', 'categories.id')
                ->groupBy('categories.name')
                ->orderByDesc('count')
                ->take(5)
                ->get();

            return [
                'monthly_records' => $monthlyRecords,
                'records_by_type' => $recordsByType,
                'resources_by_status' => $resourcesByStatus,
                'top_categories' => $topCategories,
            ];
        });
    }
}