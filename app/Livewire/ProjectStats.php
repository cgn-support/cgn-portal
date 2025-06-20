<?php

namespace App\Livewire;

use App\Models\Project;
use App\Services\Tracking\TrackingAnalyticsService;
use App\Services\Tracking\TrackingCacheService;
use App\Services\KeywordApiService;
use Livewire\Component;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class ProjectStats extends Component
{
    public $project;
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

    public $isLoading = false;

    public function mount(Project $project)
    {
        $this->project = $project;
        $this->loadStats();
    }

    public function updated($propertyName)
    {
        if ($propertyName === 'selectedDateRange') {
            $this->loadStats();
        }
    }

    public function loadStats()
    {
        $this->isLoading = true;

        try {
            $analyticsService = app(TrackingAnalyticsService::class);
            $keywordService = app(KeywordApiService::class);

            $this->loadProjectStats($analyticsService, $keywordService);
            $this->loadProjectPreviousStats($analyticsService, $keywordService);
        } catch (\Exception $e) {
            Log::error('Error loading project stats: ' . $e->getMessage());
            $this->resetStats();
        }

        $this->isLoading = false;
    }

    private function loadProjectStats($analyticsService, $keywordService)
    {
        $cacheService = app(TrackingCacheService::class);
        
        // Cache lead metrics (visitors, calls, forms) for 24 hours
        $leadMetrics = $cacheService->getOrSetLeadMetrics(
            $this->project->id, 
            $this->selectedDateRange,
            function () use ($analyticsService) {
                return $analyticsService->getProjectMetrics($this->project, $this->selectedDateRange);
            }
        );

        $this->visitorCount = $leadMetrics['unique_visitors'] ?? 0;
        $this->callCount = $leadMetrics['phone_calls'] ?? 0;
        $this->formCount = $leadMetrics['form_submissions'] ?? 0;

        // Get keyword metrics for this project (already cached for 7 days in KeywordApiService)
        $currentDate = $this->getCurrentDateForRange();
        $keywordMetrics = $keywordService->getProjectKeywords($this->project, $currentDate);
        $this->keywordsInTop3 = $keywordMetrics['keywords_in_top_3'];
        $this->keywordsInTop10 = $keywordMetrics['keywords_in_top_10'];
    }

    private function loadProjectPreviousStats($analyticsService, $keywordService)
    {
        $cacheService = app(TrackingCacheService::class);
        $previousDateRange = $this->getPreviousDateRange();
        
        // Cache previous period lead metrics for 24 hours
        $previousMetrics = $cacheService->getOrSetLeadMetrics(
            $this->project->id . '_previous', 
            $previousDateRange,
            function () use ($analyticsService, $previousDateRange) {
                return $analyticsService->getProjectMetrics($this->project, $previousDateRange);
            }
        );

        $this->previousVisitorCount = $previousMetrics['unique_visitors'] ?? 0;
        $this->previousCallCount = $previousMetrics['phone_calls'] ?? 0;
        $this->previousFormCount = $previousMetrics['form_submissions'] ?? 0;

        // Get keyword metrics for this project (already cached for 7 days in KeywordApiService)
        $previousDate = $this->getPreviousDateForRange();
        $keywordMetrics = $keywordService->getProjectKeywords($this->project, $previousDate);
        $this->previousKeywordsInTop3 = $keywordMetrics['keywords_in_top_3'];
        $this->previousKeywordsInTop10 = $keywordMetrics['keywords_in_top_10'];
    }

    private function resetStats()
    {
        $this->visitorCount = 0;
        $this->callCount = 0;
        $this->formCount = 0;
        $this->keywordsInTop3 = 0;
        $this->keywordsInTop10 = 0;
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

    public function render()
    {
        return view('livewire.project-stats');
    }
}