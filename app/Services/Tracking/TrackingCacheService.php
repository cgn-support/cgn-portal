<?php

namespace App\Services\Tracking;

use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class TrackingCacheService
{
    private int $aggregateMetricsTtl;
    private int $sessionJourneyTtl;
    private int $apiHealthTtl;
    private int $leadMetricsTtl;
    private int $chartDataTtl;
    private int $weeklyDataTtl;

    public function __construct()
    {
        $this->aggregateMetricsTtl = config('cache.tracking.aggregate_metrics_ttl', 30 * 60); // 30 minutes
        $this->sessionJourneyTtl = config('cache.tracking.session_journey_ttl', 2 * 60 * 60); // 2 hours
        $this->apiHealthTtl = config('cache.tracking.api_health_ttl', 5 * 60); // 5 minutes
        $this->leadMetricsTtl = config('cache.tracking.lead_metrics_ttl', 24 * 60 * 60); // 24 hours
        $this->chartDataTtl = config('cache.tracking.chart_data_ttl', 60 * 60); // 1 hour
        $this->weeklyDataTtl = config('cache.tracking.weekly_data_ttl', 7 * 24 * 60 * 60); // 7 days
    }

    /**
     * Cache key for aggregate metrics
     */
    public function getAggregateMetricsKey(string $domain, string $dateRange): string
    {
        return "tracking:project:{$domain}:metrics:{$dateRange}";
    }

    /**
     * Cache key for session journey
     */
    public function getSessionJourneyKey(string $sessionId): string
    {
        return "tracking:session:{$sessionId}:journey";
    }

    /**
     * Cache key for API health
     */
    public function getApiHealthKey(): string
    {
        return "tracking:api_status";
    }

    /**
     * Get or set aggregate metrics cache
     */
    public function getOrSetAggregateMetrics(string $domain, string $dateRange, callable $callback): array
    {
        $key = $this->getAggregateMetricsKey($domain, $dateRange);

        return Cache::remember($key, $this->aggregateMetricsTtl, $callback);
    }

    /**
     * Get or set session journey cache
     */
    public function getOrSetSessionJourney(string $sessionId, callable $callback): array
    {
        $key = $this->getSessionJourneyKey($sessionId);

        return Cache::remember($key, $this->sessionJourneyTtl, $callback);
    }

    /**
     * Get or set API health status
     */
    public function getOrSetApiHealth(callable $callback): bool
    {
        $key = $this->getApiHealthKey();

        return Cache::remember($key, $this->apiHealthTtl, $callback);
    }

    /**
     * Clear cache for specific domain
     */
    public function clearDomainCache(string $domain): void
    {
        if ($this->isRedisDriver()) {
            $pattern = "tracking:project:{$domain}:*";
            $keys = Cache::getRedis()->keys($pattern);
            if (!empty($keys)) {
                Cache::getRedis()->del($keys);
            }
        } else {
            // For non-Redis drivers, we'll clear specific known keys
            $dateRanges = ['today', 'yesterday', 'last_7_days', 'last_30_days', 'this_month', 'last_month'];
            foreach ($dateRanges as $range) {
                Cache::forget($this->getAggregateMetricsKey($domain, $range));
            }
        }
    }

    /**
     * Clear all tracking cache
     */
    public function clearAllCache(): void
    {
        if ($this->isRedisDriver()) {
            $pattern = "tracking:*";
            $keys = Cache::getRedis()->keys($pattern);
            if (!empty($keys)) {
                Cache::getRedis()->del($keys);
            }
        } else {
            // For non-Redis drivers, clear known cache keys
            Cache::forget('tracking:admin:overview:last_30_days');
            Cache::forget('tracking:last_health_check');

            // Clear project-specific caches (this is less efficient but works)
            $projects = \App\Models\Project::whereNotNull('project_url')->get();
            foreach ($projects as $project) {
                $domain = $project->getTrackingDomain();
                if ($domain) {
                    $this->clearDomainCache($domain);
                }
            }
        }
    }

    /**
     * Get cache status for domain
     */
    public function getCacheStatus(string $domain, string $dateRange): array
    {
        $key = $this->getAggregateMetricsKey($domain, $dateRange);

        $status = [
            'has_cache' => Cache::has($key),
            'cache_key' => $key,
            'ttl_remaining' => null,
            'cache_driver' => config('cache.default'),
        ];

        if ($this->isRedisDriver() && $status['has_cache']) {
            $status['ttl_remaining'] = Cache::getRedis()->ttl($key);
        }

        return $status;
    }

    public function getApiHealthTtl(): int
    {
        return $this->apiHealthTtl;
    }

    /**
     * Cache key for lead metrics
     */
    public function getLeadMetricsKey(string $projectId, string $dateRange): string
    {
        return "tracking:project:{$projectId}:lead_metrics:{$dateRange}";
    }

    /**
     * Cache key for chart data
     */
    public function getChartDataKey(string $identifier, string $dateRange): string
    {
        return "tracking:chart_data:{$identifier}:{$dateRange}";
    }

    /**
     * Get or set lead metrics cache (24 hours)
     */
    public function getOrSetLeadMetrics(string $projectId, string $dateRange, callable $callback): array
    {
        $key = $this->getLeadMetricsKey($projectId, $dateRange);
        return Cache::remember($key, $this->leadMetricsTtl, $callback);
    }

    /**
     * Get or set chart data cache (1 hour)
     */
    public function getOrSetChartData(string $identifier, string $dateRange, callable $callback): array
    {
        $key = $this->getChartDataKey($identifier, $dateRange);
        return Cache::remember($key, $this->chartDataTtl, $callback);
    }

    /**
     * Get or set weekly data cache (7 days)
     */
    public function getOrSetWeeklyData(string $key, callable $callback): array
    {
        return Cache::remember($key, $this->weeklyDataTtl, $callback);
    }

    /**
     * Get cache with custom TTL
     */
    public function getOrSetWithTtl(string $key, int $ttl, callable $callback)
    {
        return Cache::remember($key, $ttl, $callback);
    }

    /**
     * Check if using Redis cache driver
     */
    private function isRedisDriver(): bool
    {
        return config('cache.default') === 'redis';
    }
}
