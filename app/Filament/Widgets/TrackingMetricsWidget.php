<?php

namespace App\Filament\Widgets;

use App\Models\Project;
use App\Services\Tracking\TrackingAnalyticsService;
use Filament\Forms\Components\Select;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Cache;

class TrackingMetricsWidget extends ChartWidget
{
    protected static ?string $heading = 'Tracking Metrics by Project';
    protected static ?string $pollingInterval = null;

    public ?string $filter = 'last_30_days';
    public ?string $projectFilter = null;

    protected function getData(): array
    {
        $analyticsService = app(TrackingAnalyticsService::class);

        if ($this->projectFilter) {
            $project = Project::find($this->projectFilter);
            if ($project) {
                return $this->getProjectChartData($project, $analyticsService);
            }
        }

        return $this->getAllProjectsChartData($analyticsService);
    }

    private function getProjectChartData(Project $project, TrackingAnalyticsService $analyticsService): array
    {
        $trendingData = $analyticsService->getProjectTrending($project, 30);

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
        $projects = Project::whereNotNull('project_url')->limit(10)->get();
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
        return 'line';
    }

    protected function getFilters(): ?array
    {
        return [
            'today' => 'Today',
            'yesterday' => 'Yesterday',
            'last_7_days' => 'Last 7 days',
            'last_30_days' => 'Last 30 days',
            'this_month' => 'This month',
        ];
    }

    protected function getFormSchema(): array
    {
        return [
            Select::make('projectFilter')
                ->label('Project')
                ->placeholder('All Projects')
                ->options(function () {
                    return Project::whereNotNull('project_url')
                        ->with('business')
                        ->get()
                        ->pluck('business.name', 'id')
                        ->toArray();
                })
                ->live(),
        ];
    }

    public static function canView(): bool
    {
        return auth()->user()?->hasRole(['admin', 'account_manager']) ?? false;
    }
}
