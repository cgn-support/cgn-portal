<?php

namespace App\Services\Tracking;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class TrackingService
{
    private string $baseUrl;
    private int $timeout;

    public function __construct()
    {
        $this->baseUrl = config('services.tracking.url', 'https://tracking.contractorgrowthnetwork.com');
        $this->timeout = config('services.tracking.timeout', 30);


        // Disable SSL verification for local development
        if (app()->environment('local') && str_contains($this->baseUrl, 'tracking.test')) {
            Http::withoutVerifying();
        }
    }

    /**
     * Get tracking events by domain and filters
     */
    public function getEvents(string $domain, array $filters = []): array
    {
        try {
            $response = Http::timeout($this->timeout)
                ->get("{$this->baseUrl}/api/session-collect", array_merge([
                    'domain' => $domain,
                ], $filters));

            if ($response->successful()) {
                return $response->json() ?? [];
            }

            Log::warning('Tracking API request failed', [
                'domain' => $domain,
                'status' => $response->status(),
                'response' => $response->body()
            ]);

            return [];
        } catch (\Exception $e) {
            Log::error('Tracking API request exception', [
                'domain' => $domain,
                'error' => $e->getMessage()
            ]);

            return [];
        }
    }

    /**
     * Get events by session ID
     */
    public function getSessionEvents(string $sessionId): array
    {
        try {
            $response = Http::timeout($this->timeout)
                ->get("{$this->baseUrl}/api/session-collect", [
                    'session_id' => $sessionId,
                ]);

            if ($response->successful()) {
                return $response->json() ?? [];
            }

            return [];
        } catch (\Exception $e) {
            Log::error('Tracking API session request exception', [
                'session_id' => $sessionId,
                'error' => $e->getMessage()
            ]);

            return [];
        }
    }

    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    public function checkHealth(): bool
    {
        try {
            $http = Http::timeout(5);

            // Disable SSL verification for local .test domains
            if (str_contains($this->baseUrl, '.test')) {
                $http = $http->withoutVerifying();
            }

            // Try /api/health first, then fallback to /health
            $healthUrls = [
                "{$this->baseUrl}/api/health",
                "{$this->baseUrl}/health"
            ];

            foreach ($healthUrls as $url) {
                try {
                    $response = $http->get($url);
                    if ($response->successful()) {
                        \Illuminate\Support\Facades\Log::info('Health check successful', ['url' => $url]);
                        return true;
                    }
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::warning('Health check failed for URL', [
                        'url' => $url,
                        'error' => $e->getMessage()
                    ]);
                    continue;
                }
            }

            return false;
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Tracking API health check failed', [
                'base_url' => $this->baseUrl,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }


    /**
     * Get aggregate metrics for domain and date range
     */
    public function getAggregateMetrics(string $domain, Carbon $startDate, Carbon $endDate): array
    {
        $events = $this->getEvents($domain, [
            'date_from' => $startDate->toDateString(),
            'date_to' => $endDate->toDateString(),
        ]);

        return $this->processAggregateMetrics($events);
    }

    /**
     * Process raw events into aggregate metrics
     */
    private function processAggregateMetrics(array $events): array
    {
        $uniqueVisitors = collect($events)
            ->where('event_type', 'pageview')
            ->pluck('session_id')
            ->unique()
            ->count();

        $phoneCalls = collect($events)
            ->where('event_type', 'phone_call_attempt')
            ->count();

        $formSubmissions = collect($events)
            ->where('event_type', 'form_submission')
            ->count();

        $ctaClicks = collect($events)
            ->where('event_type', 'cta_click')
            ->count();

        $pageViews = collect($events)
            ->where('event_type', 'pageview')
            ->count();

        return [
            'unique_visitors' => $uniqueVisitors,
            'phone_calls' => $phoneCalls,
            'form_submissions' => $formSubmissions,
            'cta_clicks' => $ctaClicks,
            'page_views' => $pageViews,
            'total_events' => count($events),
        ];
    }
}
