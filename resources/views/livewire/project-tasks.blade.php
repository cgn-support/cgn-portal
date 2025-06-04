<div>
    <div class="mb-4">
        <h3 class="text-lg font-medium text-neutral-900 dark:text-neutral-100">
            Project Tasks: {{ $project->name }}
        </h3>
        <p class="text-sm text-neutral-500 dark:text-neutral-400">
            (Monday Board ID: {{ $mondayProjectBoardId ?? 'Not Set' }})
        </p>
    </div>

    @if ($isLoading)
    <div class="flex justify-center items-center p-6 min-h-[200px]">
        <svg class="animate-spin h-8 w-8 text-primary-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <p class="ml-3 text-neutral-500 dark:text-neutral-400">Loading tasks...</p>
    </div>
    @elseif (!empty($errorMessage))
    <div class="bg-red-50 dark:bg-red-900/30 border border-red-300 dark:border-red-700 text-red-700 dark:text-red-300 px-4 py-3 rounded-lg relative" role="alert">
        <strong class="font-bold">Error!</strong>
        <span class="block sm:inline">{{ $errorMessage }}</span>
    </div>
    @elseif (empty($tasks))
    <div class="text-center py-8 border border-dashed border-neutral-300 dark:border-neutral-700 rounded-lg">
        <svg class="mx-auto h-12 w-12 text-neutral-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
            <path vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
        </svg>
        <h3 class="mt-2 text-sm font-medium text-neutral-900 dark:text-neutral-100">No tasks found</h3>
        <p class="mt-1 text-sm text-neutral-500 dark:text-neutral-400">No tasks are currently marked to be shown in the portal for this project.</p>
    </div>
    @else
    <div class="align-middle inline-block min-w-full shadow overflow-hidden sm:rounded-lg border border-neutral-200 dark:border-neutral-700">
        <table class="min-w-full divide-y divide-neutral-200 dark:divide-neutral-700">
            <thead class="bg-neutral-50 dark:bg-neutral-800">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-neutral-500 dark:text-neutral-300 uppercase tracking-wider">
                        Task Name
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-neutral-500 dark:text-neutral-300 uppercase tracking-wider">
                        Date Completed
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-neutral-500 dark:text-neutral-300 uppercase tracking-wider">
                        Deliverable
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-neutral-500 dark:text-neutral-300 uppercase tracking-wider">
                        Status
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-neutral-900 divide-y divide-neutral-200 dark:divide-neutral-700">
                @foreach ($tasks as $task)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-neutral-900 dark:text-neutral-100">
                        {{ $task['name'] }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-500 dark:text-neutral-400">
                        {{ $task['date_completed_text'] !== 'N/A' ? \Carbon\Carbon::parse($task['date_completed_text'])->format('M d, Y') : 'N/A' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-500 dark:text-neutral-400">
                        @if ($task['deliverable_link'])
                        <a href="{{ $task['deliverable_link'] }}" target="_blank" rel="noopener noreferrer" class="text-primary-600 dark:text-primary-400 hover:text-primary-800 dark:hover:text-primary-200 underline">
                            {{ $task['deliverable_name'] ?? 'View File' }}
                        </a>
                        @else
                        N/A
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        @php
                        $statusText = strtolower(trim($task['status_text'] ?? ''));
                        $statusClass = 'bg-neutral-100 text-neutral-800 dark:bg-neutral-700 dark:text-neutral-200'; // Default
                        if ($statusText === 'complete') {
                        $statusClass = 'bg-green-100 text-green-800 dark:bg-green-700 dark:text-green-100';
                        } elseif ($statusText === 'behind schedule') {
                        $statusClass = 'bg-red-600 text-white dark:bg-red-700 dark:text-red-100'; // More prominent for "Behind Schedule"
                        } elseif ($statusText === 'working on it') {
                        $statusClass = 'bg-yellow-100 text-yellow-800 dark:bg-yellow-600 dark:text-yellow-100';
                        } elseif ($statusText === 'stuck') {
                        $statusClass = 'bg-red-100 text-red-800 dark:bg-red-700 dark:text-red-100';
                        } elseif ($statusText === 'waiting on client') {
                        $statusClass = 'bg-purple-100 text-purple-800 dark:bg-purple-700 dark:text-purple-100';
                        } elseif ($statusText === 'not applicable') {
                        $statusClass = 'bg-slate-100 text-slate-800 dark:bg-slate-600 dark:text-slate-200';
                        }
                        @endphp
                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClass }}">
                            {{ $task['status_text'] ?? 'N/A' }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <div class="mt-6">
        <button wire:click="loadTasks" wire:loading.attr="disabled" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-orange-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 disabled:opacity-50">
            <svg wire:loading wire:target="loadTasks" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Refresh Tasks
        </button>
    </div>
</div>