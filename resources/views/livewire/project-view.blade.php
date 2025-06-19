<div class="min-h-screen dark:bg-gray-900">
    <!-- Header Section -->
    <div class="bg-white dark:bg-gray-800 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex items-center justify-between">
                <!-- Account Manager Info -->
                <div class="flex items-center space-x-4">
                    <div class="flex-shrink-0">
                        <img class="h-12 w-12 rounded-full"
                            src="https://files.monday.com/use1/photos/55207138/small/55207138-user_photo_2024_01_30_15_45_21.png?1706629521"
                            alt="{{ $project->accountManager->name ?? 'Account Manager' }}">
                    </div>
                    <div>
                        <h1 class="text-xl font-bold text-gray-900 dark:text-white">
                            {{ $project->accountManager->name ?? 'manager' }}
                        </h1>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Your CGN Account Manager since August 25, 2020
                        </p>
                    </div>
                </div>

                <!-- Project Info -->
                <div class="text-right">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Project Start Date</p>
                    <p class="text-lg font-semibold text-gray-900 dark:text-white">
                        {{ $project->project_start_date ? \Carbon\Carbon::parse($project->project_start_date)->format('F j, Y') : 'June 11, 2025' }}
                    </p>
                </div>

                <!-- Action Buttons -->
                <div class="flex space-x-3">
                    <a href="https://slack.com" target="_blank"
                        class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Chat With Team
                    </a>
                    <a href="https://www.local-marketing-reports.com/location-dashboard/c567222026bcf219cee102a98dc43fd811bdd61c/summary"
                        target="_blank"
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-orange-600 hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">
                        My Local SEO
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Project Performance Metrics -->
    <div class="bg-white dark:bg-gray-800">
        <div class="px-6 py-6 sm:px-8">
            <livewire:project-stats :project="$project" />
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        <!-- Three Column Layout -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">

            <!-- Project Information Card -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Project Information</h3>
                <dl class="space-y-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Website</dt>
                        <dd class="mt-1">
                            <a href="{{ $project->project_url ?? 'https://wordpress.test' }}"
                                class="text-indigo-600 hover:text-indigo-500 text-sm">
                                {{ $project->project_url ?? 'https://wordpress.test' }}
                            </a>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Address</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                            @if ($business)
                                {{ $business->address_line1 }}
                                @if ($business->address_line2)
                                    , {{ $business->address_line2 }}
                                @endif
                                <br>{{ $business->city }}, {{ $business->state }} {{ $business->zip_code }}
                            @else
                                //
                            @endif
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Phone Number</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                            {{ $business->phone_number ?? '' }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Maps Link</dt>
                        <dd class="mt-1">
                            @if ($business && $business->google_maps_url)
                                <a href="{{ $business->gbp_link }}"
                                    class="text-indigo-600 hover:text-indigo-500 text-sm">
                                    View on Maps
                                </a>
                            @endif
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Success Goal</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                            {{ $business->project_goal ?? 'Shared Success Goal' }}
                        </dd>
                    </div>
                </dl>
            </div>

            <!-- Quick Stats Card -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Quick Stats</h3>
                <dl class="space-y-4">
                    <div class="flex justify-between">
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Tasks</dt>
                        <dd class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $quickStats['tasks'] }}
                        </dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Pending Tasks</dt>
                        <dd class="text-sm font-semibold text-orange-600">{{ $quickStats['pending_tasks'] }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Leads</dt>
                        <dd class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $quickStats['leads'] }}
                        </dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Valid Leads</dt>
                        <dd class="text-sm font-semibold text-green-600">{{ $quickStats['valid_leads'] }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Reports</dt>
                        <dd class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $quickStats['reports'] }}
                        </dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Lead Value</dt>
                        <dd class="text-sm font-semibold text-green-600">
                            ${{ number_format($quickStats['total_lead_value'], 0) }}</dd>
                    </div>
                </dl>
            </div>

            <!-- Project Timeline Card -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Project Timeline</h3>
                <div class="space-y-4">
                    @foreach ($projectStages as $stage)
                        <div class="flex items-center space-x-3">
                            <!-- Status Icon -->
                            <div class="flex-shrink-0">
                                @if ($stage['is_completed'])
                                    <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    </div>
                                @elseif($stage['is_active'])
                                    <div
                                        class="w-8 h-8 bg-{{ $stage['color'] }}-500 rounded-full flex items-center justify-center">
                                        <div class="w-3 h-3 bg-white rounded-full animate-pulse"></div>
                                    </div>
                                @else
                                    <div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center">
                                        <div class="w-3 h-3 bg-gray-500 rounded-full"></div>
                                    </div>
                                @endif
                            </div>

                            <!-- Stage Info -->
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $stage['name'] }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $stage['status'] }}</p>
                            </div>

                            <!-- Date -->
                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                {{ $stage['date'] }}
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Action Buttons Row -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <a href="{{ route('project.tasks', ['uuid' => $project->id]) }}"
                class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                    </path>
                </svg>
                View Tasks
            </a>

            <a href="{{ route('project.leads', ['uuid' => $project->id]) }}"
                class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                    </path>
                </svg>
                Manage Leads
            </a>

            <a href="{{ route('project.reports', ['uuid' => $project->id]) }}"
                class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                    </path>
                </svg>
                View Reports
            </a>

            <a href="{{ route('project.map', ['uuid' => $project->id ?? 'default-uuid']) }}"
                class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-orange-600 hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                    </path>
                </svg>
                Project Gantt Chart
            </a>
        </div>

        <!-- Collapsible Attachments Section -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm" x-data="{ showAttachments: false }">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <button @click="showAttachments = !showAttachments"
                    class="flex items-center justify-between w-full text-left">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13">
                            </path>
                        </svg>
                        Attachments (2)
                    </h3>
                    <svg x-show="!showAttachments" class="w-5 h-5 text-gray-400" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7">
                        </path>
                    </svg>
                    <svg x-show="showAttachments" class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7">
                        </path>
                    </svg>
                </button>
            </div>

            <div x-show="showAttachments" class="px-6 py-4">
                <ul class="space-y-3">
                    <li class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13">
                                </path>
                            </svg>
                            <span class="text-sm text-gray-900 dark:text-gray-100">marketing_contract.pdf</span>
                        </div>
                        <a href="#"
                            class="text-indigo-600 hover:text-indigo-500 text-sm font-medium">Download</a>
                    </li>
                    <li class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13">
                                </path>
                            </svg>
                            <span class="text-sm text-gray-900 dark:text-gray-100">project_start_ticket.pdf</span>
                        </div>
                        <a href="#"
                            class="text-indigo-600 hover:text-indigo-500 text-sm font-medium">Download</a>
                    </li>
                </ul>

                <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <a href="#"
                        class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-gray-700">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2v0"></path>
                        </svg>
                        Open Google Drive Folder
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
