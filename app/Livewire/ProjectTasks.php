<?php

namespace App\Livewire;

use App\Models\ClientTask;
use App\Models\Project;
use Livewire\Component;
use Livewire\WithPagination;

class ProjectTasks extends Component
{
    use WithPagination;

    public Project $project;
    public $statusFilter = 'all';
    public $selectedTask = null;
    public $showCompletionModal = false;
    public $completionNotes = '';

    protected $queryString = [
        'statusFilter' => ['except' => 'all'],
    ];

    public function mount(Project $project)
    {
        $this->project = $project;
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function getTasksProperty()
    {
        $query = ClientTask::where('project_id', $this->project->id)
            ->with(['presetTask', 'assignedBy'])
            ->latest('created_at');

        if ($this->statusFilter !== 'all') {
            switch ($this->statusFilter) {
                case 'pending':
                    $query->pending();
                    break;
                case 'completed':
                    $query->completed();
                    break;
                case 'overdue':
                    $query->overdue();
                    break;
            }
        }

        return $query->paginate(10);
    }

    public function getTaskSummaryProperty()
    {
        $tasks = ClientTask::where('project_id', $this->project->id);
        
        return [
            'total' => $tasks->count(),
            'pending' => $tasks->pending()->count(),
            'completed' => $tasks->completed()->count(),
            'overdue' => $tasks->overdue()->count(),
        ];
    }

    public function openCompletionModal($taskId)
    {
        $this->selectedTask = ClientTask::findOrFail($taskId);
        
        if ($this->selectedTask->project_id !== $this->project->id) {
            return;
        }

        $this->completionNotes = '';
        $this->showCompletionModal = true;
    }

    public function closeCompletionModal()
    {
        $this->selectedTask = null;
        $this->completionNotes = '';
        $this->showCompletionModal = false;
    }

    public function markTaskComplete()
    {
        if (!$this->selectedTask) {
            return;
        }

        $this->selectedTask->markAsCompleted($this->completionNotes);
        
        $this->closeCompletionModal();
        $this->dispatch('task-completed');
        
        session()->flash('success', 'Task marked as completed!');
    }

    public function render()
    {
        return view('livewire.project-tasks', [
            'tasks' => $this->tasks,
            'taskSummary' => $this->taskSummary,
        ]);
    }
}