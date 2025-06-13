<?php

namespace App\Filament\Widgets;

use App\Models\Project;
use App\Services\Tracking\TrackingAnalyticsService;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;

class TrackingMetricsWidget extends ChartWidget
{
    use InteractsWithPageFilters;

    protected static ?string $heading = 'Tracking Metrics by Project';
    protected static ?string $pollingInterval = null;
    protected int | string | array $columnSpan = 'full';

    public ?string $filter = 'last_30_days';

    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $analyticsService = app(TrackingAnalyticsService::class);

        // Get filters from dashboard
        $projectId = $this->filters['project'] ?? null;

        if ($projectId) {
            $project = Project::find($projectId);
            if ($project) {
                return $this->getProjectChartData($project, $analyticsService);
            }
        }

        return $this->getAllProjectsChartData($analyticsService);
    }

    private function getProjectChartData(Project $project, TrackingAnalyticsService $analyticsService): array
    {
        // Get dashboard filters
        $dateRange = $this->filters['dateRange'] ?? 'last_30_days';
        $startDate = $this->filters['startDate'] ?? null;
        $endDate = $this->filters['endDate'] ?? null;

        // Use custom dates if dateRange is 'custom'
        if ($dateRange === 'custom' && $startDate && $endDate) {
            $trendingData = $analyticsService->getProjectTrending($project, 'custom', $startDate, $endDate);
        } else {
            $trendingData = $analyticsService->getProjectTrending($project, $dateRange);
        }

        return [
            'datasets' => [
                [
                    'label' => 'Visitors',
                    'data' => array_column($trendingData, 'visitors'),
                    'borderColor' => '#3b82f6',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                ],
                [
                    'label' => 'Phone Calls',
                    'data' => array_column($trendingData, 'calls'),
                    'borderColor' => '#10b981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                ],
                [
                    'label' => 'Form Submissions',
                    'data' => array_column($trendingData, 'submissions'),
                    'borderColor' => '#f59e0b',
                    'backgroundColor' => 'rgba(245, 158, 11, 0.1)',
                ],
            ],
            'labels' => array_column($trendingData, 'date'),
        ];
    }

    private function getAllProjectsChartData(TrackingAnalyticsService $analyticsService): array
    {
        $projects = Project::whereNotNull('project_url')
            ->with('business')
            ->limit(10)
            ->get();

        $projectNames = [];
        $visitorsData = [];
        $callsData = [];
        $submissionsData = [];

        foreach ($projects as $project) {
            $metrics = $analyticsService->getProjectMetrics($project, $this->filter);
            $projectNames[] = $project->business->name ?? 'Project ' . $project->id;
            $visitorsData[] = $metrics['unique_visitors'] ?? 0;
            $callsData[] = $metrics['phone_calls'] ?? 0;
            $submissionsData[] = $metrics['form_submissions'] ?? 0;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Visitors',
                    'data' => $visitorsData,
                    'backgroundColor' => '#3b82f6',
                ],
                [
                    'label' => 'Phone Calls',
                    'data' => $callsData,
                    'backgroundColor' => '#10b981',
                ],
                [
                    'label' => 'Form Submissions',
                    'data' => $submissionsData,
                    'backgroundColor' => '#f59e0b',
                ],
            ],
            'labels' => $projectNames,
        ];
    }

    protected function getType(): string
    {
        // Use dashboard filter instead of $projectFilter
        $projectId = $this->filters['project'] ?? null;
        return $projectId ? 'line' : 'bar';
    }

    // protected function getFilters(): ?array
    // {
    //     return [
    //         'today' => 'Today',
    //         'yesterday' => 'Yesterday',
    //         'last_7_days' => 'Last 7 days',
    //         'last_30_days' => 'Last 30 days',
    //         'this_month' => 'This month',
    //     ];
    // }

    public function getHeading(): string
    {
        $projectId = $this->filters['project'] ?? null;
        $dateRange = $this->filters['dateRange'] ?? null;
        $startDate = $this->filters['startDate'] ?? null;
        $endDate = $this->filters['endDate'] ?? null;

        $heading = 'Tracking Metrics';

        // Add project name
        if ($projectId) {
            $project = Project::find($projectId);
            $projectName = $project?->business->name ?? 'Selected Project';
            $heading .= " - {$projectName}";
        } else {
            $heading .= " - All Projects";
        }

        // Add date range
        if ($dateRange && $dateRange !== 'custom') {
            $dateRangeLabels = [
                'last_7_days' => 'Last 7 Days',
                'last_30_days' => 'Last 30 Days',
                'last_90_days' => 'Last 90 Days',
                'this_month' => 'This Month',
                'last_month' => 'Last Month',
                'this_year' => 'This Year',
            ];
            $heading .= " ({$dateRangeLabels[$dateRange]})";
        } elseif ($startDate && $endDate) {
            $start = \Carbon\Carbon::parse($startDate)->format('M j');
            $end = \Carbon\Carbon::parse($endDate)->format('M j, Y');
            $heading .= " ({$start} - {$end})";
        } else {
            $heading .= " (Last 30 Days)";
        }

        return $heading;
    }

    public static function canView(): bool
    {
        return auth()->user()?->hasRole(['admin', 'account_manager']) ?? false;
    }
}
