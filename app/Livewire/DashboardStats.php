<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log; // For logging
use Illuminate\Database\Eloquent\Collection; // Import Collection

class DashboardStats extends Component
{
    public Collection $projects; // Typehint if you are sure it's always a collection
    public string $selectedProjectId = 'all'; // Default to 'all', use string for value consistency

    // Stats properties
    public $formCount = 0;
    public $callCount = 0;
    public $visitorCount = 0;

    public function mount(Collection $projects) // Typehint $projects
    {
        $this->projects = $projects;
        // Initial load can be based on 'all' or the first project if available
        $this->loadStatsForProject($this->selectedProjectId);
    }

    /**
     * This is a Livewire lifecycle hook that runs when a public property is updated.
     * When $selectedProjectId changes, this method will be called.
     */
    public function updatedSelectedProjectId(string $value): void
    {
        // $value is the new selected project ID
        $this->selectedProjectId = $value; // Ensure it's updated if not using .live
        $this->loadStatsForProject($this->selectedProjectId);
    }

    public function loadStatsForProject(string $projectId): void
    {
        Log::info("Loading stats for Project ID: " . $projectId);

        // TODO: Implement your actual data fetching logic based on $projectId
        // If $projectId is 'all', fetch aggregated stats.
        // If $projectId is a specific ID, filter your stats for that project.

        // Example placeholder logic:
        if ($projectId === 'all') {
            $this->formCount = Cache::remember('dashboard.formCount.all', 60, function () {
                // Replace with actual logic for all projects
                return 120; // Example aggregate
            });
            $this->callCount = Cache::remember('dashboard.callCount.all', 60, function () {
                return 70; // Example aggregate
            });
            $this->visitorCount = Cache::remember('dashboard.visitorCount.all', 60, function () {
                return 1450; // Example aggregate
            });
        } else {
            // Specific project selected - use $projectId to fetch data
            $this->formCount = Cache::remember("dashboard.formCount.project.{$projectId}", 60, function () use ($projectId) {
                // Replace with actual logic for specific project (e.g., Lead::where('project_id', $projectId)->count())
                return rand(5, 20); // Example for specific project
            });
            $this->callCount = Cache::remember("dashboard.callCount.project.{$projectId}", 60, function () use ($projectId) {
                return rand(1, 10); // Example for specific project
            });
            $this->visitorCount = Cache::remember("dashboard.visitorCount.project.{$projectId}", 60, function () use ($projectId) {
                return rand(50, 200); // Example for specific project
            });
        }
    }

    // This method is called by wire:init and wire:poll if you keep them,
    // but the primary stats loading should now happen via updatedSelectedProjectId
    // or mount. You might rename it or adjust its role.
    // For simplicity, I've integrated its logic into loadStatsForProject.
    // public function loadStats()
    // {
    //    $this->loadStatsForProject($this->selectedProjectId);
    // }


    public function render()
    {
        return view('livewire.dashboard-stats');
    }
}
