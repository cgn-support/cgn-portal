<div>
    @if($isLoading)
        <div class="agency-card p-6">
            <div class="text-center text-gray-500">
                <flux:icon.arrow-path class="w-8 h-8 mx-auto mb-2 animate-spin" />
                <p>Loading completed tasks...</p>
            </div>
        </div>
    @elseif($errorMessage)
        <div class="agency-card p-6 bg-red-50 border-red-200">
            <div class="text-center text-red-600">
                <flux:icon.exclamation-triangle class="w-8 h-8 mx-auto mb-2" />
                <p class="font-medium">{{ $errorMessage }}</p>
            </div>
        </div>
    @elseif(empty($tasks))
        <div class="agency-card p-6">
            <div class="text-center text-gray-500">
                <flux:icon.check-circle class="w-12 h-12 mx-auto mb-2 opacity-50" />
                <p class="font-medium mb-1">No tasks completed this month</p>
                <p class="text-sm">Tasks will appear here when completed and marked to show in portal</p>
            </div>
        </div>
    @else
        <div class="space-y-4">
            @foreach($tasks as $task)
                <div class="agency-card p-4 hover:shadow-md transition-shadow duration-200">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <h4 class="font-semibold text-gray-900 mb-2">{{ $task['name'] }}</h4>
                            
                            <div class="flex items-center space-x-4 text-sm text-gray-600">
                                <div class="flex items-center space-x-1">
                                    <flux:icon.check-circle class="w-4 h-4 text-green-500" />
                                    <span class="font-medium text-green-700">{{ $task['status_text'] }}</span>
                                </div>
                                
                                @if($task['date_completed_text'] !== 'N/A')
                                    <div class="flex items-center space-x-1">
                                        <flux:icon.calendar class="w-4 h-4" />
                                        <span>{{ $task['date_completed_text'] }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                        
                        @if($task['deliverable_link'])
                            <div class="ml-4">
                                <a href="{{ $task['deliverable_link'] }}" 
                                   target="_blank" 
                                   class="agency-button-secondary text-sm px-4 py-2">
                                    <span class="flex items-center space-x-1">
                                        <flux:icon.document-arrow-down class="w-4 h-4" />
                                        <span>{{ $task['deliverable_name'] ?: 'View File' }}</span>
                                    </span>
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
        
        <div class="mt-4 text-center">
            <p class="text-sm text-gray-600">
                Showing {{ count($tasks) }} completed task{{ count($tasks) === 1 ? '' : 's' }} for 
                <span class="font-medium">{{ $report->report_month_name }} {{ $report->report_year }}</span>
            </p>
        </div>
    @endif
</div>