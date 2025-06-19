<?php

namespace App\Livewire;

use App\Models\Lead;
use App\Models\Project;
use Livewire\Component;
use Livewire\WithPagination;

class ProjectLeads extends Component
{
    use WithPagination;

    public Project $project;
    public $search = '';
    public $statusFilter = 'all';
    public $selectedLead = null;
    public $showLeadModal = false;
    public $leadValue = null;
    public $leadNotes = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => 'all'],
    ];

    public function mount(Project $project)
    {
        $this->project = $project;
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function getLeadsProperty()
    {
        $query = Lead::where('project_id', $this->project->id)
            ->latest('submitted_at');

        if ($this->search) {
            $query->where(function($q) {
                $q->whereJsonContains('payload->email', $this->search)
                  ->orWhereJsonContains('payload->name', $this->search)
                  ->orWhereJsonContains('payload->first_name', $this->search)
                  ->orWhereJsonContains('payload->last_name', $this->search)
                  ->orWhereJsonContains('payload->phone', $this->search);
            });
        }

        if ($this->statusFilter !== 'all') {
            $query->where('status', $this->statusFilter);
        }

        return $query->paginate(15);
    }

    public function getMetricsProperty()
    {
        $leads = Lead::where('project_id', $this->project->id);
        
        return [
            'total_leads' => $leads->count(),
            'valid_leads' => $leads->where('is_valid', true)->count(),
            'closed_leads' => $leads->where('status', 'closed')->count(),
            'total_value' => $leads->where('status', 'closed')->sum('value'),
            'avg_value' => $leads->where('status', 'closed')->avg('value') ?: 0,
        ];
    }

    public function markAsValid($leadId)
    {
        $lead = Lead::findOrFail($leadId);
        
        if ($lead->project_id !== $this->project->id) {
            return;
        }

        $lead->markAsValid();
        $this->dispatch('lead-updated');
    }

    public function markAsInvalid($leadId)
    {
        $lead = Lead::findOrFail($leadId);
        
        if ($lead->project_id !== $this->project->id) {
            return;
        }

        $lead->markAsInvalid();
        $this->dispatch('lead-updated');
    }

    public function openLeadModal($leadId)
    {
        $this->selectedLead = Lead::findOrFail($leadId);
        $this->leadValue = $this->selectedLead->value;
        $this->leadNotes = $this->selectedLead->notes;
        $this->showLeadModal = true;
    }

    public function closeLeadModal()
    {
        $this->selectedLead = null;
        $this->leadValue = null;
        $this->leadNotes = '';
        $this->showLeadModal = false;
    }

    public function markAsClosed()
    {
        if (!$this->selectedLead) {
            return;
        }

        $this->selectedLead->markAsClosed($this->leadValue);
        $this->selectedLead->update(['notes' => $this->leadNotes]);
        
        $this->closeLeadModal();
        $this->dispatch('lead-updated');
    }

    public function render()
    {
        return view('livewire.project-leads', [
            'leads' => $this->leads,
            'metrics' => $this->metrics,
        ]);
    }
}