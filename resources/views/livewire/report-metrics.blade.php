<div>
    <div class="flex items-center justify-between mb-6">
        <div>
            <h3 class="text-xl font-semibold text-gray-800">Marketing Metrics</h3>
            <p class="text-gray-600">The most important KPIs for your marketing campaign are below.</p>
        </div>
        @if($trackingAvailable)
            <button 
                wire:click="refreshMetrics" 
                wire:loading.attr="disabled"
                class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 disabled:opacity-50"
            >
                <svg wire:loading.remove wire:target="refreshMetrics" class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                <svg wire:loading wire:target="refreshMetrics" class="animate-spin w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Refresh Data
            </button>
        @endif
    </div>

    @if(!$trackingAvailable)
        <div class="bg-yellow-50 border border-yellow-200 rounded-md p-4 mb-6">
            <div class="flex">
                <svg class="w-5 h-5 text-yellow-400 mr-3 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                </svg>
                <div>
                    <p class="text-sm text-yellow-800">
                        <strong>Limited Tracking Available:</strong> Some automated metrics may not be available for this project. Manual metrics can still be entered via the admin panel.
                    </p>
                </div>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6" wire:loading.class="opacity-50" wire:target="refreshMetrics">
        <!-- Row 1: Traffic & Engagement -->
        <div class="agency-card p-6 text-center">
            <h4 class="text-sm font-medium text-gray-600 mb-2">Organic Sessions</h4>
            <p class="text-3xl font-bold text-gray-900">{{ number_format($metrics['organic_sessions'] ?? 0) }}</p>
            @if($trackingAvailable)
                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-green-100 text-green-800 mt-2">
                    <span class="w-2 h-2 bg-green-400 rounded-full mr-1"></span>
                    Auto-tracked
                </span>
            @endif
        </div>
        
        <div class="agency-card p-6 text-center">
            <h4 class="text-sm font-medium text-gray-600 mb-2">Contact Button Users</h4>
            <p class="text-3xl font-bold text-gray-900">{{ number_format($metrics['contact_button_users'] ?? 0) }}</p>
            @if($trackingAvailable)
                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-green-100 text-green-800 mt-2">
                    <span class="w-2 h-2 bg-green-400 rounded-full mr-1"></span>
                    Auto-tracked
                </span>
            @endif
        </div>
        
        <div class="agency-card p-6 text-center">
            <h4 class="text-sm font-medium text-gray-600 mb-2">Form Submissions</h4>
            <p class="text-3xl font-bold text-gray-900">{{ number_format($metrics['form_submissions'] ?? 0) }}</p>
            @if($trackingAvailable)
                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-green-100 text-green-800 mt-2">
                    <span class="w-2 h-2 bg-green-400 rounded-full mr-1"></span>
                    Auto-tracked
                </span>
            @endif
        </div>

        <!-- Row 2: Phone Calls -->
        <div class="agency-card p-6 text-center">
            <h4 class="text-sm font-medium text-gray-600 mb-2">Web Phone Calls</h4>
            <p class="text-3xl font-bold text-gray-900">{{ number_format($metrics['web_phone_calls'] ?? 0) }}</p>
            @if($trackingAvailable)
                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-green-100 text-green-800 mt-2">
                    <span class="w-2 h-2 bg-green-400 rounded-full mr-1"></span>
                    Auto-tracked
                </span>
            @endif
        </div>
        
        <div class="agency-card p-6 text-center">
            <h4 class="text-sm font-medium text-gray-600 mb-2">GBP Phone Calls</h4>
            <p class="text-3xl font-bold text-gray-900">{{ number_format($metrics['gbp_phone_calls'] ?? 0) }}</p>
            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-blue-100 text-blue-800 mt-2">
                <span class="w-2 h-2 bg-blue-400 rounded-full mr-1"></span>
                Manual entry
            </span>
        </div>
        
        <div class="agency-card p-6 text-center">
            <h4 class="text-sm font-medium text-gray-600 mb-2">GBP Listing Clicks</h4>
            <p class="text-3xl font-bold text-gray-900">{{ number_format($metrics['gbp_listing_clicks'] ?? 0) }}</p>
            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-blue-100 text-blue-800 mt-2">
                <span class="w-2 h-2 bg-blue-400 rounded-full mr-1"></span>
                Manual entry
            </span>
        </div>

        <!-- Row 3: Additional Metrics -->
        <div class="agency-card p-6 text-center">
            <h4 class="text-sm font-medium text-gray-600 mb-2">GBP Booking Clicks</h4>
            <p class="text-3xl font-bold text-gray-900">{{ number_format($metrics['gbp_booking_clicks'] ?? 0) }}</p>
            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-blue-100 text-blue-800 mt-2">
                <span class="w-2 h-2 bg-blue-400 rounded-full mr-1"></span>
                Manual entry
            </span>
        </div>
        
        <div class="agency-card p-6 text-center">
            <h4 class="text-sm font-medium text-gray-600 mb-2">Total Citations</h4>
            <p class="text-3xl font-bold text-gray-900">{{ number_format($metrics['total_citations'] ?? 0) }}</p>
            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-blue-100 text-blue-800 mt-2">
                <span class="w-2 h-2 bg-blue-400 rounded-full mr-1"></span>
                Manual entry
            </span>
        </div>
        
        <div class="agency-card p-6 text-center">
            <h4 class="text-sm font-medium text-gray-600 mb-2">Total Reviews</h4>
            <p class="text-3xl font-bold text-gray-900">{{ number_format($metrics['total_reviews'] ?? 0) }}</p>
            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-blue-100 text-blue-800 mt-2">
                <span class="w-2 h-2 bg-blue-400 rounded-full mr-1"></span>
                Manual entry
            </span>
        </div>
    </div>

    <!-- Month-over-Month Comparisons -->
    <div class="mt-8 bg-gray-50 rounded-lg p-6">
        <h4 class="text-lg font-semibold text-gray-800 mb-4">Month-over-Month Change</h4>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <!-- Total Calls -->
            <div class="text-center">
                <div class="flex items-center justify-center mb-1">
                    @if(($comparisons['calls']['direction'] ?? 'unchanged') === 'increase')
                        <svg class="w-4 h-4 text-green-500 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M3.293 9.707a1 1 0 010-1.414l6-6a1 1 0 011.414 0l6 6a1 1 0 01-1.414 1.414L11 5.414V17a1 1 0 11-2 0V5.414L4.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                        </svg>
                        <p class="text-lg font-bold text-green-600">{{ $comparisons['calls']['display'] ?? '0%' }}</p>
                    @elseif(($comparisons['calls']['direction'] ?? 'unchanged') === 'decrease')
                        <svg class="w-4 h-4 text-red-500 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 10.293a1 1 0 010 1.414l-6 6a1 1 0 01-1.414 0l-6-6a1 1 0 111.414-1.414L9 14.586V3a1 1 0 012 0v11.586l4.293-4.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                        <p class="text-lg font-bold text-red-600">{{ $comparisons['calls']['display'] ?? '0%' }}</p>
                    @else
                        <p class="text-lg font-bold text-gray-500">{{ $comparisons['calls']['display'] ?? '0%' }}</p>
                    @endif
                </div>
                <p class="text-sm text-gray-600">Total Calls</p>
            </div>

            <!-- Form Submissions -->
            <div class="text-center">
                <div class="flex items-center justify-center mb-1">
                    @if(($comparisons['form_submissions']['direction'] ?? 'unchanged') === 'increase')
                        <svg class="w-4 h-4 text-green-500 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M3.293 9.707a1 1 0 010-1.414l6-6a1 1 0 011.414 0l6 6a1 1 0 01-1.414 1.414L11 5.414V17a1 1 0 11-2 0V5.414L4.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                        </svg>
                        <p class="text-lg font-bold text-green-600">{{ $comparisons['form_submissions']['display'] ?? '0%' }}</p>
                    @elseif(($comparisons['form_submissions']['direction'] ?? 'unchanged') === 'decrease')
                        <svg class="w-4 h-4 text-red-500 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 10.293a1 1 0 010 1.414l-6 6a1 1 0 01-1.414 0l-6-6a1 1 0 111.414-1.414L9 14.586V3a1 1 0 012 0v11.586l4.293-4.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                        <p class="text-lg font-bold text-red-600">{{ $comparisons['form_submissions']['display'] ?? '0%' }}</p>
                    @else
                        <p class="text-lg font-bold text-gray-500">{{ $comparisons['form_submissions']['display'] ?? '0%' }}</p>
                    @endif
                </div>
                <p class="text-sm text-gray-600">Form Submissions</p>
            </div>

            <!-- Total Citations -->
            <div class="text-center">
                <div class="flex items-center justify-center mb-1">
                    @if(($comparisons['total_citations']['direction'] ?? 'unchanged') === 'increase')
                        <svg class="w-4 h-4 text-green-500 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M3.293 9.707a1 1 0 010-1.414l6-6a1 1 0 011.414 0l6 6a1 1 0 01-1.414 1.414L11 5.414V17a1 1 0 11-2 0V5.414L4.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                        </svg>
                        <p class="text-lg font-bold text-green-600">{{ $comparisons['total_citations']['display'] ?? '0%' }}</p>
                    @elseif(($comparisons['total_citations']['direction'] ?? 'unchanged') === 'decrease')
                        <svg class="w-4 h-4 text-red-500 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 10.293a1 1 0 010 1.414l-6 6a1 1 0 01-1.414 0l-6-6a1 1 0 111.414-1.414L9 14.586V3a1 1 0 012 0v11.586l4.293-4.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                        <p class="text-lg font-bold text-red-600">{{ $comparisons['total_citations']['display'] ?? '0%' }}</p>
                    @else
                        <p class="text-lg font-bold text-gray-500">{{ $comparisons['total_citations']['display'] ?? '0%' }}</p>
                    @endif
                </div>
                <p class="text-sm text-gray-600">Total Citations</p>
            </div>

            <!-- Total Reviews -->
            <div class="text-center">
                <div class="flex items-center justify-center mb-1">
                    @if(($comparisons['total_reviews']['direction'] ?? 'unchanged') === 'increase')
                        <svg class="w-4 h-4 text-green-500 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M3.293 9.707a1 1 0 010-1.414l6-6a1 1 0 011.414 0l6 6a1 1 0 01-1.414 1.414L11 5.414V17a1 1 0 11-2 0V5.414L4.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                        </svg>
                        <p class="text-lg font-bold text-green-600">{{ $comparisons['total_reviews']['display'] ?? '0%' }}</p>
                    @elseif(($comparisons['total_reviews']['direction'] ?? 'unchanged') === 'decrease')
                        <svg class="w-4 h-4 text-red-500 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 10.293a1 1 0 010 1.414l-6 6a1 1 0 01-1.414 0l-6-6a1 1 0 111.414-1.414L9 14.586V3a1 1 0 012 0v11.586l4.293-4.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                        <p class="text-lg font-bold text-red-600">{{ $comparisons['total_reviews']['display'] ?? '0%' }}</p>
                    @else
                        <p class="text-lg font-bold text-gray-500">{{ $comparisons['total_reviews']['display'] ?? '0%' }}</p>
                    @endif
                </div>
                <p class="text-sm text-gray-600">Total Reviews</p>
            </div>
        </div>
        <p class="text-xs text-gray-500 mt-4 text-center">Compared to previous month</p>
    </div>
</div>