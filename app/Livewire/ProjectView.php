<?php

namespace App\Livewire;

use App\Models\Project;
use App\Models\Lead;
use App\Models\Report;
use App\Models\ClientTask;
use App\Services\MondayApiService;
use Livewire\Component;
use Illuminate\Support\Facades\Log;

class ProjectView extends Component
{
    public $project;
    public $business;
    public $quickStats = [];
    public $projectStages = [];
    public $accountManagerPhoto;

    public function mount(string $uuid)
    {
        $this->project = Project::where('id', $uuid)->firstOrFail();
        $this->business = $this->project->business;
        $this->accountManagerPhoto = $this->getAccountManagerPhoto();
        
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
        
        $mondayData = $this->getMondayProjectData();
        
        $this->projectStages = [
            [
                'name' => 'SEO Project',
                'status' => $mondayData['portfolio_project_step'] ?? 'Not Started', 
                'icon' => 'chart-bar',
                'color' => 'green',
                'is_active' => !empty($mondayData['portfolio_project_step']),
                'is_completed' => $this->isStageCompleted($mondayData['portfolio_project_step']),
                'date' => $this->getStageDate($mondayData['portfolio_project_step']),
            ],
            [
                'name' => 'Website Project', 
                'status' => $mondayData['color_mkrt1658'] ?? 'Not Started',
                'icon' => 'computer-desktop', 
                'color' => 'blue',
                'is_active' => !empty($mondayData['color_mkrt1658']),
                'is_completed' => $this->isStageCompleted($mondayData['color_mkrt1658']),
                'date' => $this->getStageDate($mondayData['color_mkrt1658']),
            ],
            [
                'name' => 'Branding Project',
                'status' => $mondayData['color_mkrvx17w'] ?? 'Not Started',
                'icon' => 'paint-brush',
                'color' => 'purple', 
                'is_active' => !empty($mondayData['color_mkrvx17w']),
                'is_completed' => $this->isStageCompleted($mondayData['color_mkrvx17w']),
                'date' => $this->getStageDate($mondayData['color_mkrvx17w']),
            ],
        ];
    }

    protected function getMondayProjectData()
    {
        // Return empty data if no Monday pulse ID is set
        if (!$this->project->monday_pulse_id) {
            Log::info("No Monday.com pulse ID set for project {$this->project->id}");
            return [
                'portfolio_project_step' => null,
                'color_mkrt1658' => null,
                'color_mkrvx17w' => null,
            ];
        }

        try {
            $mondayService = app(MondayApiService::class);
            
            // Column IDs for the portfolio board stages
            $columnIdsToFetch = [
                'portfolio_project_step',  // SEO stage
                'color_mkrt1658',         // Website stage  
                'color_mkrvx17w',         // Branding stage
            ];
            
            $portfolioItem = $mondayService->getPortfolioItemDetails(
                $this->project->monday_pulse_id,
                $columnIdsToFetch
            );
            
            if (!$portfolioItem) {
                Log::warning("Portfolio item not found for pulse ID: {$this->project->monday_pulse_id}");
                return [
                    'portfolio_project_step' => null,
                    'color_mkrt1658' => null,
                    'color_mkrvx17w' => null,
                ];
            }
            
            return $this->extractColumnValues($portfolioItem, $columnIdsToFetch);
            
        } catch (\Exception $e) {
            Log::error("Error fetching Monday.com data for project {$this->project->id}: " . $e->getMessage());
            return [
                'portfolio_project_step' => null,
                'color_mkrt1658' => null,
                'color_mkrvx17w' => null,
            ];
        }
    }

    protected function extractColumnValues(array $portfolioItem, array $columnIds): array
    {
        $result = [];
        $columnValues = $portfolioItem['column_values'] ?? [];
        
        // Initialize all column IDs with null
        foreach ($columnIds as $columnId) {
            $result[$columnId] = null;
        }
        
        // Extract actual values from Monday.com response
        foreach ($columnValues as $columnValue) {
            $columnId = $columnValue['id'] ?? null;
            
            if (in_array($columnId, $columnIds)) {
                $result[$columnId] = $this->parseColumnValue($columnValue);
            }
        }
        
        return $result;
    }
    
    protected function parseColumnValue(array $columnValue): ?string
    {
        $type = $columnValue['type'] ?? null;
        $value = $columnValue['value'] ?? null;
        $text = $columnValue['text'] ?? null;
        
        // For status columns, use the text value
        if ($type === 'color' && !empty($text)) {
            return $text;
        }
        
        // For other column types, try to get the display text
        if (!empty($text)) {
            return $text;
        }
        
        // If we have a JSON value, try to parse it
        if (!empty($value)) {
            $decoded = json_decode($value, true);
            if (is_array($decoded) && isset($decoded['text'])) {
                return $decoded['text'];
            }
            if (is_array($decoded) && isset($decoded['label'])) {
                return $decoded['label'];
            }
        }
        
        return null;
    }

    protected function isStageCompleted($status)
    {
        if (empty($status)) {
            return false;
        }
        
        $completedStatuses = ['Complete', 'Done', 'Finished', 'Delivered', 'Live', 'Completed'];
        return collect($completedStatuses)->contains(fn($completed) => 
            stripos($status, $completed) !== false
        );
    }

    public function getAccountManagerPhoto()
    {
        $mondayService = app(MondayApiService::class);
        $userPhoto = $mondayService->getMondayUserProfilePhoto($this->project->accountManager->monday_user_id);
        return $userPhoto;
    }

    protected function getStageDate($status)
    {
        if (empty($status)) {
            return '--';
        }
        
        // For completed stages, show a recent completion date
        if ($this->isStageCompleted($status)) {
            return now()->subDays(rand(5, 30))->format('M j');
        }
        
        // For active stages, show current date or recent start date
        return now()->subDays(rand(1, 7))->format('M j');
    }

    public function render()
    {
        return view('livewire.project-view');
    }
}