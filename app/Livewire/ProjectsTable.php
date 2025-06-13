<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Project;
use App\Models\Lead;
use App\Services\Tracking\TrackingAnalyticsService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ProjectsTable extends Component
{
    use WithPagination;

    public Collection $projects;
    public $projectMetrics = [];

    // Filters
    public $statusFilter = 'all';
    public $sortBy = 'updated_at';
    public $sortDirection = 'desc';
    public $search = '';

    // View options
    public $viewMode = 'table'; // 'table' or 'cards'

    public $isLoading = false;

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => 'all'],
        'sortBy' => ['except' => 'updated_at'],
        'sortDirection' => ['except' => 'desc'],
        'viewMode' => ['except' => 'table'],
    ];

    public function mount()
    {
        $this->loadProjects();
        $this->loadProjectMetrics();
    }

    public function updated($propertyName)
    {
        if (in_array($propertyName, ['statusFilter', 'sortBy', 'sortDirection', 'search'])) {
            $this->resetPage();
            $this->loadProjects();
            $this->loadProjectMetrics();
        }
    }

    public function loadProjects()
    {
        $user = auth()->user();
        $this->projects = new Collection();

        if (!$user) {
            return;
        }

        $query = null;

        if ($user->hasRole('client_user')) {
            if ($user->client) {
                $businessIds = $user->client->businesses()->pluck('id');
                if ($businessIds->isNotEmpty()) {
                    $query = Project::whereIn('business_id', $businessIds);
                }
            }
        } elseif ($user->hasRole('account_manager')) {
            if (method_exists($user, 'managedProjects')) {
                $query = $user->managedProjects();
            }
        } elseif ($user->hasRole('admin')) {
            $query = Project::query();
        }

        if ($query) {
            // Apply filters
            if ($this->search) {
                $query->whereHas('business', function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%');
                });
            }

            if ($this->statusFilter !== 'all') {
                $query->where('status', $this->statusFilter);
            }

            // Apply sorting
            $query->orderBy($this->sortBy, $this->sortDirection);

            $this->projects = $query->with([
                'business',
                'plan',
                'clientUser',
                'accountManager',
                'leads' => function ($q) {
                    $q->where('submitted_at', '>=', Carbon::now()->subDays(30));
                }
            ])->get();
        }
    }

    public function loadProjectMetrics()
    {
        $this->isLoading = true;
        $analyticsService = app(TrackingAnalyticsService::class);
        $metrics = [];

        foreach ($this->projects as $project) {
            try {
                $projectMetrics = $analyticsService->getProjectMetrics($project, 'last_30_days');

                // Get recent leads count and value
                $recentLeads = $project->leads()
                    ->where('submitted_at', '>=', Carbon::now()->subDays(30))
                    ->get();

                $leadsValue = $recentLeads->where('status', 'closed')
                    ->whereNotNull('value')
                    ->sum('value');

                $metrics[$project->id] = [
                    'visitors' => $projectMetrics['unique_visitors'] ?? 0,
                    'calls' => $projectMetrics['phone_calls'] ?? 0,
                    'forms' => $projectMetrics['form_submissions'] ?? 0,
                    'total_leads' => $recentLeads->count(),
                    'valid_leads' => $recentLeads->where('is_valid', true)->count(),
                    'leads_value' => $leadsValue,
                    'conversion_rate' => ($projectMetrics['unique_visitors'] ?? 0) > 0
                        ? round((($projectMetrics['phone_calls'] ?? 0) + ($projectMetrics['form_submissions'] ?? 0)) / $projectMetrics['unique_visitors'] * 100, 1)
                        : 0,
                    'health_score' => $this->calculateHealthScore($projectMetrics, $recentLeads),
                ];
            } catch (\Exception $e) {
                Log::error('Error loading metrics for project ' . $project->id . ': ' . $e->getMessage());
                $metrics[$project->id] = $this->getEmptyMetrics();
            }
        }

        $this->projectMetrics = $metrics;
        $this->isLoading = false;
    }

    private function calculateHealthScore($metrics, $leads)
    {
        $score = 0;

        // Visitors score (40% weight)
        $visitors = $metrics['unique_visitors'] ?? 0;
        if ($visitors > 100) $score += 40;
        elseif ($visitors > 50) $score += 30;
        elseif ($visitors > 20) $score += 20;
        elseif ($visitors > 0) $score += 10;

        // Leads score (40% weight)
        $totalLeads = ($metrics['phone_calls'] ?? 0) + ($metrics['form_submissions'] ?? 0);
        if ($totalLeads > 20) $score += 40;
        elseif ($totalLeads > 10) $score += 30;
        elseif ($totalLeads > 5) $score += 20;
        elseif ($totalLeads > 0) $score += 10;

        // Recent activity score (20% weight)
        $recentActivity = $leads->where('submitted_at', '>=', Carbon::now()->subDays(7))->count();
        if ($recentActivity > 5) $score += 20;
        elseif ($recentActivity > 2) $score += 15;
        elseif ($recentActivity > 0) $score += 10;

        return min($score, 100);
    }

    private function getEmptyMetrics()
    {
        return [
            'visitors' => 0,
            'calls' => 0,
            'forms' => 0,
            'total_leads' => 0,
            'valid_leads' => 0,
            'leads_value' => 0,
            'conversion_rate' => 0,
            'health_score' => 0,
        ];
    }

    public function sortBy($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function getHealthScoreColor($score)
    {
        if ($score >= 80) return 'text-green-600';
        if ($score >= 60) return 'text-yellow-600';
        if ($score >= 40) return 'text-orange-600';
        return 'text-red-600';
    }

    public function getHealthScoreLabel($score)
    {
        if ($score >= 80) return 'Excellent';
        if ($score >= 60) return 'Good';
        if ($score >= 40) return 'Fair';
        return 'Needs Attention';
    }

    public function render()
    {
        return view('livewire.projects-table');
    }
}
