<div class="space-y-6">
    <!-- Filters -->
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
        <h3 class="text-lg font-bold text-neutral-900 dark:text-neutral-100">Project Performance</h3>

        <div class="flex flex-col sm:flex-row gap-3">
            <!-- Date Range Filter -->
            <select wire:model.live="selectedDateRange"
                class="block w-full sm:w-auto bg-white dark:bg-neutral-700 text-neutral-900 dark:text-neutral-200 
                           border border-neutral-300 dark:border-neutral-600 rounded-md
                           py-2 pl-3 pr-8 text-sm
                           focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                <option value="today">Today</option>
                <option value="last_7_days">Last 7 Days</option>
                <option value="last_30_days">Last 30 Days</option>
                <option value="this_month">This Month</option>
            </select>

            <!-- Project Filter -->
            <select wire:model.live="selectedProjectId"
                class="block w-full sm:w-auto bg-white dark:bg-neutral-700 text-neutral-900 dark:text-neutral-200 
                           border border-neutral-300 dark:border-neutral-600 rounded-md
                           py-2 pl-3 pr-8 text-sm
                           focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                <option value="all">All Projects</option>
                @foreach ($projects as $project)
                    <option value="{{ $project->id }}">{{ $project->business->name ?? $project->display_name }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- Loading State -->
    <div wire:loading.flex wire:target="loadStats" class="items-center justify-center py-8">
        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary-600"></div>
        <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">Loading metrics...</span>
    </div>

    <!-- Stats Grid -->
    <div wire:loading.remove wire:target="loadStats" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
        <!-- Visitors -->
        <div class="bg-white dark:bg-neutral-800 rounded-lg p-6 border border-neutral-200 dark:border-neutral-700">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                        </path>
                    </svg>
                </div>
                <div class="ml-3 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Visitors</dt>
                        <dd class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                            {{ number_format($visitorCount) }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <!-- Phone Calls -->
        <div class="bg-white dark:bg-neutral-800 rounded-lg p-6 border border-neutral-200 dark:border-neutral-700">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z">
                        </path>
                    </svg>
                </div>
                <div class="ml-3 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Phone Calls</dt>
                        <dd class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                            {{ number_format($callCount) }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <!-- Form Submissions -->
        <div class="bg-white dark:bg-neutral-800 rounded-lg p-6 border border-neutral-200 dark:border-neutral-700">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-6 w-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                        </path>
                    </svg>
                </div>
                <div class="ml-3 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Form Submissions</dt>
                        <dd class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                            {{ number_format($formCount) }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <!-- Conversion Rate -->
        <div class="bg-white dark:bg-neutral-800 rounded-lg p-6 border border-neutral-200 dark:border-neutral-700">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                        </path>
                    </svg>
                </div>
                <div class="ml-3 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Conversion Rate</dt>
                        <dd class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $conversionRate }}%</dd>
                    </dl>
                </div>
            </div>
        </div>

        <!-- Total Lead Value -->
        <div class="bg-white dark:bg-neutral-800 rounded-lg p-6 border border-neutral-200 dark:border-neutral-700">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1">
                        </path>
                    </svg>
                </div>
                <div class="ml-3 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Lead Value</dt>
                        <dd class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                            ${{ number_format($totalLeadsValue, 2) }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</div>
