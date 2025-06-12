<?php

namespace App\Services\Tracking;

use App\Models\Lead;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class TrackingJourneyService
{
    private TrackingService $trackingService;
    private TrackingCacheService $cacheService;

    public function __construct(TrackingService $trackingService, TrackingCacheService $cacheService)
    {
        $this->trackingService = $trackingService;
        $this->cacheService = $cacheService;
    }

    /**
     * Get complete journey for a lead
     */
    public function getLeadJourney(Lead $lead): array
    {
        $sessionId = $this->extractSessionIdFromLead($lead);

        if (!$sessionId) {
            return $this->getEmptyJourney();
        }

        return $this->cacheService->getOrSetSessionJourney(
            $sessionId,
            function () use ($sessionId) {
                $events = $this->trackingService->getSessionEvents($sessionId);
                return $this->processLeadJourney($events);
            }
        );
    }

    /**
     * Get journey summary for a lead
     */
    public function getLeadJourneySummary(Lead $lead): array
    {
        $journey = $this->getLeadJourney($lead);

        return [
            'initial_referrer' => $journey['initial_referrer'] ?? '(direct)',
            'total_page_views' => count($journey['page_views'] ?? []),
            'total_events' => $journey['total_events'] ?? 0,
            'session_duration' => $journey['session_duration_minutes'] ?? 0,
            'pages_visited' => array_slice($journey['pages_visited'] ?? [], 0, 5), // First 5 pages
            'last_page_before_conversion' => $journey['last_page_before_conversion'] ?? null,
            'conversion_page' => $journey['conversion_page'] ?? null,
        ];
    }

    /**
     * Get multiple lead journeys
     */
    public function getMultipleLeadJourneys(Collection $leads): array
    {
        $journeys = [];

        foreach ($leads as $lead) {
            $journeys[$lead->id] = $this->getLeadJourneySummary($lead);
        }

        return $journeys;
    }

    /**
     * Extract session ID from lead payload
     */
    private function extractSessionIdFromLead(Lead $lead): ?string
    {
        return $lead->payload['session_id'] ?? null;
    }

    /**
     * Process raw events into structured journey
     */
    private function processLeadJourney(array $events): array
    {
        if (empty($events)) {
            return $this->getEmptyJourney();
        }

        $eventCollection = collect($events)->sortBy('event_timestamp');

        // Basic journey info
        $firstEvent = $eventCollection->first();
        $lastEvent = $eventCollection->last();

        $journey = [
            'session_id' => $firstEvent['session_id'] ?? null,
            'initial_referrer' => $firstEvent['initial_referrer'] ?? '(direct)',
            'start_time' => $firstEvent['event_timestamp'] ?? null,
            'end_time' => $lastEvent['event_timestamp'] ?? null,
            'total_events' => $eventCollection->count(),
        ];

        // Calculate session duration
        if ($journey['start_time'] && $journey['end_time']) {
            $start = Carbon::parse($journey['start_time']);
            $end = Carbon::parse($journey['end_time']);
            $journey['session_duration_minutes'] = round($start->diffInMinutes($end), 1);
        }

        // Process page views
        $pageViews = $eventCollection->where('event_type', 'pageview');
        $journey['page_views'] = $pageViews->map(function ($event) {
            return [
                'url' => $event['current_url'] ?? '',
                'title' => $event['page_title'] ?? '',
                'timestamp' => $event['event_timestamp'] ?? '',
            ];
        })->values()->toArray();

        // Get unique pages visited in order
        $journey['pages_visited'] = $pageViews->pluck('current_url')
            ->unique()
            ->values()
            ->toArray();

        // CTA clicks
        $ctaClicks = $eventCollection->where('event_type', 'cta_click');
        $journey['cta_clicks'] = $ctaClicks->map(function ($event) {
            $eventData = is_string($event['event_data']) ?
                json_decode($event['event_data'], true) :
                $event['event_data'];

            return [
                'element_text' => $eventData['element_text'] ?? '',
                'element_cta_id' => $eventData['element_cta_id'] ?? '',
                'page_url' => $event['current_url'] ?? '',
                'timestamp' => $event['event_timestamp'] ?? '',
            ];
        })->values()->toArray();

        // Phone calls
        $phoneCalls = $eventCollection->where('event_type', 'phone_call_attempt');
        $journey['phone_calls'] = $phoneCalls->map(function ($event) {
            $eventData = is_string($event['event_data']) ?
                json_decode($event['event_data'], true) :
                $event['event_data'];

            return [
                'phone_number' => $eventData['phone_number'] ?? '',
                'page_url' => $event['current_url'] ?? '',
                'timestamp' => $event['event_timestamp'] ?? '',
            ];
        })->values()->toArray();

        // Form submissions
        $formSubmissions = $eventCollection->where('event_type', 'form_submission');
        $journey['form_submissions'] = $formSubmissions->map(function ($event) {
            $eventData = is_string($event['event_data']) ?
                json_decode($event['event_data'], true) :
                $event['event_data'];

            return [
                'form_name' => $eventData['form_name'] ?? '',
                'submission_method' => $eventData['submission_method'] ?? '',
                'page_url' => $event['current_url'] ?? '',
                'timestamp' => $event['event_timestamp'] ?? '',
            ];
        })->values()->toArray();

        // Find conversion details
        $conversion = $formSubmissions->first();
        if ($conversion) {
            $journey['conversion_page'] = $conversion['current_url'] ?? null;

            // Find last page before conversion
            $conversionTime = Carbon::parse($conversion['event_timestamp']);
            $lastPageBeforeConversion = $pageViews
                ->where('event_timestamp', '<', $conversionTime->toISOString())
                ->sortByDesc('event_timestamp')
                ->first();

            $journey['last_page_before_conversion'] = $lastPageBeforeConversion['current_url'] ?? null;
        }

        // UTM data from first event
        $journey['utm_data'] = [
            'utm_source' => $firstEvent['utm_source'] ?? null,
            'utm_medium' => $firstEvent['utm_medium'] ?? null,
            'utm_campaign' => $firstEvent['utm_campaign'] ?? null,
            'utm_term' => $firstEvent['utm_term'] ?? null,
            'utm_content' => $firstEvent['utm_content'] ?? null,
        ];

        return $journey;
    }

    /**
     * Get empty journey structure
     */
    private function getEmptyJourney(): array
    {
        return [
            'session_id' => null,
            'initial_referrer' => '(direct)',
            'start_time' => null,
            'end_time' => null,
            'total_events' => 0,
            'session_duration_minutes' => 0,
            'page_views' => [],
            'pages_visited' => [],
            'cta_clicks' => [],
            'phone_calls' => [],
            'form_submissions' => [],
            'conversion_page' => null,
            'last_page_before_conversion' => null,
            'utm_data' => [],
        ];
    }
}
