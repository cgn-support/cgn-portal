<x-layouts.app :title="__('Dashboard')">
    <!-- Main Dashboard Container with proper spacing -->
    <div class="space-y-6 pb-8">

        <!-- Enhanced Welcome Section -->
        <div
            class="bg-gradient-to-r from-gray-50 to-orange-50 dark:from-gray-800 dark:to-gray-700 rounded-xl border border-blue-100 dark:border-gray-600 overflow-hidden">
            <div class="px-6 py-8 sm:px-8">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                    <!-- Welcome Content -->
                    <div class="flex items-center space-x-4">
                        <!-- User Avatar -->
                        <div class="flex-shrink-0">
                            <div
                                class="w-12 h-12 bg-gradient-to-br from-orange-500 to-orange-600 rounded-full flex items-center justify-center text-white font-semibold text-lg">
                                {{ $user->initials() }}
                            </div>
                        </div>

                        <!-- Greeting -->
                        <div>
                            <h1 class="gradient-heading">
                                @php
                                    $hour = now()->hour;
                                    $greeting =
                                        $hour < 12 ? 'Good morning' : ($hour < 17 ? 'Good afternoon' : 'Good evening');
                                @endphp
                                {{ $greeting }}, {{ ucfirst($user->name) }}
                            </h1>
                            <p class="text-gray-600 dark:text-gray-300 mt-1">
                                Here's what's happening with your projects today.
                            </p>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="mt-4 sm:mt-0 flex flex-col sm:flex-row gap-2">
                        @if ($projects->count() > 0)
                            <a href="{{ route('project', $projects->first()->id) }}"
                                class="inline-flex items-center px-4 py-2 bg-orange-600 hover:bg-blue-700 text-white text-sm font-medium rounded-full transition-colors duration-200 shadow">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                                View Latest Project
                            </a>
                        @endif
                        <a href="{{ route('support') }}"
                            class="inline-flex items-center px-4 py-2 bg-white hover:bg-gray-50 text-gray-700 text-sm font-medium rounded-full border border-gray-300 transition-colors duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192L5.636 18.364M12 12l2.828-2.828m0 0l2.829-2.829M12 12l-2.828 2.828m0 0l-2.829 2.829" />
                            </svg>
                            Get Support
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Project Performance Metrics -->
        @if ($projects->count() > 0)
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
                <div class="px-6 py-6 sm:px-8">
                    <livewire:dashboard-stats :projects="$projects" />
                </div>
            </div>
        @endif

        <!-- Projects Section -->
        <div
            class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
            <div class="px-6 py-6 sm:px-8">
                <livewire:projects-table />
            </div>
        </div>

        <!-- Global Notifications -->
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
            <div class="px-6 py-6 sm:px-8">
                <div class="flex items-center mb-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="w-5 h-5 text-blue-500 mr-3" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 17h5l-5 5-5-5h5v-13h-5l5-5 5 5h-5v13z" />
                            </svg>
                        </div>
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Latest Updates</h2>
                    </div>
                </div>
                <livewire:global-notifications />
            </div>
        </div>

    </div>
</x-layouts.app>
