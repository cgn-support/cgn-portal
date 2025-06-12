<?php

namespace App\Services\Tracking;

use App\Models\Project;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class TrackingAnalyticsService
{
    private TrackingService $trackingService;
    private TrackingCacheService $cacheService;

    public function __construct(TrackingService $trackingService, TrackingCacheService $cacheService)
    {
        $this->trackingService = $trackingService;
        $this->cacheService = $cacheService;
    }

    /**
     * Get metrics for a project with caching
     */
    public function getProjectMetrics(Project $project, string $dateRange = 'last_30_days', bool $forceRefresh = false): array
    {
        $domain = $project->getTrackingDomain();

        if (!$domain) {
            return $this->getEmptyMetrics();
        }

        $cacheKey = $dateRange;

        if ($forceRefresh) {
            $this->cacheService->clearDomainCache($domain);
        }

        return $this->cacheService->getOrSetAggregateMetrics(
            $domain,
            $cacheKey,
            function () use ($domain, $dateRange) {
                $dates = $this->getDateRange($dateRange);
                $metrics = $this->trackingService->getAggregateMetrics($domain, $dates['start'], $dates['end']);

                // Add metadata
                $metrics['cache_updated_at'] = now()->toISOString();
                $metrics['date_range'] = $dateRange;
                $metrics['start_date'] = $dates['start']->toDateString();
                $metrics['end_date'] = $dates['end']->toDateString();

                return $metrics;
            }
        );
    }

    /**
     * Get metrics for multiple projects
     */
    public function getMultipleProjectMetrics(array $projectIds, string $dateRange = 'last_30_days'): array
    {
        $projects = Project::whereIn('id', $projectIds)->get();
        $results = [];

        foreach ($projects as $project) {
            $results[$project->id] = $this->getProjectMetrics($project, $dateRange);
        }

        return $results;
    }

    /**
     * Get aggregate metrics across all projects
     */
    public function getAllProjectsMetrics(string $dateRange = 'last_30_days'): array
    {
        $projects = Project::whereNotNull('project_url')->get();
        $totalMetrics = $this->getEmptyMetrics();

        foreach ($projects as $project) {
            $projectMetrics = $this->getProjectMetrics($project, $dateRange);

            $totalMetrics['unique_visitors'] += $projectMetrics['unique_visitors'] ?? 0;
            $totalMetrics['phone_calls'] += $projectMetrics['phone_calls'] ?? 0;
            $totalMetrics['form_submissions'] += $projectMetrics['form_submissions'] ?? 0;
            $totalMetrics['cta_clicks'] += $projectMetrics['cta_clicks'] ?? 0;
            $totalMetrics['page_views'] += $projectMetrics['page_views'] ?? 0;
            $totalMetrics['total_events'] += $projectMetrics['total_events'] ?? 0;
        }

        return $totalMetrics;
    }

    /**
     * Get trending data for a project
     */
    public function getProjectTrending(Project $project, int $days = 30): array
    {
        $domain = $project->getTrackingDomain();

        if (!$domain) {
            return [];
        }

        $trendingData = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $dateRange = 'day_' . $date->toDateString();

            $metrics = $this->cacheService->getOrSetAggregateMetrics(
                $domain,
                $dateRange,
                function () use ($domain, $date) {
                    return $this->trackingService->getAggregateMetrics(
                        $domain,
                        $date->startOfDay(),
                        $date->endOfDay()
                    );
                }
            );

            $trendingData[] = [
                'date' => $date->toDateString(),
                'visitors' => $metrics['unique_visitors'] ?? 0,
                'calls' => $metrics['phone_calls'] ?? 0,
                'submissions' => $metrics['form_submissions'] ?? 0,
            ];
        }

        return $trendingData;
    }

    /**
     * Get conversion rates for a project
     */
    public function getProjectConversionRates(Project $project, string $dateRange = 'last_30_days'): array
    {
        $metrics = $this->getProjectMetrics($project, $dateRange);

        $visitors = $metrics['unique_visitors'] ?? 0;
        $calls = $metrics['phone_calls'] ?? 0;
        $submissions = $metrics['form_submissions'] ?? 0;

        if ($visitors === 0) {
            return [
                'visitor_to_call_rate' => 0,
                'visitor_to_submission_rate' => 0,
                'total_conversion_rate' => 0,
            ];
        }

        return [
            'visitor_to_call_rate' => round(($calls / $visitors) * 100, 2),
            'visitor_to_submission_rate' => round(($submissions / $visitors) * 100, 2),
            'total_conversion_rate' => round((($calls + $submissions) / $visitors) * 100, 2),
        ];
    }

    /**
     * Check if API is healthy
     */
    public function isApiHealthy(): bool
    {
        $isHealthy = $this->cacheService->getOrSetApiHealth(
            function () {
                return $this->trackingService->checkHealth();
            }
        );

        // Also update the last health check timestamp
        Cache::put('tracking:last_health_check', now(), $this->cacheService->getApiHealthTtl());

        return $isHealthy;
    }

    /**
     * Get cache status for project
     */
    public function getCacheInfo(Project $project, string $dateRange = 'last_30_days'): array
    {
        $domain = $project->getTrackingDomain();

        if (!$domain) {
            return ['has_cache' => false];
        }

        $cacheStatus = $this->cacheService->getCacheStatus($domain, $dateRange);
        $cacheStatus['api_healthy'] = $this->isApiHealthy();

        return $cacheStatus;
    }

    /**
     * Convert date range string to Carbon dates
     */
    private function getDateRange(string $range): array
    {
        $now = Carbon::now();

        return match ($range) {
            'today' => [
                'start' => $now->copy()->startOfDay(),
                'end' => $now->copy()->endOfDay(),
            ],
            'yesterday' => [
                'start' => $now->copy()->subDay()->startOfDay(),
                'end' => $now->copy()->subDay()->endOfDay(),
            ],
            'last_7_days' => [
                'start' => $now->copy()->subDays(7)->startOfDay(),
                'end' => $now->copy()->endOfDay(),
            ],
            'last_30_days' => [
                'start' => $now->copy()->subDays(30)->startOfDay(),
                'end' => $now->copy()->endOfDay(),
            ],
            'last_90_days' => [
                'start' => $now->copy()->subDays(90)->startOfDay(),
                'end' => $now->copy()->endOfDay(),
            ],
            'this_month' => [
                'start' => $now->copy()->startOfMonth(),
                'end' => $now->copy()->endOfDay(),
            ],
            'last_month' => [
                'start' => $now->copy()->subMonth()->startOfMonth(),
                'end' => $now->copy()->subMonth()->endOfMonth(),
            ],
            'this_year' => [
                'start' => $now->copy()->startOfYear(),
                'end' => $now->copy()->endOfDay(),
            ],
            default => [
                'start' => $now->copy()->subDays(30)->startOfDay(),
                'end' => $now->copy()->endOfDay(),
            ],
        };
    }

    /**
     * Get empty metrics array
     */
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
}
