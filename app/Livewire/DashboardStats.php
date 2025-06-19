<?php

namespace App\Livewire;

use App\Models\Project;
use App\Services\Tracking\TrackingAnalyticsService;
use App\Services\KeywordApiService;
use Livewire\Component;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class DashboardStats extends Component
{
    public $projects;
    public $selectedProjectId = 'all';
    public $selectedDateRange = 'last_30_days';

    // Metrics
    public $visitorCount = 0;
    public $callCount = 0;
    public $formCount = 0;
    public $keywordsInTop3 = 0;
    public $keywordsInTop10 = 0;

    // Previous period metrics for comparison
    public $previousVisitorCount = 0;
    public $previousCallCount = 0;
    public $previousFormCount = 0;
    public $previousKeywordsInTop3 = 0;
    public $previousKeywordsInTop10 = 0;

    // Chart data
    public $chartData = [];
    public $chartLabels = [];

    public $isLoading = false;

    public function mount($projects)
    {
        $this->projects = $projects;
        $this->loadStats();
    }

    public function updated($propertyName)
    {
        if (in_array($propertyName, ['selectedProjectId', 'selectedDateRange'])) {
            $this->loadStats();
        }
    }

    public function loadStats()
    {
        $this->isLoading = true;

        try {
            $analyticsService = app(TrackingAnalyticsService::class);
            $keywordService = app(KeywordApiService::class);

            if ($this->selectedProjectId === 'all') {
                $this->loadAllProjectsStats($analyticsService, $keywordService);
                $this->loadAllProjectsPreviousStats($analyticsService, $keywordService);
            } else {
                $this->loadSingleProjectStats($analyticsService, $keywordService);
                $this->loadSingleProjectPreviousStats($analyticsService, $keywordService);
            }

            $this->loadChartData($analyticsService);
        } catch (\Exception $e) {
            Log::error('Error loading dashboard stats: ' . $e->getMessage());
            $this->resetStats();
        }

        $this->isLoading = false;
    }

    private function loadAllProjectsStats($analyticsService, $keywordService)
    {
        $totalMetrics = [
            'unique_visitors' => 0,
            'phone_calls' => 0,
            'form_submissions' => 0,
        ];

        $totalKeywordMetrics = [
            'keywords_in_top_3' => 0,
            'keywords_in_top_10' => 0,
        ];

        $currentDate = $this->getCurrentDateForRange();

        foreach ($this->projects as $project) {
            $metrics = $analyticsService->getProjectMetrics($project, $this->selectedDateRange);

            $totalMetrics['unique_visitors'] += $metrics['unique_visitors'] ?? 0;
            $totalMetrics['phone_calls'] += $metrics['phone_calls'] ?? 0;
            $totalMetrics['form_submissions'] += $metrics['form_submissions'] ?? 0;

            // Get keyword metrics for this project
            $keywordMetrics = $keywordService->getProjectKeywords($project, $currentDate);
            $totalKeywordMetrics['keywords_in_top_3'] += $keywordMetrics['keywords_in_top_3'];
            $totalKeywordMetrics['keywords_in_top_10'] += $keywordMetrics['keywords_in_top_10'];
        }

        $this->visitorCount = $totalMetrics['unique_visitors'];
        $this->callCount = $totalMetrics['phone_calls'];
        $this->formCount = $totalMetrics['form_submissions'];
        $this->keywordsInTop3 = $totalKeywordMetrics['keywords_in_top_3'];
        $this->keywordsInTop10 = $totalKeywordMetrics['keywords_in_top_10'];
    }

    private function loadAllProjectsPreviousStats($analyticsService, $keywordService)
    {
        $previousDateRange = $this->getPreviousDateRange();
        
        $totalMetrics = [
            'unique_visitors' => 0,
            'phone_calls' => 0,
            'form_submissions' => 0,
        ];

        $totalKeywordMetrics = [
            'keywords_in_top_3' => 0,
            'keywords_in_top_10' => 0,
        ];

        $previousDate = $this->getPreviousDateForRange();

        foreach ($this->projects as $project) {
            $metrics = $analyticsService->getProjectMetrics($project, $previousDateRange);

            $totalMetrics['unique_visitors'] += $metrics['unique_visitors'] ?? 0;
            $totalMetrics['phone_calls'] += $metrics['phone_calls'] ?? 0;
            $totalMetrics['form_submissions'] += $metrics['form_submissions'] ?? 0;

            // Get keyword metrics for this project
            $keywordMetrics = $keywordService->getProjectKeywords($project, $previousDate);
            $totalKeywordMetrics['keywords_in_top_3'] += $keywordMetrics['keywords_in_top_3'];
            $totalKeywordMetrics['keywords_in_top_10'] += $keywordMetrics['keywords_in_top_10'];
        }

        $this->previousVisitorCount = $totalMetrics['unique_visitors'];
        $this->previousCallCount = $totalMetrics['phone_calls'];
        $this->previousFormCount = $totalMetrics['form_submissions'];
        $this->previousKeywordsInTop3 = $totalKeywordMetrics['keywords_in_top_3'];
        $this->previousKeywordsInTop10 = $totalKeywordMetrics['keywords_in_top_10'];
    }

    private function loadSingleProjectStats($analyticsService, $keywordService)
    {
        $project = $this->projects->find($this->selectedProjectId);

        if (!$project) {
            $this->resetStats();
            return;
        }

        $metrics = $analyticsService->getProjectMetrics($project, $this->selectedDateRange);

        $this->visitorCount = $metrics['unique_visitors'] ?? 0;
        $this->callCount = $metrics['phone_calls'] ?? 0;
        $this->formCount = $metrics['form_submissions'] ?? 0;

        // Get keyword metrics for this project
        $currentDate = $this->getCurrentDateForRange();
        $keywordMetrics = $keywordService->getProjectKeywords($project, $currentDate);
        $this->keywordsInTop3 = $keywordMetrics['keywords_in_top_3'];
        $this->keywordsInTop10 = $keywordMetrics['keywords_in_top_10'];
    }

    private function loadSingleProjectPreviousStats($analyticsService, $keywordService)
    {
        $project = $this->projects->find($this->selectedProjectId);

        if (!$project) {
            $this->resetPreviousStats();
            return;
        }

        $previousDateRange = $this->getPreviousDateRange();
        $metrics = $analyticsService->getProjectMetrics($project, $previousDateRange);

        $this->previousVisitorCount = $metrics['unique_visitors'] ?? 0;
        $this->previousCallCount = $metrics['phone_calls'] ?? 0;
        $this->previousFormCount = $metrics['form_submissions'] ?? 0;

        // Get keyword metrics for this project
        $previousDate = $this->getPreviousDateForRange();
        $keywordMetrics = $keywordService->getProjectKeywords($project, $previousDate);
        $this->previousKeywordsInTop3 = $keywordMetrics['keywords_in_top_3'];
        $this->previousKeywordsInTop10 = $keywordMetrics['keywords_in_top_10'];
    }

    private function loadChartData($analyticsService)
    {
        // Simple 7-day trending for the chart
        $chartData = ['visitors' => [], 'leads' => []];
        $labels = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $labels[] = $date->format('M j');

            if ($this->selectedProjectId === 'all') {
                $dayVisitors = 0;
                $dayLeads = 0;

                foreach ($this->projects as $project) {
                    $dayMetrics = $analyticsService->getProjectMetrics($project, 'day_' . $date->toDateString());
                    $dayVisitors += $dayMetrics['unique_visitors'] ?? 0;
                    $dayLeads += ($dayMetrics['phone_calls'] ?? 0) + ($dayMetrics['form_submissions'] ?? 0);
                }

                $chartData['visitors'][] = $dayVisitors;
                $chartData['leads'][] = $dayLeads;
            } else {
                $project = $this->projects->find($this->selectedProjectId);
                if ($project) {
                    $dayMetrics = $analyticsService->getProjectMetrics($project, 'day_' . $date->toDateString());
                    $chartData['visitors'][] = $dayMetrics['unique_visitors'] ?? 0;
                    $chartData['leads'][] = ($dayMetrics['phone_calls'] ?? 0) + ($dayMetrics['form_submissions'] ?? 0);
                }
            }
        }

        $this->chartData = $chartData;
        $this->chartLabels = $labels;
    }

    private function getDateRange()
    {
        $now = Carbon::now();

        return match ($this->selectedDateRange) {
            'today' => [
                'start' => $now->copy()->startOfDay(),
                'end' => $now->copy()->endOfDay(),
            ],
            'last_7_days' => [
                'start' => $now->copy()->subDays(7)->startOfDay(),
                'end' => $now->copy()->endOfDay(),
            ],
            'last_30_days' => [
                'start' => $now->copy()->subDays(30)->startOfDay(),
                'end' => $now->copy()->endOfDay(),
            ],
            'this_month' => [
                'start' => $now->copy()->startOfMonth(),
                'end' => $now->copy()->endOfDay(),
            ],
            default => [
                'start' => $now->copy()->subDays(30)->startOfDay(),
                'end' => $now->copy()->endOfDay(),
            ],
        };
    }

    private function resetStats()
    {
        $this->visitorCount = 0;
        $this->callCount = 0;
        $this->formCount = 0;
        $this->keywordsInTop3 = 0;
        $this->keywordsInTop10 = 0;
        $this->chartData = [];
        $this->chartLabels = [];
        $this->resetPreviousStats();
    }

    private function resetPreviousStats()
    {
        $this->previousVisitorCount = 0;
        $this->previousCallCount = 0;
        $this->previousFormCount = 0;
        $this->previousKeywordsInTop3 = 0;
        $this->previousKeywordsInTop10 = 0;
    }

    private function getPreviousDateRange()
    {
        return match ($this->selectedDateRange) {
            'today' => 'yesterday',
            'last_7_days' => 'previous_7_days',
            'last_30_days' => 'previous_30_days',
            'this_month' => 'previous_month',
            default => 'previous_30_days',
        };
    }

    private function getPreviousDateRangeDates()
    {
        $now = Carbon::now();

        return match ($this->selectedDateRange) {
            'today' => [
                'start' => $now->copy()->subDay()->startOfDay(),
                'end' => $now->copy()->subDay()->endOfDay(),
            ],
            'last_7_days' => [
                'start' => $now->copy()->subDays(14)->startOfDay(),
                'end' => $now->copy()->subDays(8)->endOfDay(),
            ],
            'last_30_days' => [
                'start' => $now->copy()->subDays(60)->startOfDay(),
                'end' => $now->copy()->subDays(31)->endOfDay(),
            ],
            'this_month' => [
                'start' => $now->copy()->subMonth()->startOfMonth(),
                'end' => $now->copy()->subMonth()->endOfMonth(),
            ],
            default => [
                'start' => $now->copy()->subDays(60)->startOfDay(),
                'end' => $now->copy()->subDays(31)->endOfDay(),
            ],
        };
    }

    public function getPercentageChange($current, $previous)
    {
        if ($previous == 0) {
            return $current > 0 ? 100 : 0;
        }
        
        return round((($current - $previous) / $previous) * 100, 1);
    }

    public function getTrendDirection($current, $previous)
    {
        if ($current > $previous) return 'up';
        if ($current < $previous) return 'down';
        return 'neutral';
    }

    private function getCurrentDateForRange()
    {
        return match ($this->selectedDateRange) {
            'today' => now()->format('Y-m-d'),
            'last_7_days' => now()->format('Y-m-d'),
            'last_30_days' => now()->format('Y-m-d'),
            'this_month' => now()->format('Y-m-d'),
            default => now()->format('Y-m-d'),
        };
    }

    private function getPreviousDateForRange()
    {
        return match ($this->selectedDateRange) {
            'today' => now()->subDay()->format('Y-m-d'),
            'last_7_days' => now()->subDays(7)->format('Y-m-d'),
            'last_30_days' => now()->subDays(30)->format('Y-m-d'),
            'this_month' => now()->subMonth()->format('Y-m-d'),
            default => now()->subDays(30)->format('Y-m-d'),
        };
    }

    public function render()
    {
        return view('livewire.dashboard-stats');
    }
}
