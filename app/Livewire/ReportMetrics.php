<?php

namespace App\Livewire;

use App\Models\Report;
use App\Services\ReportMetricsService;
use Livewire\Component;

class ReportMetrics extends Component
{
    public Report $report;
    public array $metrics = [];
    public array $comparisons = [];
    public bool $trackingAvailable = false;
    public bool $loading = false;

    public function mount(Report $report)
    {
        $this->report = $report;
        $this->loadMetrics();
    }

    public function loadMetrics()
    {
        $this->loading = true;
        
        try {
            $metricsService = app(ReportMetricsService::class);
            
            // Get all metrics (automated + manual)
            $this->metrics = $metricsService->getAllMetrics($this->report);
            
            // Get month-over-month comparisons
            $this->comparisons = $metricsService->getMonthOverMonthChanges($this->report);
            
            // Check if tracking is available
            $this->trackingAvailable = $metricsService->isTrackingAvailable($this->report->project);
            
        } catch (\Exception $e) {
            $this->metrics = $this->getEmptyMetrics();
            $this->comparisons = $this->getEmptyComparisons();
            $this->trackingAvailable = false;
        }
        
        $this->loading = false;
    }

    public function refreshMetrics()
    {
        $this->loading = true;
        
        try {
            $metricsService = app(ReportMetricsService::class);
            
            // Refresh automated metrics from tracking API
            $metricsService->refreshAutomatedMetrics($this->report);
            
            // Reload all metrics
            $this->loadMetrics();
            
            $this->dispatch('metrics-refreshed');
            
        } catch (\Exception $e) {
            $this->dispatch('metrics-refresh-failed');
        }
        
        $this->loading = false;
    }

    private function getEmptyMetrics(): array
    {
        return [
            'organic_sessions' => 0,
            'contact_button_users' => 0,
            'form_submissions' => 0,
            'web_phone_calls' => 0,
            'gbp_phone_calls' => 0,
            'gbp_listing_clicks' => 0,
            'gbp_booking_clicks' => 0,
            'total_citations' => 0,
            'total_reviews' => 0,
        ];
    }

    private function getEmptyComparisons(): array
    {
        return [
            'calls' => [
                'percentage' => 0,
                'direction' => 'unchanged',
                'display' => '0%',
            ],
            'form_submissions' => [
                'percentage' => 0,
                'direction' => 'unchanged',
                'display' => '0%',
            ],
            'total_citations' => [
                'percentage' => 0,
                'direction' => 'unchanged',
                'display' => '0%',
            ],
            'total_reviews' => [
                'percentage' => 0,
                'direction' => 'unchanged',
                'display' => '0%',
            ],
        ];
    }

    public function render()
    {
        return view('livewire.report-metrics');
    }
}