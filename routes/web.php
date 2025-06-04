<?php

use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use Illuminate\Support\Facades\Route;
use App\Models\Project;
use App\Models\Report;


Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Route::get('dashboard', function () {
        $user = auth()->user();
        $projects = null; // Default to null or empty collection

        // Ensure user is authenticated and the method exists before calling
        if ($user && method_exists($user, 'companyProjects')) {
            $projects = $user->companyProjects()->get(); // <-- Execute the query here
        } else {
            $projects = collect(); // Pass an empty collection if no user or method
            // Or handle error/redirect as appropriate
        }

        return view('dashboard', ['projects' => $projects, 'user' => $user]);
    })->name('dashboard');

    Route::get('settings/profile', Profile::class)->name('settings.profile');
    Route::get('settings/password', Password::class)->name('settings.password');
    Route::get('settings/appearance', Appearance::class)->name('settings.appearance');

    Route::get('project/{uuid}', function (string $uuid) {
        $project = Project::where('id', $uuid)->firstOrFail();
        return view('project', ['uuid' => $uuid, 'project' => $project]);
    })->name('project');

    Route::get('project/{uuid}/updates', function ($uuid) {
        $project = Project::where('id', $uuid)->firstOrFail();
        return view('updates', compact('project'));
    });

    Route::get('project/{uuid}/tasks', function ($uuid) {
        $project = Project::where('id', $uuid)->firstOrFail();
        return view('tasks', compact('project'));
    });

    Route::get('project/{uuid}/users', function ($uuid) {
        $project = Project::where('id', $uuid)->firstOrFail();
        return view('user-log', compact('project'))->name('project');
    });

    Route::get('/support', function () {
        return view('support');
    })->name('support');

    Route::get('project/{uuid}/content', function ($uuid) {
        $project = Project::where('id', $uuid)->firstOrFail();
        return view('content', compact('project'));
    })->name('project.content');

    Route::get('project/{uuid}/my-map', function ($uuid) {
        $project = Project::where('id', $uuid)->firstOrFail();
        return view('my-map', compact('project'));
    })->name('project.map');

    Route::get('project/{uuid}/leads', function ($uuid) {
        $project = Project::where('id', $uuid)->firstOrFail();
        return view('leads', compact('project'));
    })->name('project.leads');

    Route::get('project/{uuid}/reports', function ($uuid) {
        $project = Project::where('id', $uuid)->firstOrFail();
        return view('reports', compact('project'));
    })->name('project.reports');

    Route::get('project/{uuid}/reports/{report_id}', function ($uuid, $report_id) {
        $project = Project::where('id', $uuid)->firstOrFail();
        $report = Report::where('id', $report_id)->firstOrFail();
        return view('report', ['report' => $report, 'project' => $project]);
    })->name('project.report');

    Route::get('project/{uuid}/reports/{report_id}/download', function ($uuid, $report_id) {
        $report = Report::where('id', $report_id)->firstOrFail();
        return response()->download(public_path($report->file_path));
    })->name('project.report.download');

    Route::get('project/{uuid}/gantt', function ($uuid) {
        $project = Project::where('id', $uuid)->firstOrFail();
        return view('gantt', compact('project'));
    })->name('project.gantt');
});

require __DIR__ . '/auth.php';
