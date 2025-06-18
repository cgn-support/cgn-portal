<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\MondayApiService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(MondayApiService::class, function ($app) {
            $token = config('services.monday.token');
            $apiUrl = config('services.monday.url');
            $portfolioBoardId = config('services.monday.portfolio_board_id');

            if (empty($token) || empty($portfolioBoardId) || empty($apiUrl)) {
                \Illuminate\Support\Facades\Log::error('CRITICAL: Monday API Service is not configured properly. Token, API URL, or Portfolio Board ID is missing from config/services.php or .env file.');
                // You might want to throw an exception here or ensure the app doesn't try to use a misconfigured service.
            }

            return new MondayApiService(
                $token,
                $apiUrl,
                $portfolioBoardId
            );
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                \App\Console\Commands\FetchTrackingMetrics::class,
            ]);
        }

        // Register event listeners
        \Illuminate\Support\Facades\Event::listen(
            \App\Events\ReportPublished::class,
            \App\Listeners\SendReportNotifications::class
        );
    }
}
