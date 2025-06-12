<?php

namespace App\Services\Tracking;

use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class TrackingCacheService
{
    private int $aggregateMetricsTtl;
    private int $sessionJourneyTtl;
    private int $apiHealthTtl;

    public function __construct()
    {
        $this->aggregateMetricsTtl = config('cache.tracking.aggregate_metrics_ttl', 30 * 60); // 30 minutes
        $this->sessionJourneyTtl = config('cache.tracking.session_journey_ttl', 2 * 60 * 60); // 2 hours
        $this->apiHealthTtl = config('cache.tracking.api_health_ttl', 5 * 60); // 5 minutes
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
     * Check if using Redis cache driver
     */
    private function isRedisDriver(): bool
    {
        return config('cache.default') === 'redis';
    }
}
