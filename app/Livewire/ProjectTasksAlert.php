<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Project;

class ProjectTasksAlert extends Component
{
    public $countOfAssignedTasks;
    public $uuid;

    public function mount($uuid)
    {
        $this->uuid = $uuid;
        $project = Project::where('id', $uuid)->first();
        $tasks = $project->tasks()->where('status', 'Assigned')->get();
        $this->countOfAssignedTasks = $tasks->count();
    }
    
    
    
    public function render()
    {
        return view('livewire.project-tasks-alert');
    }
}
