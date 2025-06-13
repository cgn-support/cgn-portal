<?php

namespace App\Filament\Widgets;

use App\Models\Project;
use App\Services\Tracking\TrackingAnalyticsService;
use Filament\Widgets\Widget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;

class ProjectLeaderboardWidget extends Widget
{
    use InteractsWithPageFilters;

    protected static string $view = 'filament.widgets.project-leaderboard';
    protected static ?string $pollingInterval = null;
    protected int | string | array $columnSpan = 1;

    public $heading = 'Project Leaderboard';

    protected static ?int $sort = 4;

    protected function getViewData(): array
    {
        $analyticsService = app(TrackingAnalyticsService::class);

        // Get dashboard filters
        $projectId = $this->filters['project'] ?? null;
        $dateRange = $this->filters['dateRange'] ?? 'last_30_days';
        $startDate = $this->filters['startDate'] ?? null;
        $endDate = $this->filters['endDate'] ?? null;

        // Use custom dates if dateRange is 'custom'
        if ($dateRange === 'custom' && $startDate && $endDate) {
            $dateRange = 'custom';
        }

        // Get projects with tracking URLs
        $projectsQuery = Project::whereNotNull('project_url')->with('business');

        // If specific project is filtered, only show that one
        if ($projectId) {
            $projectsQuery->where('id', $projectId);
        }

        $projects = $projectsQuery->get();
        $leaderboardData = collect();

        foreach ($projects as $project) {
            $metrics = $analyticsService->getProjectMetrics($project, $dateRange);

            $totalLeads = ($metrics['phone_calls'] ?? 0) + ($metrics['form_submissions'] ?? 0);
            $visitors = $metrics['unique_visitors'] ?? 0;
            $conversionRate = $visitors > 0 ? round(($totalLeads / $visitors) * 100, 1) : 0;

            $leaderboardData->push([
                'id' => $project->id,
                'business_name' => $project->business->name ?? 'Project ' . $project->id,
                'total_leads' => $totalLeads,
                'phone_calls' => $metrics['phone_calls'] ?? 0,
                'form_submissions' => $metrics['form_submissions'] ?? 0,
                'unique_visitors' => $visitors,
                'conversion_rate' => $conversionRate,
            ]);
        }

        // Sort by total leads and add ranking
        $rankedData = $leaderboardData
            ->sortByDesc('total_leads')
            ->values()
            ->map(function ($item, $index) {
                $item['rank'] = $index + 1;
                $item['rank_icon'] = match ($index + 1) {
                    1 => '🏆',
                    2 => '🥈',
                    3 => '🥉',
                    default => $index + 1,
                };
                $item['rank_class'] = match ($index + 1) {
                    1 => 'text-yellow-600 font-bold',
                    2 => 'text-gray-500 font-semibold',
                    3 => 'text-orange-600 font-semibold',
                    default => 'text-gray-400',
                };
                return $item;
            });

        $heading = $this->getHeading($dateRange, $projectId);

        return [
            'projects' => $rankedData->take(10), // Limit to top 10
            'heading' => $heading,
            'isEmpty' => $rankedData->isEmpty(),
        ];
    }

    private function getHeading(string $dateRange, ?string $projectId): string
    {
        if ($projectId) {
            return 'Project Performance';
        }

        $dateLabels = [
            'last_7_days' => 'Last 7 Days',
            'last_30_days' => 'Last 30 Days',
            'last_90_days' => 'Last 90 Days',
            'this_month' => 'This Month',
            'last_month' => 'Last Month',
            'this_year' => 'This Year',
            'custom' => 'Custom Range',
        ];

        return 'Project Leaderboard - ' . ($dateLabels[$dateRange] ?? 'Last 30 Days');
    }

    public static function canView(): bool
    {
        return auth()->user()?->hasRole(['admin', 'account_manager']) ?? false;
    }
}
