<?php

namespace App\Livewire;

use App\Models\Project;
use Livewire\Component;

class ProjectView extends Component
{
    public $project;

    public function mount(string $uuid)
    {
        $this->project = Project::where('id', $uuid)->firstOrFail();
    }

    public function render()
    {
        return view('livewire.project-view');
    }
}
