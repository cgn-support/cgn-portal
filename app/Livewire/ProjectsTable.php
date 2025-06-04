<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Project;
use App\Models\User; // Assuming User model is in App\Models
use Illuminate\Database\Eloquent\Collection; // For type hinting
use Illuminate\Support\Facades\Log;

class ProjectsTable extends Component
{
    public Collection $projects; // Initialize as an Eloquent Collection

    public function mount()
    {
        $user = auth()->user();
        $this->projects = new Collection(); // Default to an empty collection

        if (!$user) {
            // Should not happen if route is protected by auth middleware, but good to check
            return;
        }

        // Handle different roles - you'll need to adjust role names as per your Spatie setup
        if ($user->hasRole('client_user')) {
            if ($user->client) { // Check if the client relationship exists
                // Fetch all business IDs belonging to this client's company
                $businessIds = $user->client->businesses()->pluck('id'); // Use businesses() relationship method

                if ($businessIds->isNotEmpty()) {
                    // Load projects for those businesses, eager-loading the business relation
                    $this->projects = Project::whereIn('business_id', $businessIds)
                        ->with(['business', 'plan', 'clientUser', 'accountManager']) // Eager load relevant data
                        ->latest('updated_at')
                        ->get();
                }
            } else {
                // Client user has no client company assigned, so no projects via company
                Log::warning("Client user ID {$user->id} has no client company assigned.");
            }
        } elseif ($user->hasRole('account_manager')) {
            // Account Manager sees projects they are assigned to
            if (method_exists($user, 'managedProjects')) {
                $this->projects = $user->managedProjects()
                    ->with(['business', 'plan', 'clientUser', 'accountManager'])
                    ->latest('updated_at')
                    ->get();
            }
        } elseif ($user->hasRole('admin')) {
            // Admin sees all projects (or a relevant subset)
            $this->projects = Project::with(['business', 'plan', 'clientUser', 'accountManager'])
                ->latest('updated_at')
                ->get(); // Consider pagination for admins: ->paginate(10);
        }
        // Add other role conditions if necessary
    }

    public function render()
    {
        return view('livewire.projects-table');
    }
}
