<?php

namespace App\Filament\Widgets;

use App\Services\Tracking\TrackingAnalyticsService;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Support\Facades\Cache;

class TrackingOverviewWidget extends BaseWidget
{
    use InteractsWithPageFilters;

    protected static ?string $pollingInterval = null;

    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $analyticsService = app(TrackingAnalyticsService::class);

        // Get filters from dashboard
        $projectId = $this->filters['project'] ?? null;
        $dateRange = $this->filters['dateRange'] ?? 'last_30_days';
        $startDate = $this->filters['startDate'] ?? null;
        $endDate = $this->filters['endDate'] ?? null;

        // Use custom dates if dateRange is 'custom'
        if ($dateRange === 'custom' && $startDate && $endDate) {
            $dateRange = 'custom';
        }

        // Get metrics based on filters
        if ($projectId) {
            $project = \App\Models\Project::find($projectId);
            $metrics = $project ? $analyticsService->getProjectMetrics($project, $dateRange) : $this->getEmptyMetrics();
        } else {
            $metrics = $analyticsService->getAllProjectsMetrics($dateRange);
        }

        $isApiHealthy = $analyticsService->isApiHealthy();
        $lastUpdate = Cache::get('tracking:last_health_check', now()->subHours(2));

        return [
            Stat::make('Total Visitors', number_format($metrics['unique_visitors']))
                ->description($this->getDateRangeDescription($dateRange, $startDate, $endDate))
                ->descriptionIcon('heroicon-m-user-group')
                ->color('primary')
                ->chart($this->getVisitorChart()),

            Stat::make('Phone Calls', number_format($metrics['phone_calls']))
                ->description($this->getDateRangeDescription($dateRange, $startDate, $endDate))
                ->descriptionIcon('heroicon-m-phone')
                ->color('success'),

            Stat::make('Form Submissions', number_format($metrics['form_submissions']))
                ->description($this->getDateRangeDescription($dateRange, $startDate, $endDate))
                ->descriptionIcon('heroicon-m-document-text')
                ->color('warning'),

            Stat::make('API Status', $isApiHealthy ? 'Healthy' : 'Down')
                ->description('Last checked: ' . $lastUpdate->diffForHumans())
                ->descriptionIcon($isApiHealthy ? 'heroicon-m-check-circle' : 'heroicon-m-x-circle')
                ->color($isApiHealthy ? 'success' : 'danger'),
        ];
    }

    private function getDateRangeDescription(string $dateRange, ?string $startDate, ?string $endDate): string
    {
        if ($dateRange === 'custom' && $startDate && $endDate) {
            return \Carbon\Carbon::parse($startDate)->format('M j') . ' - ' . \Carbon\Carbon::parse($endDate)->format('M j, Y');
        }

        $descriptions = [
            'last_7_days' => 'Last 7 days',
            'last_30_days' => 'Last 30 days',
            'last_90_days' => 'Last 90 days',
            'this_month' => 'This month',
            'last_month' => 'Last month',
            'this_year' => 'This year',
        ];

        return $descriptions[$dateRange] ?? 'Last 30 days';
    }


    private function getDateRange(?string $startDate, ?string $endDate): string
    {
        if ($startDate && $endDate) {
            return 'custom'; // You'll need to handle custom date ranges in your analytics service
        }

        return 'last_30_days';
    }

    // private function getDateRangeDescription(?string $startDate, ?string $endDate): string
    // {
    //     if ($startDate && $endDate) {
    //         return \Carbon\Carbon::parse($startDate)->format('M j') . ' - ' . \Carbon\Carbon::parse($endDate)->format('M j, Y');
    //     }

    //     return 'Last 30 days';
    // }

    private function getEmptyMetrics(): array
    {
        return [
            'unique_visitors' => 0,
            'phone_calls' => 0,
            'form_submissions' => 0,
            'cta_clicks' => 0,
            'page_views' => 0,
            'total_events' => 0,
        ];
    }

    private function getVisitorChart(): array
    {
        // Simple chart data
        $chartData = [];
        for ($i = 6; $i >= 0; $i--) {
            $chartData[] = rand(10, 100);
        }
        return $chartData;
    }

    public static function canView(): bool
    {
        return auth()->user()?->hasRole(['admin', 'account_manager']) ?? false;
    }
}
