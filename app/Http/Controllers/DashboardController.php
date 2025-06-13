<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Lead;
use App\Services\Tracking\TrackingAnalyticsService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Get client's projects
        $projects = Project::where('user_id', $user->id)
            ->orWhereHas('business', function ($query) use ($user) {
                $query->where('client_id', $user->client_id);
            })
            ->with(['business', 'accountManager'])
            ->get();

        // Get recent leads
        $recentLeads = Lead::whereIn('project_id', $projects->pluck('id'))
            ->orderBy('submitted_at', 'desc')
            ->limit(5)
            ->get();

        return view('dashboard', compact('user', 'projects', 'recentLeads'));
    }
}
