<?php

namespace App\Services\Tracking;

use App\Models\Project;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Services\Tracking\TrackingService;


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
    public function getProjectTrending(Project $project, string $dateRange = 'last_30_days', ?string $startDate = null, ?string $endDate = null): array
    {
        $domain = $project->getTrackingDomain();

        if (!$domain) {
            return [];
        }

        // Determine the date range and granularity
        $dateConfig = $this->getDateRangeConfig($dateRange, $startDate, $endDate);
        $trendingData = [];

        foreach ($dateConfig['dates'] as $dateInfo) {
            $cacheKey = $dateConfig['granularity'] . '_' . $dateInfo['key'];

            $metrics = $this->cacheService->getOrSetAggregateMetrics(
                $domain,
                $cacheKey,
                function () use ($domain, $dateInfo) {
                    return $this->trackingService->getAggregateMetrics(
                        $domain,
                        $dateInfo['start'],
                        $dateInfo['end']
                    );
                }
            );

            $trendingData[] = [
                'date' => $dateInfo['label'],
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
        \Illuminate\Support\Facades\Cache::put('tracking:last_health_check', now(), $this->cacheService->getApiHealthTtl());

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
     * Configure date ranges and granularity for trending data
     */
    private function getDateRangeConfig(string $dateRange, ?string $startDate = null, ?string $endDate = null): array
    {
        $now = Carbon::now();

        return match ($dateRange) {
            'last_7_days' => [
                'granularity' => 'daily',
                'dates' => collect(range(6, 0))->map(function ($daysAgo) use ($now) {
                    $date = $now->copy()->subDays($daysAgo);
                    return [
                        'key' => $date->toDateString(),
                        'label' => $date->format('M j'),
                        'start' => $date->startOfDay(),
                        'end' => $date->endOfDay(),
                    ];
                })->toArray(),
            ],

            'last_30_days' => [
                'granularity' => 'daily',
                'dates' => collect(range(29, 0))->map(function ($daysAgo) use ($now) {
                    $date = $now->copy()->subDays($daysAgo);
                    return [
                        'key' => $date->toDateString(),
                        'label' => $date->format('M j'),
                        'start' => $date->startOfDay(),
                        'end' => $date->endOfDay(),
                    ];
                })->toArray(),
            ],

            'last_90_days' => [
                'granularity' => 'weekly',
                'dates' => collect(range(12, 0))->map(function ($weeksAgo) use ($now) {
                    $startOfWeek = $now->copy()->subWeeks($weeksAgo)->startOfWeek();
                    $endOfWeek = $startOfWeek->copy()->endOfWeek();
                    return [
                        'key' => $startOfWeek->format('Y-W'),
                        'label' => $startOfWeek->format('M j'),
                        'start' => $startOfWeek,
                        'end' => $endOfWeek,
                    ];
                })->toArray(),
            ],

            'this_month' => [
                'granularity' => 'daily',
                'dates' => collect(range($now->day - 1, 0))->map(function ($daysAgo) use ($now) {
                    $date = $now->copy()->subDays($daysAgo);
                    return [
                        'key' => $date->toDateString(),
                        'label' => $date->format('M j'),
                        'start' => $date->startOfDay(),
                        'end' => $date->endOfDay(),
                    ];
                })->toArray(),
            ],

            'last_month' => [
                'granularity' => 'daily',
                'dates' => collect(range($now->copy()->subMonth()->daysInMonth - 1, 0))->map(function ($daysAgo) use ($now) {
                    $date = $now->copy()->subMonth()->startOfMonth()->addDays($daysAgo);
                    return [
                        'key' => $date->toDateString(),
                        'label' => $date->format('M j'),
                        'start' => $date->startOfDay(),
                        'end' => $date->endOfDay(),
                    ];
                })->toArray(),
            ],

            'this_year' => [
                'granularity' => 'monthly',
                'dates' => collect(range($now->month - 1, 0))->map(function ($monthsAgo) use ($now) {
                    $month = $now->copy()->subMonths($monthsAgo);
                    return [
                        'key' => $month->format('Y-m'),
                        'label' => $month->format('M'),
                        'start' => $month->startOfMonth(),
                        'end' => $month->endOfMonth(),
                    ];
                })->toArray(),
            ],

            'custom' => $this->getCustomDateRange($startDate, $endDate),

            default => $this->getDateRangeConfig('last_30_days'),
        };
    }

    /**
     * Handle custom date ranges
     */
    private function getCustomDateRange(?string $startDate, ?string $endDate): array
    {
        if (!$startDate || !$endDate) {
            return $this->getDateRangeConfig('last_30_days');
        }

        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);
        $daysDiff = $start->diffInDays($end);

        // Determine granularity based on date range length
        if ($daysDiff <= 31) {
            // Daily granularity for ranges up to 31 days
            $dates = [];
            $current = $start->copy();

            while ($current->lte($end)) {
                $dates[] = [
                    'key' => $current->toDateString(),
                    'label' => $current->format('M j'),
                    'start' => $current->startOfDay(),
                    'end' => $current->endOfDay(),
                ];
                $current->addDay();
            }

            return [
                'granularity' => 'daily',
                'dates' => $dates,
            ];
        } elseif ($daysDiff <= 90) {
            // Weekly granularity for ranges up to 90 days
            $dates = [];
            $current = $start->copy()->startOfWeek();

            while ($current->lt($end)) {
                $weekEnd = $current->copy()->endOfWeek();
                if ($weekEnd->gt($end)) {
                    $weekEnd = $end->copy();
                }

                $dates[] = [
                    'key' => $current->format('Y-W'),
                    'label' => $current->format('M j'),
                    'start' => $current,
                    'end' => $weekEnd,
                ];
                $current->addWeek();
            }

            return [
                'granularity' => 'weekly',
                'dates' => $dates,
            ];
        } else {
            // Monthly granularity for longer ranges
            $dates = [];
            $current = $start->copy()->startOfMonth();

            while ($current->lt($end)) {
                $monthEnd = $current->copy()->endOfMonth();
                if ($monthEnd->gt($end)) {
                    $monthEnd = $end->copy();
                }

                $dates[] = [
                    'key' => $current->format('Y-m'),
                    'label' => $current->format('M Y'),
                    'start' => $current,
                    'end' => $monthEnd,
                ];
                $current->addMonth();
            }

            return [
                'granularity' => 'monthly',
                'dates' => $dates,
            ];
        }
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
