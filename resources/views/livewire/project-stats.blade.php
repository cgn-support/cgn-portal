<div class="space-y-6">
    <!-- Header and Filters -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div>
            <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Project Performance</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Track your marketing metrics and keyword rankings
            </p>
        </div>

        <div class="flex flex-col sm:flex-row gap-3">
            <!-- Date Range Filter -->
            <select wire:model.live="selectedDateRange"
                class="block w-full sm:w-auto bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 
                       border border-gray-300 dark:border-gray-600 rounded-lg
                       py-2.5 pl-3 pr-10 text-sm
                       focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                       transition-colors duration-200">
                <option value="today">Today</option>
                <option value="last_7_days">Last 7 Days</option>
                <option value="last_30_days">Last 30 Days</option>
                <option value="this_month">This Month</option>
            </select>
        </div>
    </div>

    <!-- Loading State -->
    <div wire:loading.flex wire:target="loadStats" class="items-center justify-center py-12">
        <div class="flex items-center space-x-3">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
            <span class="text-sm text-gray-600 dark:text-gray-400">Loading metrics...</span>
        </div>
    </div>

    <!-- Stats Grid -->
    <div wire:loading.remove wire:target="loadStats" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">

        <!-- Phone Calls -->
        <div
            class="relative bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div
                                class="w-10 h-10 bg-green-100 dark:bg-green-900/20 rounded-lg flex items-center justify-center">
                                <svg class="h-5 w-5 text-green-600 dark:text-green-400" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z">
                                    </path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4 flex-1">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Phone Calls</p>
                            <div class="flex items-baseline space-x-2">
                                <p class="text-2xl font-bold text-gray-900 dark:text-white">
                                    {{ number_format($callCount) }}</p>
                                @php
                                    $callChange = $this->getPercentageChange($callCount, $previousCallCount);
                                    $callTrend = $this->getTrendDirection($callCount, $previousCallCount);
                                @endphp
                                @if ($callChange != 0)
                                    <div
                                        class="flex items-center text-sm {{ $callTrend === 'up' ? 'text-green-600' : ($callTrend === 'down' ? 'text-red-600' : 'text-gray-500') }}">
                                        @if ($callTrend === 'up')
                                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        @elseif($callTrend === 'down')
                                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M14.707 10.293a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L9 12.586V5a1 1 0 012 0v7.586l2.293-2.293a1 1 0 011.414 0z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        @endif
                                        <span class="font-medium">{{ abs($callChange) }}%</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Submissions -->
        <div
            class="relative bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div
                                class="w-10 h-10 bg-orange-100 dark:bg-orange-900/20 rounded-lg flex items-center justify-center">
                                <svg class="h-5 w-5 text-orange-600 dark:text-orange-400" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                    </path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4 flex-1">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Leads</p>
                            <div class="flex items-baseline space-x-2">
                                <p class="text-2xl font-bold text-gray-900 dark:text-white">
                                    {{ number_format($formCount) }}</p>
                                @php
                                    $formChange = $this->getPercentageChange($formCount, $previousFormCount);
                                    $formTrend = $this->getTrendDirection($formCount, $previousFormCount);
                                @endphp
                                @if ($formChange != 0)
                                    <div
                                        class="flex items-center text-sm {{ $formTrend === 'up' ? 'text-green-600' : ($formTrend === 'down' ? 'text-red-600' : 'text-gray-500') }}">
                                        @if ($formTrend === 'up')
                                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        @elseif($formTrend === 'down')
                                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M14.707 10.293a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L9 12.586V5a1 1 0 012 0v7.586l2.293-2.293a1 1 0 011.414 0z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        @endif
                                        <span class="font-medium">{{ abs($formChange) }}%</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Keywords in Top 3 -->
        <div
            class="relative bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div
                                class="w-10 h-10 bg-purple-100 dark:bg-purple-900/20 rounded-lg flex items-center justify-center">
                                <svg class="h-5 w-5 text-purple-600 dark:text-purple-400" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 713.138-3.138z">
                                    </path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4 flex-1">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Top 3 Keywords</p>
                            <div class="flex items-baseline space-x-2">
                                <p class="text-2xl font-bold text-gray-900 dark:text-white">
                                    {{ number_format($keywordsInTop3) }}</p>
                                @php
                                    $top3Change = $this->getPercentageChange($keywordsInTop3, $previousKeywordsInTop3);
                                    $top3Trend = $this->getTrendDirection($keywordsInTop3, $previousKeywordsInTop3);
                                @endphp
                                @if ($top3Change != 0)
                                    <div
                                        class="flex items-center text-sm {{ $top3Trend === 'up' ? 'text-green-600' : ($top3Trend === 'down' ? 'text-red-600' : 'text-gray-500') }}">
                                        @if ($top3Trend === 'up')
                                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        @elseif($top3Trend === 'down')
                                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M14.707 10.293a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L9 12.586V5a1 1 0 012 0v7.586l2.293-2.293a1 1 0 011.414 0z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        @endif
                                        <span class="font-medium">{{ abs($top3Change) }}%</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Keywords in Top 10 -->
        <div
            class="relative bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div
                                class="w-10 h-10 bg-yellow-100 dark:bg-yellow-900/20 rounded-lg flex items-center justify-center">
                                <svg class="h-5 w-5 text-yellow-600 dark:text-yellow-400" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6">
                                    </path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4 flex-1">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Top 10 Keywords</p>
                            <div class="flex items-baseline space-x-2">
                                <p class="text-2xl font-bold text-gray-900 dark:text-white">
                                    {{ number_format($keywordsInTop10) }}</p>
                                @php
                                    $top10Change = $this->getPercentageChange(
                                        $keywordsInTop10,
                                        $previousKeywordsInTop10,
                                    );
                                    $top10Trend = $this->getTrendDirection($keywordsInTop10, $previousKeywordsInTop10);
                                @endphp
                                @if ($top10Change != 0)
                                    <div
                                        class="flex items-center text-sm {{ $top10Trend === 'up' ? 'text-green-600' : ($top10Trend === 'down' ? 'text-red-600' : 'text-gray-500') }}">
                                        @if ($top10Trend === 'up')
                                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        @elseif($top10Trend === 'down')
                                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M14.707 10.293a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L9 12.586V5a1 1 0 012 0v7.586l2.293-2.293a1 1 0 011.414 0z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        @endif
                                        <span class="font-medium">{{ abs($top10Change) }}%</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
