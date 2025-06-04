<?php

namespace App\Livewire;

use App\Models\Business;
use App\Models\Project;
use App\Services\MondayApiService;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class ProjectDetails extends Component
{
    public $mondayPulseId;
    public $status;
    public $name;
    public $project;
    public $business;
    public $pulseData = [
        'accountManagerId' => null,
        'accountManagerName' => null,
        'accountManagerPhoto' => null,
        'projectComponent' => null
    ];
    public $projectName;

    public function mount(Project $project)
    {
        $this->project = $project;
        $this->mondayPulseId = $project->monday_pulse_id;
        $this->name = $project->name;
        $this->status = $project->portfolio_project_rag;
        $this->business = $this->project->business;

        $this->fetchMondayData();
    }

    protected function fetchMondayData()
    {
        if (!$this->mondayPulseId) {
            return;
        }

        try {
            // $mondayApiService = app(MondayApiService::class);

            // // Fetch all required data in a single method
            // $this->updateProjectData($mondayApiService);
        } catch (\Exception $e) {
            $this->handleMondayApiError($e, 'Error fetching Monday.com data');
        }
    }

    // protected function updateProjectData(MondayApiService $mondayApiService)
    // {
    //     // Fetch pulse data
    //     $pulseData = $mondayApiService->getPulseUpdatedData($this->mondayPulseId);
    //     $columnValues = collect($pulseData['data']['items'][0]['column_values'] ?? []);

    //     // Update project name
    //     $projectData = $mondayApiService->getProjectName($this->project->monday_board_id);
    //     $this->projectName = $projectData['data']['boards'][0]['name'] ?? null;

    //     // Update account manager info
    //     $this->updateAccountManagerInfo($pulseData, $columnValues, $mondayApiService);
    // }

    // protected function updateAccountManagerInfo($pulseData, $columnValues, MondayApiService $mondayApiService)
    // {
    //     if (!isset($pulseData['data']['items'][0]['column_values'][0]['value'])) {
    //         return;
    //     }

    //     $accountManagerData = $pulseData['data']['items'][0]['column_values'][0]['value'];
    //     $accountManagerDecoded = json_decode($accountManagerData, true);

    //     if (!isset($accountManagerDecoded['personsAndTeams'][0]['id'])) {
    //         return;
    //     }

    //     $this->pulseData['accountManagerId'] = $accountManagerDecoded['personsAndTeams'][0]['id'];
    //     $accountManagerInfo = $mondayApiService->getAccountManagerPhotoName($this->pulseData['accountManagerId']);
    //     $this->pulseData['accountManagerPhoto'] = $accountManagerInfo['data']['users'][0]['photo_small'] ?? null;
    //     $this->pulseData['accountManagerName'] = $accountManagerInfo['data']['users'][0]['name'] ?? 'Unknown';
    //     $this->pulseData['projectComponent'] = $columnValues->firstWhere('column.id', 'portfolio_project_step')['text'] ?? null;
    // }

    protected function handleMondayApiError(\Exception $e, string $context)
    {
        Log::error("$context: " . $e->getMessage());
        $this->pulseData['accountManagerName'] = 'Not Available';
    }

    public function render()
    {
        return view('livewire.project-details');
    }
}
