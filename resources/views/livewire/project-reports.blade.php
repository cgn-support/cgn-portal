<div>
    <!-- Header with Year Filter -->
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="gradient-heading">Marketing Reports</h1>
            <p class="text-gray-600 mt-2">View and access your monthly marketing performance reports</p>
        </div>
        
        @if(count($availableYears) > 1)
            <div class="flex items-center space-x-3">
                <label for="year-select" class="text-sm font-medium text-gray-700">Year:</label>
                <select wire:model.live="selectedYear" id="year-select" 
                    class="rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500">
                    @foreach($availableYears as $year)
                        <option value="{{ $year }}">{{ $year }}</option>
                    @endforeach
                </select>
            </div>
        @endif
    </div>

    @if(empty($availableYears))
        <!-- No Reports State -->
        <div class="text-center py-16">
            <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <h3 class="mt-4 text-lg font-medium text-gray-900">No reports available yet</h3>
            <p class="mt-2 text-gray-500">Your marketing reports will appear here once they're published by your account manager.</p>
        </div>
    @else
        <!-- Sequential Reports Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach($monthlyReports as $monthData)
                <div class="agency-card p-6 text-center relative {{ $monthData['report'] ? 'hover:shadow-lg cursor-pointer' : 'opacity-60' }} transition-all duration-200">
                    @if($monthData['report'])
                        <!-- Published Report -->
                        <a href="{{ route('project.report', ['uuid' => $project->id, 'report_id' => $monthData['report']->id]) }}" 
                           class="block">
                            <div class="mb-4">
                                <div class="w-16 h-16 bg-gradient-to-br from-orange-400 to-orange-600 rounded-full mx-auto flex items-center justify-center">
                                    <span class="text-white font-bold text-lg">{{ $monthData['report_number'] }}</span>
                                </div>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">Report {{ $monthData['report_number'] }}</h3>
                            <p class="text-sm text-gray-600 mb-3">{{ $monthData['name'] }}</p>
                            
                            <!-- Report Status Badge -->
                            <div class="flex items-center justify-center mb-3">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                    Published
                                </span>
                            </div>
                            
                            <!-- View Button -->
                            <div class="bg-orange-500 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-orange-600 transition-colors">
                                View Report
                            </div>
                            
                            <!-- Published Date -->
                            <p class="text-xs text-gray-500 mt-2">
                                Published {{ $monthData['report']->created_at->format('M j, Y') }}
                            </p>
                        </a>
                    @else
                        <!-- Unpublished/Upcoming Report -->
                        <div class="mb-4">
                            <div class="w-16 h-16 bg-gray-200 rounded-full mx-auto flex items-center justify-center">
                                <span class="text-gray-400 font-bold text-lg">{{ $monthData['report_number'] }}</span>
                            </div>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-500 mb-2">Report {{ $monthData['report_number'] }}</h3>
                        <p class="text-sm text-gray-500 mb-3">{{ $monthData['name'] }}</p>
                        
                        @if($monthData['is_current_month'])
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 mb-3">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                                </svg>
                                Current Month
                            </span>
                        @elseif($monthData['is_future_month'])
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600 mb-3">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                                </svg>
                                Upcoming
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 mb-3">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                                In Progress
                            </span>
                        @endif
                        
                        <div class="bg-gray-100 text-gray-500 px-4 py-2 rounded-md text-sm font-medium cursor-not-allowed">
                            Not Available
                        </div>
                    @endif
                </div>
            @endforeach
        </div>

        <!-- Year Summary -->
        @php
            $publishedCount = collect($monthlyReports)->filter(fn($m) => $m['report'])->count();
            $totalReports = count($monthlyReports);
        @endphp
        
        <div class="mt-12 text-center">
            <div class="inline-flex items-center px-4 py-2 bg-gray-50 rounded-full">
                <svg class="w-5 h-5 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
                <span class="text-sm font-medium text-gray-600">
                    {{ $publishedCount }} of {{ $totalReports }} reports published for {{ $selectedYear }}
                </span>
            </div>
        </div>
    @endif
</div>
