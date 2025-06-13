<?php

namespace App\Filament\Widgets;

use App\Services\Tracking\TrackingAnalyticsService;
use App\Services\Tracking\TrackingCacheService;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Cache;

class TrackingStatusWidget extends Widget
{
    protected static string $view = 'filament.widgets.tracking-status';
    protected static ?string $pollingInterval = '30s';

    protected static ?int $sort = 3;


    protected function getViewData(): array
    {
        $analyticsService = app(TrackingAnalyticsService::class);
        $cacheService = app(TrackingCacheService::class);

        $isApiHealthy = $analyticsService->isApiHealthy();
        $lastHealthCheck = Cache::get('tracking:last_health_check', now()->subHours(2));

        // Get cache statistics
        $cacheStats = $this->getCacheStatistics();

        return [
            'api_healthy' => $isApiHealthy,
            'last_health_check' => $lastHealthCheck,
            'cache_stats' => $cacheStats,
        ];
    }

    private function getCacheStatistics(): array
    {
        $cacheDriver = config('cache.default');

        if ($cacheDriver === 'redis') {
            try {
                $redis = Cache::getRedis();
                $trackingKeys = $redis->keys('tracking:*');

                return [
                    'total_cache_keys' => count($trackingKeys),
                    'memory_usage' => $this->formatBytes($redis->memory('usage')),
                    'cache_driver' => 'Redis',
                ];
            } catch (\Exception $e) {
                return [
                    'total_cache_keys' => 0,
                    'memory_usage' => 'Unknown',
                    'cache_driver' => 'Redis (Error)',
                    'error' => $e->getMessage(),
                ];
            }
        }

        // For non-Redis drivers
        return [
            'total_cache_keys' => 'N/A',
            'memory_usage' => 'N/A',
            'cache_driver' => ucfirst($cacheDriver),
        ];
    }


    private function formatBytes($bytes, $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, $precision) . ' ' . $units[$i];
    }

    public static function canView(): bool
    {
        return auth()->user()?->hasRole(['admin', 'account_manager']) ?? false;
    }

    public function refreshCache()
    {
        try {
            app(\App\Jobs\FetchTrackingMetricsJob::class)->handle(
                app(\App\Services\Tracking\TrackingAnalyticsService::class)
            );

            \Filament\Notifications\Notification::make()
                ->title('Cache refreshed successfully')
                ->success()
                ->send();
        } catch (\Exception $e) {
            \Filament\Notifications\Notification::make()
                ->title('Failed to refresh cache')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function clearCache()
    {
        try {
            app(\App\Services\Tracking\TrackingCacheService::class)->clearAllCache();

            \Filament\Notifications\Notification::make()
                ->title('Cache cleared successfully')
                ->success()
                ->send();
        } catch (\Exception $e) {
            \Filament\Notifications\Notification::make()
                ->title('Failed to clear cache')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }
}
