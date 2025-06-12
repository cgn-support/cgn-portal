<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\FetchTrackingMetricsJob;
use App\Services\Tracking\TrackingAnalyticsService;
use App\Services\Tracking\TrackingCacheService;
use Illuminate\Http\Request;
use Filament\Notifications\Notification;

class TrackingController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin|account_manager']);
    }

    public function refreshCache(TrackingCacheService $cacheService)
    {
        try {
            // Clear all tracking cache
            $cacheService->clearAllCache();

            // Dispatch job to fetch fresh data
            FetchTrackingMetricsJob::dispatch();

            Notification::make()
                ->title('Cache Refresh Started')
                ->body('Tracking data is being refreshed in the background.')
                ->success()
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->title('Cache Refresh Failed')
                ->body('Failed to refresh tracking cache: ' . $e->getMessage())
                ->danger()
                ->send();
        }

        return back();
    }

    public function clearCache(TrackingCacheService $cacheService)
    {
        try {
            $cacheService->clearAllCache();

            Notification::make()
                ->title('Cache Cleared')
                ->body('All tracking cache has been cleared.')
                ->success()
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->title('Clear Cache Failed')
                ->body('Failed to clear tracking cache: ' . $e->getMessage())
                ->danger()
                ->send();
        }

        return back();
    }

    public function healthCheck(TrackingAnalyticsService $analyticsService)
    {
        $isHealthy = $analyticsService->isApiHealthy();

        return response()->json([
            'healthy' => $isHealthy,
            'timestamp' => now()->toISOString(),
        ]);
    }
}
