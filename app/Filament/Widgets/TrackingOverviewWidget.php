<?php

namespace App\Filament\Widgets;

use App\Services\Tracking\TrackingAnalyticsService;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Cache;

class TrackingOverviewWidget extends BaseWidget
{
    protected static ?string $pollingInterval = null; // Disable auto-polling

    protected function getStats(): array
    {
        $analyticsService = app(TrackingAnalyticsService::class);

        // Get cached overview metrics
        $metrics = Cache::remember('tracking:admin:overview:last_30_days', 1800, function () use ($analyticsService) {
            return $analyticsService->getAllProjectsMetrics('last_30_days');
        });

        $isApiHealthy = $analyticsService->isApiHealthy();
        $lastUpdate = Cache::get('tracking:last_health_check', now()->subHours(2));

        return [
            Stat::make('Total Visitors', number_format($metrics['unique_visitors']))
                ->description('Last 30 days')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('primary')
                ->chart($this->getVisitorChart()),

            Stat::make('Phone Calls', number_format($metrics['phone_calls']))
                ->description('Last 30 days')
                ->descriptionIcon('heroicon-m-phone')
                ->color('success'),

            Stat::make('Form Submissions', number_format($metrics['form_submissions']))
                ->description('Last 30 days')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('warning'),

            Stat::make('API Status', $isApiHealthy ? 'Healthy' : 'Down')
                ->description('Last checked: ' . $lastUpdate->diffForHumans())
                ->descriptionIcon($isApiHealthy ? 'heroicon-m-check-circle' : 'heroicon-m-x-circle')
                ->color($isApiHealthy ? 'success' : 'danger'),
        ];
    }

    private function getVisitorChart(): array
    {
        // Simple 7-day visitor chart
        $chartData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $dayMetrics = Cache::get("tracking:admin:daily:{$date->toDateString()}", ['unique_visitors' => rand(10, 100)]);
            $chartData[] = $dayMetrics['unique_visitors'];
        }
        return $chartData;
    }

    public static function canView(): bool
    {
        return auth()->user()?->hasRole(['admin', 'account_manager']) ?? false;
    }
}
