<?php

namespace App\Livewire;

use App\Models\Project;
use App\Models\Lead;
use App\Models\Report;
use App\Models\ClientTask;
use Livewire\Component;

class ProjectViewImproved extends Component
{
    public $project;
    public $business;
    public $quickStats = [];
    public $projectStages = [];

    public function mount(string $uuid)
    {
        $this->project = Project::where('id', $uuid)->firstOrFail();
        $this->business = $this->project->business;
        
        $this->loadQuickStats();
        $this->loadProjectStages();
    }

    protected function loadQuickStats()
    {
        $this->quickStats = [
            'tasks' => ClientTask::where('project_id', $this->project->id)->count(),
            'pending_tasks' => ClientTask::where('project_id', $this->project->id)->pending()->count(),
            'leads' => Lead::where('project_id', $this->project->id)->count(),
            'valid_leads' => Lead::where('project_id', $this->project->id)->valid()->count(),
            'reports' => Report::where('project_id', $this->project->id)->where('status', 'sent')->count(),
            'total_lead_value' => Lead::where('project_id', $this->project->id)->where('status', 'closed')->sum('value') ?? 0,
        ];
    }

    protected function loadProjectStages()
    {
        // These column IDs map to Monday.com Portfolio board columns:
        // portfolio_project_step = SEO stage
        // color_mkrt1658 = Website stage  
        // color_mkrvx17w = Branding stage
        
        // For now using mock data - would integrate with Monday.com API
        $mondayData = $this->getMondayProjectData();
        
        $this->projectStages = [
            [
                'name' => 'SEO Project',
                'status' => $mondayData['portfolio_project_step'] ?? 'Strategy', 
                'icon' => 'chart-bar',
                'color' => 'green',
                'is_active' => true,
                'is_completed' => $this->isStageCompleted($mondayData['portfolio_project_step'] ?? ''),
                'date' => 'Sep 22',
            ],
            [
                'name' => 'Website Project', 
                'status' => $mondayData['color_mkrt1658'] ?? 'Building',
                'icon' => 'computer-desktop', 
                'color' => 'blue',
                'is_active' => true,
                'is_completed' => $this->isStageCompleted($mondayData['color_mkrt1658'] ?? ''),
                'date' => 'Sep 28',
            ],
            [
                'name' => 'Branding Project',
                'status' => $mondayData['color_mkrvx17w'] ?? 'Designing',
                'icon' => 'paint-brush',
                'color' => 'purple', 
                'is_active' => !empty($mondayData['color_mkrvx17w']),
                'is_completed' => $this->isStageCompleted($mondayData['color_mkrvx17w'] ?? ''),
                'date' => 'Oct 4',
            ],
        ];
    }

    protected function getMondayProjectData()
    {
        // Mock data - in real implementation, this would call Monday.com API
        // using the project's monday_pulse_id to get column values
        return [
            'portfolio_project_step' => 'Strategy Complete',
            'color_mkrt1658' => 'Building',
            'color_mkrvx17w' => 'Designing',
        ];
    }

    protected function isStageCompleted($status)
    {
        $completedStatuses = ['Complete', 'Done', 'Finished', 'Delivered', 'Live'];
        return collect($completedStatuses)->contains(fn($completed) => 
            stripos($status, $completed) !== false
        );
    }

    public function render()
    {
        return view('livewire.project-view-improved');
    }
}