<div class="space-y-6">
    <!-- Header with filters -->
    <div class="flex flex-col p-6 lg:flex-row lg:items-center lg:justify-between gap-4">
        <div>
            <h3 class="text-lg font-bold text-neutral-900 dark:text-neutral-100">Manage Projects</h3>
            <p class="text-sm text-gray-600 dark:text-gray-400">Manage and monitor your marketing projects</p>
        </div>

        <!-- View Toggle -->
        <div class="flex items-center gap-2">
            <button wire:click="$set('viewMode', 'table')"
                class="p-2 rounded {{ $viewMode === 'table' ? 'bg-primary-100 text-primary-700' : 'text-gray-400 hover:text-gray-600' }}">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z">
                    </path>
                </svg>
            </button>
            <button wire:click="$set('viewMode', 'cards')"
                class="p-2 rounded {{ $viewMode === 'cards' ? 'bg-primary-100 text-primary-700' : 'text-gray-400 hover:text-gray-600' }}">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path
                        d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zM11 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zM11 13a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z">
                    </path>
                </svg>
            </button>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white dark:bg-neutral-800 rounded-lg p-4 border border-neutral-200 dark:border-neutral-700">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- Search -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Search Projects</label>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search by business name..."
                    class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-primary-500 focus:ring-primary-500">
            </div>

            <!-- Status Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                <select wire:model.live="statusFilter"
                    class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-primary-500 focus:ring-primary-500">
                    <option value="all">All Statuses</option>
                    <option value="active">Active</option>
                    <option value="paused">Paused</option>
                    <option value="completed">Completed</option>
                    <option value="archived">Archived</option>
                </select>
            </div>

            <!-- Sort -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Sort By</label>
                <select wire:model.live="sortBy"
                    class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-primary-500 focus:ring-primary-500">
                    <option value="updated_at">Last Updated</option>
                    <option value="created_at">Created Date</option>
                    <option value="status">Status</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Loading State -->
    <div wire:loading.flex wire:target="loadProjectMetrics" class="items-center justify-center py-8">
        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary-600"></div>
        <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">Loading project data...</span>
    </div>

    <!-- Table View -->
    @if ($viewMode === 'table')
        <div wire:loading.remove wire:target="loadProjectMetrics"
            class="bg-white dark:bg-neutral-800 rounded-lg border border-neutral-200 dark:border-neutral-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-900">
                        <tr>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Project
                            </th>
                            <th
                                class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Health
                            </th>
                            <th
                                class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Visitors (30d)
                            </th>
                            <th
                                class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Leads (30d)
                            </th>
                            <th
                                class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Conv. Rate
                            </th>
                            <th
                                class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Lead Value
                            </th>
                            <th
                                class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-neutral-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($projects as $project)
                            @php
                                $metrics = $projectMetrics[$project->id] ?? $this->getEmptyMetrics();
                            @endphp
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div
                                                class="h-10 w-10 rounded-lg bg-gradient-to-br from-primary-500 to-primary-600 flex items-center justify-center">
                                                <span class="text-white font-bold text-sm">
                                                    {{ substr($project->business->name ?? 'P', 0, 2) }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                {{ $project->business->name ?? 'Unnamed Project' }}
                                            </div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                                <span
                                                    class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium 
                                                   {{ $project->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                                    {{ ucfirst($project->status) }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex items-center justify-center">
                                        <div
                                            class="text-sm font-medium {{ $this->getHealthScoreColor($metrics['health_score']) }}">
                                            {{ $metrics['health_score'] }}%
                                        </div>
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $this->getHealthScoreLabel($metrics['health_score']) }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center text-sm text-gray-900 dark:text-gray-100">
                                    {{ number_format($metrics['visitors']) }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                        {{ number_format($metrics['total_leads']) }}
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ number_format($metrics['valid_leads']) }} valid
                                    </div>
                                </td>
                                <td
                                    class="px-6 py-4 text-center text-sm font-medium 
                                   {{ $metrics['conversion_rate'] >= 3 ? 'text-green-600' : ($metrics['conversion_rate'] >= 1 ? 'text-yellow-600' : 'text-red-600') }}">
                                    {{ $metrics['conversion_rate'] }}%
                                </td>
                                <td class="px-6 py-4 text-center text-sm font-medium text-green-600">
                                    ${{ number_format($metrics['leads_value'], 2) }}
                                </td>
                                <td class="px-6 py-4 text-right text-sm font-medium space-x-2">
                                    <a href="{{ '/project/' . $project->id }}"
                                        class="inline-flex items-center px-3 py-1.5 bg-primary-600 hover:bg-primary-500 text-white text-sm font-medium rounded-md transition-colors">
                                        Manage
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 48 48">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M34 40h10v-4a6 6 0 00-10.712-3.714M34 40H14m20 0v-4a9.971 9.971 0 00-.712-3.714M14 40H4v-4a6 6 0 0110.712-3.714M14 40v-4a9.971 9.971 0 01.712-3.714M28 16a4 4 0 11-8 0 4 4 0 018 0zm-8 12a6 6 0 00-6 6v2h12v-2a6 6 0 00-6-6z">
                                        </path>
                                    </svg>
                                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">No projects
                                        found</h3>
                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                        @if ($search || $statusFilter !== 'all')
                                            Try adjusting your filters to see more projects.
                                        @else
                                            You don't have any projects yet.
                                        @endif
                                    </p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- Cards View -->
    @if ($viewMode === 'cards')
        <div wire:loading.remove wire:target="loadProjectMetrics"
            class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($projects as $project)
                @php
                    $metrics = $projectMetrics[$project->id] ?? $this->getEmptyMetrics();
                @endphp
                <div
                    class="bg-white dark:bg-neutral-800 rounded-lg border border-neutral-200 dark:border-neutral-700 p-6 hover:shadow-lg transition-shadow">
                    <!-- Project Header -->
                    <div class="flex items-center mb-4">
                        <div class="flex-shrink-0 h-12 w-12">
                            <div
                                class="h-12 w-12 rounded-lg bg-gradient-to-br from-primary-500 to-primary-600 flex items-center justify-center">
                                <span class="text-white font-bold">
                                    {{ substr($project->business->name ?? 'P', 0, 2) }}
                                </span>
                            </div>
                        </div>
                        <div class="ml-3 flex-1">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 truncate">
                                {{ $project->business->name ?? 'Unnamed Project' }}
                            </h3>
                            <div class="flex items-center gap-2">
                                <span
                                    class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium 
                                   {{ $project->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ ucfirst($project->status) }}
                                </span>
                                <span
                                    class="text-xs {{ $this->getHealthScoreColor($metrics['health_score']) }} font-medium">
                                    {{ $this->getHealthScoreLabel($metrics['health_score']) }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Metrics Grid -->
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                                {{ number_format($metrics['visitors']) }}
                            </div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">Visitors</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-green-600">
                                {{ number_format($metrics['total_leads']) }}
                            </div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">Leads</div>
                        </div>
                        <div class="text-center">
                            <div
                                class="text-lg font-semibold {{ $metrics['conversion_rate'] >= 3 ? 'text-green-600' : 'text-gray-600' }}">
                                {{ $metrics['conversion_rate'] }}%
                            </div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">Conv. Rate</div>
                        </div>
                        <div class="text-center">
                            <div class="text-lg font-semibold text-green-600">
                                ${{ number_format($metrics['leads_value']) }}
                            </div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">Lead Value</div>
                        </div>
                    </div>

                    <!-- Action Button -->
                    <a href="{{ '/project/' . $project->id }}"
                        class="w-full inline-flex justify-center items-center px-4 py-2 bg-primary-600 hover:bg-primary-500 text-white text-sm font-medium rounded-md transition-colors">
                        Manage Project
                    </a>
                </div>
            @empty
                <div class="col-span-full text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor"
                        viewBox="0 0 48 48">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M34 40h10v-4a6 6 0 00-10.712-3.714M34 40H14m20 0v-4a9.971 9.971 0 00-.712-3.714M14 40H4v-4a6 6 0 0110.712-3.714M14 40v-4a9.971 9.971 0 01.712-3.714M28 16a4 4 0 11-8 0 4 4 0 018 0zm-8 12a6 6 0 00-6 6v2h12v-2a6 6 0 00-6-6z">
                        </path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">No projects found</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        @if ($search || $statusFilter !== 'all')
                            Try adjusting your filters to see more projects.
                        @else
                            You don't have any projects yet.
                        @endif
                    </p>
                </div>
            @endforelse
        </div>
    @endif
</div>
