<?php

namespace App\Livewire;

use App\Models\Project;
use App\Services\MondayApiService;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Illuminate\Database\Eloquent\Collection; // For type hinting $project->tasks if you were to store them

class ProjectTasks extends Component
{
    public Project $project; // The current Laravel Project model
    public ?string $mondayProjectBoardId = null;
    public array $tasks = [];
    public bool $isLoading = true;
    public string $errorMessage = '';

    // Monday.com specific configuration
    protected string $showInPortalColumnId = 'color_mkrh753c'; // Your "Show In Portal" column
    protected string $showInPortalTextValue = 'yes';
    protected array $taskDetailColumnIdsToFetch = [];

    public function mount(Project $project): void
    {
        $this->project = $project;
        $this->mondayProjectBoardId = $project->monday_board_id;

        // Define which columns you want to display for each task
        $this->taskDetailColumnIdsToFetch = [
            'status_mkmx2j1q', // Status
            'date_mknpykdq',   // Date Completed
            'file_mkpdh120',   // Deliverable (File column)
            // Add other column IDs for task details you want to fetch here
        ];

        $this->loadTasks();
    }

    public function loadTasks(): void
    {
        $this->isLoading = true;
        $this->errorMessage = '';
        $this->tasks = []; // Reset tasks before loading

        if (empty($this->mondayProjectBoardId)) {
            $this->errorMessage = 'Monday.com Project Board ID is not set for this project.';
            Log::warning("ProjectTasks: Monday Board ID missing for project ID {$this->project->id}");
            $this->isLoading = false;
            return;
        }

        if (empty($this->taskDetailColumnIdsToFetch)) {
            $this->taskDetailColumnIdsToFetch = ['status_mkmx2j1q'];
            Log::warning("ProjectTasks: taskDetailColumnIdsToFetch was empty in loadTasks, re-initialized with default status column.");
        }


        try {
            $mondayApiService = app(MondayApiService::class);
            // getTasksToShowInPortal already filters by the "Show In Portal" column and value
            $fetchedTasksToShowInPortal = $mondayApiService->getTasksToShowInPortal(
                $this->mondayProjectBoardId,
                $this->showInPortalColumnId,
                $this->taskDetailColumnIdsToFetch,
                $this->showInPortalTextValue
            );

            $processedTasks = [];
            if (is_array($fetchedTasksToShowInPortal)) {
                foreach ($fetchedTasksToShowInPortal as $task) {
                    $taskColumnValues = $task['column_values'] ?? [];

                    $statusColumnData = $mondayApiService->getColumnDataById($taskColumnValues, 'status_mkmx2j1q');
                    $statusText = isset($statusColumnData['text']) ? trim($statusColumnData['text']) : null;

                    // Apply the additional filter: Skip if status is "Not Applicable"
                    if ($statusText && strtolower($statusText) === 'not applicable') {
                        Log::info("ProjectTasks: Skipping task ID {$task['id']} ('{$task['name']}') because its status is 'Not Applicable'.");
                        continue; // Skip this task
                    }

                    $dateCompletedColumnData = $mondayApiService->getColumnDataById($taskColumnValues, 'date_mknpykdq');
                    $deliverableColumnData = $mondayApiService->getColumnDataById($taskColumnValues, 'file_mkpdh120');

                    $deliverableLink = null;
                    $deliverableName = null;
                    if ($deliverableColumnData && isset($deliverableColumnData['value']) && is_string($deliverableColumnData['value'])) {
                        $fileValue = json_decode($deliverableColumnData['value'], true);
                        if (json_last_error() === JSON_ERROR_NONE && isset($fileValue['files'][0])) {
                            $deliverableLink = $fileValue['files'][0]['public_url'] ?? $deliverableColumnData['text'] ?? null;
                            $deliverableName = $fileValue['files'][0]['name'] ?? 'View File';
                        }
                    }
                    if (!$deliverableLink && $deliverableColumnData && isset($deliverableColumnData['text'])) {
                        $deliverableLink = $deliverableColumnData['text'];
                        $deliverableName = $deliverableName ?: 'View File';
                    }

                    $processedTasks[] = [
                        'id' => $task['id'] ?? null,
                        'name' => $task['name'] ?? 'Unnamed Task',
                        'status_text' => $statusText ?? 'N/A',
                        'date_completed_text' => $dateCompletedColumnData['text'] ?? 'N/A',
                        'deliverable_link' => $deliverableLink,
                        'deliverable_name' => $deliverableName,
                    ];
                }
                $this->tasks = $processedTasks;
            } else {
                Log::warning("ProjectTasks: Fetched tasks was not an array for Monday Board ID {$this->mondayProjectBoardId}.");
                $this->tasks = [];
                $this->errorMessage = 'Received unexpected data format while fetching tasks.';
            }

            Log::info("ProjectTasks: Successfully processed and filtered " . count($this->tasks) . " tasks for Monday Board ID {$this->mondayProjectBoardId}");
        } catch (\Exception $e) {
            $this->errorMessage = 'Failed to load tasks from Monday.com. Please try again later.';
            Log::error("ProjectTasks: Error fetching tasks for Monday Board ID {$this->mondayProjectBoardId}: " . $e->getMessage(), ['exception' => $e]);
        } finally {
            $this->isLoading = false;
        }
    }

    public function render()
    {
        return view('livewire.project-tasks');
    }
}
