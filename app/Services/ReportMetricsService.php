<?php

namespace App\Services;

use App\Models\Project;
use App\Models\Report;
use App\Services\Tracking\TrackingAnalyticsService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class ReportMetricsService
{
    private TrackingAnalyticsService $trackingService;

    public function __construct(TrackingAnalyticsService $trackingService)
    {
        $this->trackingService = $trackingService;
    }

    /**
     * Get automated metrics for a report month
     */
    public function getAutomatedMetrics(Report $report): array
    {
        try {
            // Create date range for the specific report month
            $startDate = Carbon::create($report->report_year, $report->report_month, 1)->startOfMonth();
            $endDate = $startDate->copy()->endOfMonth();

            // Get metrics from tracking service using custom date range
            $metrics = $this->getProjectMetricsForDateRange($report->project, $startDate, $endDate);

            return [
                'organic_sessions' => $metrics['unique_visitors'] ?? 0,
                'contact_button_users' => $metrics['cta_clicks'] ?? 0,
                'form_submissions' => $metrics['form_submissions'] ?? 0,
                'web_phone_calls' => $metrics['phone_calls'] ?? 0,
            ];
        } catch (\Exception $e) {
            Log::error('Failed to fetch automated metrics for report', [
                'report_id' => $report->id,
                'project_id' => $report->project_id,
                'error' => $e->getMessage()
            ]);

            return $this->getEmptyAutomatedMetrics();
        }
    }

    /**
     * Get metrics for a project within a specific date range
     */
    private function getProjectMetricsForDateRange(Project $project, Carbon $startDate, Carbon $endDate): array
    {
        $domain = $project->getTrackingDomain();

        if (!$domain) {
            return $this->getEmptyAutomatedMetrics();
        }

        // Use the tracking service to get metrics for the specific date range
        $trackingService = app(\App\Services\Tracking\TrackingService::class);
        
        return $trackingService->getAggregateMetrics($domain, $startDate, $endDate);
    }

    /**
     * Update report with automated metrics
     */
    public function updateReportWithAutomatedMetrics(Report $report): Report
    {
        $automatedMetrics = $this->getAutomatedMetrics($report);
        
        // Get existing metrics data or create new array
        $metricsData = $report->metrics_data ?? [];
        
        // Merge automated metrics with existing data
        $metricsData = array_merge($metricsData, $automatedMetrics);
        
        $report->update(['metrics_data' => $metricsData]);
        
        return $report->fresh();
    }

    /**
     * Get manual metrics from report
     */
    public function getManualMetrics(Report $report): array
    {
        $metricsData = $report->metrics_data ?? [];

        return [
            'gbp_phone_calls' => $metricsData['gbp_phone_calls'] ?? 0,
            'gbp_listing_clicks' => $metricsData['gbp_listing_clicks'] ?? 0,
            'gbp_booking_clicks' => $metricsData['gbp_booking_clicks'] ?? 0,
            'total_citations' => $metricsData['total_citations'] ?? 0,
            'total_reviews' => $metricsData['total_reviews'] ?? 0,
        ];
    }

    /**
     * Get all metrics for a report (automated + manual)
     */
    public function getAllMetrics(Report $report): array
    {
        $automated = $this->getAutomatedMetrics($report);
        $manual = $this->getManualMetrics($report);

        return array_merge($automated, $manual);
    }

    /**
     * Refresh automated metrics for a report
     */
    public function refreshAutomatedMetrics(Report $report): array
    {
        // Force refresh by clearing cache if needed
        $this->trackingService->getProjectMetrics($report->project, 'custom', true);
        
        return $this->updateReportWithAutomatedMetrics($report)->metrics_data ?? [];
    }

    /**
     * Get previous month metrics for comparison
     */
    public function getPreviousMonthMetrics(Report $report): array
    {
        try {
            // Calculate previous month
            $previousMonth = $report->report_month === 1 ? 12 : $report->report_month - 1;
            $previousYear = $report->report_month === 1 ? $report->report_year - 1 : $report->report_year;

            // Find previous month's report for the same project
            $previousReport = Report::where('project_id', $report->project_id)
                ->where('report_month', $previousMonth)
                ->where(function($query) use ($previousYear, $report) {
                    if ($previousYear !== $report->report_year) {
                        // Handle year change (December to January)
                        $query->whereYear('report_date', $previousYear);
                    } else {
                        $query->whereYear('report_date', $report->report_year);
                    }
                })
                ->first();

            if ($previousReport) {
                return $this->getAllMetrics($previousReport);
            }

            // If no previous report exists, try to get automated metrics for previous month
            $startDate = Carbon::create($previousYear, $previousMonth, 1)->startOfMonth();
            $endDate = $startDate->copy()->endOfMonth();
            
            $automatedMetrics = $this->getProjectMetricsForDateRange($report->project, $startDate, $endDate);
            
            return [
                'organic_sessions' => $automatedMetrics['unique_visitors'] ?? 0,
                'contact_button_users' => $automatedMetrics['cta_clicks'] ?? 0,
                'form_submissions' => $automatedMetrics['form_submissions'] ?? 0,
                'web_phone_calls' => $automatedMetrics['phone_calls'] ?? 0,
                'gbp_phone_calls' => 0, // No previous data available
                'gbp_listing_clicks' => 0,
                'gbp_booking_clicks' => 0,
                'total_citations' => 0,
                'total_reviews' => 0,
            ];
        } catch (\Exception $e) {
            Log::error('Failed to fetch previous month metrics', [
                'report_id' => $report->id,
                'error' => $e->getMessage()
            ]);

            return $this->getEmptyAutomatedMetrics() + [
                'gbp_phone_calls' => 0,
                'gbp_listing_clicks' => 0,
                'gbp_booking_clicks' => 0,
                'total_citations' => 0,
                'total_reviews' => 0,
            ];
        }
    }

    /**
     * Get month-over-month percentage changes
     */
    public function getMonthOverMonthChanges(Report $report): array
    {
        $currentMetrics = $this->getAllMetrics($report);
        $previousMetrics = $this->getPreviousMonthMetrics($report);

        $comparisons = [];
        $metricsToCompare = ['form_submissions', 'total_citations', 'total_reviews'];

        // Calculate total calls (web + GBP)
        $currentCalls = ($currentMetrics['web_phone_calls'] ?? 0) + ($currentMetrics['gbp_phone_calls'] ?? 0);
        $previousCalls = ($previousMetrics['web_phone_calls'] ?? 0) + ($previousMetrics['gbp_phone_calls'] ?? 0);

        $comparisons['calls'] = $this->calculatePercentageChange($previousCalls, $currentCalls);

        // Calculate changes for other metrics
        foreach ($metricsToCompare as $metric) {
            $current = $currentMetrics[$metric] ?? 0;
            $previous = $previousMetrics[$metric] ?? 0;
            $comparisons[$metric] = $this->calculatePercentageChange($previous, $current);
        }

        return $comparisons;
    }

    /**
     * Calculate percentage change between two values
     */
    private function calculatePercentageChange(int $previous, int $current): array
    {
        if ($previous === 0) {
            if ($current === 0) {
                return [
                    'percentage' => 0,
                    'direction' => 'unchanged',
                    'display' => '0%',
                ];
            } else {
                return [
                    'percentage' => 100,
                    'direction' => 'increase',
                    'display' => '+' . $current . ' new',
                ];
            }
        }

        $change = (($current - $previous) / $previous) * 100;
        $roundedChange = round($change, 1);

        return [
            'percentage' => abs($roundedChange),
            'direction' => $change > 0 ? 'increase' : ($change < 0 ? 'decrease' : 'unchanged'),
            'display' => $this->formatPercentageDisplay($roundedChange),
            'previous' => $previous,
            'current' => $current,
        ];
    }

    /**
     * Format percentage display string
     */
    private function formatPercentageDisplay(float $change): string
    {
        if ($change == 0) {
            return '0%';
        } elseif ($change > 0) {
            return '+' . $change . '%';
        } else {
            return $change . '%';
        }
    }

    /**
     * Validate metrics data
     */
    public function validateMetrics(array $metrics): array
    {
        $validatedMetrics = [];
        
        $numericFields = [
            'organic_sessions', 'contact_button_users', 'form_submissions', 'web_phone_calls',
            'gbp_phone_calls', 'gbp_listing_clicks', 'gbp_booking_clicks', 'total_citations', 'total_reviews'
        ];

        foreach ($numericFields as $field) {
            $value = $metrics[$field] ?? 0;
            $validatedMetrics[$field] = is_numeric($value) && $value >= 0 ? (int) $value : 0;
        }

        return $validatedMetrics;
    }

    /**
     * Get empty automated metrics structure
     */
    private function getEmptyAutomatedMetrics(): array
    {
        return [
            'organic_sessions' => 0,
            'contact_button_users' => 0,
            'form_submissions' => 0,
            'web_phone_calls' => 0,
        ];
    }

    /**
     * Check if tracking is available for project
     */
    public function isTrackingAvailable(Project $project): bool
    {
        return !empty($project->getTrackingDomain()) && $this->trackingService->isApiHealthy();
    }

    /**
     * Get metrics summary for report
     */
    public function getMetricsSummary(Report $report): array
    {
        $metrics = $this->getAllMetrics($report);
        $comparisons = $this->getMonthOverMonthChanges($report);

        return [
            'metrics' => $metrics,
            'comparisons' => $comparisons,
            'tracking_available' => $this->isTrackingAvailable($report->project),
            'last_updated' => $report->updated_at,
        ];
    }
}