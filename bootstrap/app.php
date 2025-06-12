<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Jobs\FetchTrackingMetricsJob;
use Illuminate\Support\Facades\Cache;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withSchedule(function ($schedule) {
        // Fetch tracking metrics every 30 minutes
        $schedule->job(new FetchTrackingMetricsJob())
            ->everyThirtyMinutes()
            ->withoutOverlapping()
            ->onOneServer();

        // Optional: Run a quick health check every 5 minutes
        $schedule->call(function () {
            Cache::put('tracking:last_health_check', now(), 600);
        })->everyFiveMinutes();
    })
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
