<?php

namespace App\Livewire;

use App\Models\Project;
use App\Services\Tracking\TrackingAnalyticsService;
use Livewire\Component;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class DashboardStats extends Component
{
    public $projects;
    public $selectedProjectId = 'all';
    public $selectedDateRange = 'last_30_days';

    // Metrics
    public $visitorCount = 0;
    public $callCount = 0;
    public $formCount = 0;
    public $conversionRate = 0;
    public $totalLeadsValue = 0;

    // Chart data
    public $chartData = [];
    public $chartLabels = [];

    public $isLoading = false;

    public function mount($projects)
    {
        $this->projects = $projects;
        $this->loadStats();
    }

    public function updated($propertyName)
    {
        if (in_array($propertyName, ['selectedProjectId', 'selectedDateRange'])) {
            $this->loadStats();
        }
    }

    public function loadStats()
    {
        $this->isLoading = true;

        try {
            $analyticsService = app(TrackingAnalyticsService::class);

            if ($this->selectedProjectId === 'all') {
                $this->loadAllProjectsStats($analyticsService);
            } else {
                $this->loadSingleProjectStats($analyticsService);
            }

            $this->loadChartData($analyticsService);
        } catch (\Exception $e) {
            Log::error('Error loading dashboard stats: ' . $e->getMessage());
            $this->resetStats();
        }

        $this->isLoading = false;
    }

    private function loadAllProjectsStats($analyticsService)
    {
        $totalMetrics = [
            'unique_visitors' => 0,
            'phone_calls' => 0,
            'form_submissions' => 0,
        ];

        $totalLeadsValue = 0;

        foreach ($this->projects as $project) {
            $metrics = $analyticsService->getProjectMetrics($project, $this->selectedDateRange);

            $totalMetrics['unique_visitors'] += $metrics['unique_visitors'] ?? 0;
            $totalMetrics['phone_calls'] += $metrics['phone_calls'] ?? 0;
            $totalMetrics['form_submissions'] += $metrics['form_submissions'] ?? 0;

            // Get leads value for this project
            $leadsValue = $project->leads()
                ->where('status', 'closed')
                ->whereNotNull('value')
                ->when($this->selectedDateRange !== 'all_time', function ($query) {
                    $dates = $this->getDateRange();
                    return $query->whereBetween('submitted_at', [$dates['start'], $dates['end']]);
                })
                ->sum('value');

            $totalLeadsValue += $leadsValue;
        }

        $this->visitorCount = $totalMetrics['unique_visitors'];
        $this->callCount = $totalMetrics['phone_calls'];
        $this->formCount = $totalMetrics['form_submissions'];
        $this->totalLeadsValue = $totalLeadsValue;

        $totalLeads = $this->callCount + $this->formCount;
        $this->conversionRate = $this->visitorCount > 0 ? round(($totalLeads / $this->visitorCount) * 100, 1) : 0;
    }

    private function loadSingleProjectStats($analyticsService)
    {
        $project = $this->projects->find($this->selectedProjectId);

        if (!$project) {
            $this->resetStats();
            return;
        }

        $metrics = $analyticsService->getProjectMetrics($project, $this->selectedDateRange);

        $this->visitorCount = $metrics['unique_visitors'] ?? 0;
        $this->callCount = $metrics['phone_calls'] ?? 0;
        $this->formCount = $metrics['form_submissions'] ?? 0;

        // Get leads value for this project
        $this->totalLeadsValue = $project->leads()
            ->where('status', 'closed')
            ->whereNotNull('value')
            ->when($this->selectedDateRange !== 'all_time', function ($query) {
                $dates = $this->getDateRange();
                return $query->whereBetween('submitted_at', [$dates['start'], $dates['end']]);
            })
            ->sum('value');

        $totalLeads = $this->callCount + $this->formCount;
        $this->conversionRate = $this->visitorCount > 0 ? round(($totalLeads / $this->visitorCount) * 100, 1) : 0;
    }

    private function loadChartData($analyticsService)
    {
        // Simple 7-day trending for the chart
        $chartData = ['visitors' => [], 'leads' => []];
        $labels = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $labels[] = $date->format('M j');

            if ($this->selectedProjectId === 'all') {
                $dayVisitors = 0;
                $dayLeads = 0;

                foreach ($this->projects as $project) {
                    $dayMetrics = $analyticsService->getProjectMetrics($project, 'day_' . $date->toDateString());
                    $dayVisitors += $dayMetrics['unique_visitors'] ?? 0;
                    $dayLeads += ($dayMetrics['phone_calls'] ?? 0) + ($dayMetrics['form_submissions'] ?? 0);
                }

                $chartData['visitors'][] = $dayVisitors;
                $chartData['leads'][] = $dayLeads;
            } else {
                $project = $this->projects->find($this->selectedProjectId);
                if ($project) {
                    $dayMetrics = $analyticsService->getProjectMetrics($project, 'day_' . $date->toDateString());
                    $chartData['visitors'][] = $dayMetrics['unique_visitors'] ?? 0;
                    $chartData['leads'][] = ($dayMetrics['phone_calls'] ?? 0) + ($dayMetrics['form_submissions'] ?? 0);
                }
            }
        }

        $this->chartData = $chartData;
        $this->chartLabels = $labels;
    }

    private function getDateRange()
    {
        $now = Carbon::now();

        return match ($this->selectedDateRange) {
            'today' => [
                'start' => $now->copy()->startOfDay(),
                'end' => $now->copy()->endOfDay(),
            ],
            'last_7_days' => [
                'start' => $now->copy()->subDays(7)->startOfDay(),
                'end' => $now->copy()->endOfDay(),
            ],
            'last_30_days' => [
                'start' => $now->copy()->subDays(30)->startOfDay(),
                'end' => $now->copy()->endOfDay(),
            ],
            'this_month' => [
                'start' => $now->copy()->startOfMonth(),
                'end' => $now->copy()->endOfDay(),
            ],
            default => [
                'start' => $now->copy()->subDays(30)->startOfDay(),
                'end' => $now->copy()->endOfDay(),
            ],
        };
    }

    private function resetStats()
    {
        $this->visitorCount = 0;
        $this->callCount = 0;
        $this->formCount = 0;
        $this->conversionRate = 0;
        $this->totalLeadsValue = 0;
        $this->chartData = [];
        $this->chartLabels = [];
    }

    public function render()
    {
        return view('livewire.dashboard-stats');
    }
}
