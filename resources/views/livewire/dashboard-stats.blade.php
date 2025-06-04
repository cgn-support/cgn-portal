<div wire:poll.60s class="space-y-8"> {{-- wire:init="loadStats" can be removed if mount handles initial load --}}
    <div class="flex justify-between items-center">
        <h3 class="text-lg font-lg text-neutral-900 font-bold dark:text-neutral-100">Project Metrics</h3>
        <div>
            <label for="project_selector" class="sr-only">Select Project</label>
            <select
                id="project_selector"
                wire:model.live="selectedProjectId"
                class="block w-full max-w-xs bg-white dark:bg-neutral-700 text-neutral-900 dark:text-neutral-200 
                    border border-neutral-300 dark:border-neutral-600 rounded-md
                    py-2 pl-3 pr-10 
                    text-sm leading-5
                    focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500
                    transition duration-150 ease-in-out
                    cursor-pointer
                    appearance-none">
                <option value="all">All Projects</option>
                @if ($projects && $projects->count() > 0) {{-- Check if $projects is not null and has items --}}
                @foreach ($projects as $project)
                {{-- Ensure $project->display_name accessor exists and is working --}}
                <option value="{{ $project->id }}">{{ $project->display_name ?? $project->name }}</option>
                @endforeach
                @else
                <option value="" disabled>No projects available</option>
                @endif
            </select>
        </div>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
        <x-metric-card :value="$formCount" label="Form Submissions" icon="mail" />
        <x-metric-card :value="$callCount" label="Phone Calls" icon="phone" />
        <x-metric-card :value="$visitorCount" label="Unique Visitors" icon="users" />
    </div>
</div>