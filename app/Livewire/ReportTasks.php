<?php

namespace App\Livewire;

use App\Models\Project;
use App\Models\Report;
use App\Services\MondayApiService;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Carbon\Carbon;

class ReportTasks extends Component
{
    public Project $project;
    public Report $report;
    public ?string $mondayProjectBoardId = null;
    public array $tasks = [];
    public bool $isLoading = true;
    public string $errorMessage = '';
    public string $reportMonth = '';
    public string $reportYear = '';

    // Monday.com specific configuration
    protected string $showInPortalColumnId = 'color_mkrh753c'; // Your "Show In Portal" column
    protected string $showInPortalTextValue = 'yes';
    protected array $taskDetailColumnIdsToFetch = [];

    public function mount(Report $report): void
    {
        $this->report = $report;
        $this->project = $report->project;
        $this->mondayProjectBoardId = $this->project->monday_board_id;
        $this->reportMonth = str_pad($report->report_month, 2, '0', STR_PAD_LEFT);
        $this->reportYear = (string) $report->report_year;

        // Define which columns you want to display for each task
        $this->taskDetailColumnIdsToFetch = [
            'status_mkmx2j1q', // Status
            'date_mknpykdq',   // Date Completed
            'file_mkpdh120',   // Deliverable (File column)
        ];

        $this->loadTasks();
    }

    public function loadTasks(): void
    {
        $this->isLoading = true;
        $this->errorMessage = '';
        $this->tasks = [];

        if (empty($this->mondayProjectBoardId)) {
            $this->errorMessage = 'Monday.com Project Board ID is not set for this project.';
            Log::warning("ReportTasks: Monday Board ID missing for project ID {$this->project->id}");
            $this->isLoading = false;
            return;
        }

        try {
            $mondayApiService = app(MondayApiService::class);
            
            // Get tasks that are marked to show in portal
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

                    // Filter 1: Must be completed status
                    if (!$statusText || strtolower($statusText) !== 'complete') {
                        continue;
                    }

                    $dateCompletedColumnData = $mondayApiService->getColumnDataById($taskColumnValues, 'date_mknpykdq');
                    $dateCompletedText = $dateCompletedColumnData['text'] ?? null;

                    // Filter 2: Must have completion date in the report month/year
                    if (!$this->isTaskCompletedInReportMonth($dateCompletedText)) {
                        continue;
                    }

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
                        'status_text' => $statusText,
                        'date_completed_text' => $dateCompletedText ?? 'N/A',
                        'deliverable_link' => $deliverableLink,
                        'deliverable_name' => $deliverableName,
                    ];
                }
                $this->tasks = $processedTasks;
            } else {
                Log::warning("ReportTasks: Fetched tasks was not an array for Monday Board ID {$this->mondayProjectBoardId}.");
                $this->tasks = [];
                $this->errorMessage = 'Received unexpected data format while fetching tasks.';
            }

            Log::info("ReportTasks: Successfully processed " . count($this->tasks) . " completed tasks for {$this->reportMonth}/{$this->reportYear} from Monday Board ID {$this->mondayProjectBoardId}");
        } catch (\Exception $e) {
            $this->errorMessage = 'Failed to load tasks from Monday.com. Please try again later.';
            Log::error("ReportTasks: Error fetching tasks for Monday Board ID {$this->mondayProjectBoardId}: " . $e->getMessage(), ['exception' => $e]);
        } finally {
            $this->isLoading = false;
        }
    }

    /**
     * Check if a task completion date falls within the report month/year
     */
    private function isTaskCompletedInReportMonth(?string $dateCompletedText): bool
    {
        if (empty($dateCompletedText) || $dateCompletedText === 'N/A') {
            return false;
        }

        try {
            // Parse the date from Monday.com (could be various formats)
            $completedDate = Carbon::parse($dateCompletedText);
            
            // Check if it falls within the report month/year
            return $completedDate->format('m') === $this->reportMonth && 
                   $completedDate->format('Y') === $this->reportYear;
        } catch (\Exception $e) {
            Log::warning("ReportTasks: Could not parse completion date '{$dateCompletedText}': " . $e->getMessage());
            return false;
        }
    }

    public function render()
    {
        return view('livewire.report-tasks');
    }
}