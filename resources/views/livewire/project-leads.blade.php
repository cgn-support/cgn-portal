<div>
    <!-- Header -->
    <div class="mb-8">
        <h1 class="gradient-heading">Lead Management</h1>
        <p class="text-gray-600 mt-2">Track and manage your marketing leads to understand campaign performance and ROI</p>
    </div>

    <!-- Metrics Dashboard -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-6 mb-8">
        <!-- Total Leads -->
        <div class="agency-card p-6 text-center">
            <div class="mb-2">
                <div class="w-12 h-12 bg-gradient-to-br from-blue-400 to-blue-600 rounded-full mx-auto flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
            </div>
            <div class="text-2xl font-bold text-gray-900">{{ number_format($metrics['total_leads']) }}</div>
            <div class="text-sm text-gray-600">Total Leads</div>
        </div>

        <!-- Valid Leads -->
        <div class="agency-card p-6 text-center">
            <div class="mb-2">
                <div class="w-12 h-12 bg-gradient-to-br from-green-400 to-green-600 rounded-full mx-auto flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            <div class="text-2xl font-bold text-gray-900">{{ number_format($metrics['valid_leads']) }}</div>
            <div class="text-sm text-gray-600">Valid Leads</div>
        </div>

        <!-- Closed Leads -->
        <div class="agency-card p-6 text-center">
            <div class="mb-2">
                <div class="w-12 h-12 bg-gradient-to-br from-purple-400 to-purple-600 rounded-full mx-auto flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                </div>
            </div>
            <div class="text-2xl font-bold text-gray-900">{{ number_format($metrics['closed_leads']) }}</div>
            <div class="text-sm text-gray-600">Closed Leads</div>
        </div>

        <!-- Total Value -->
        <div class="agency-card p-6 text-center">
            <div class="mb-2">
                <div class="w-12 h-12 bg-gradient-to-br from-yellow-400 to-yellow-600 rounded-full mx-auto flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                    </svg>
                </div>
            </div>
            <div class="text-2xl font-bold text-gray-900">${{ number_format($metrics['total_value'], 0) }}</div>
            <div class="text-sm text-gray-600">Total Lead Value</div>
        </div>

        <!-- Average Value -->
        <div class="agency-card p-6 text-center">
            <div class="mb-2">
                <div class="w-12 h-12 bg-gradient-to-br from-orange-400 to-orange-600 rounded-full mx-auto flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                    </svg>
                </div>
            </div>
            <div class="text-2xl font-bold text-gray-900">${{ number_format($metrics['avg_value'], 0) }}</div>
            <div class="text-sm text-gray-600">Avg. Lead Value</div>
        </div>
    </div>

    <!-- Filters -->
    <div class="agency-card p-6 mb-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0 md:space-x-4">
            <div class="flex-1">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search leads by name, email, or phone..."
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500">
            </div>
            <div class="flex space-x-4">
                <select wire:model.live="statusFilter" 
                    class="rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500">
                    <option value="all">All Status</option>
                    <option value="new">New</option>
                    <option value="valid">Valid</option>
                    <option value="invalid">Invalid</option>
                    <option value="closed">Closed</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Leads Table -->
    <div class="agency-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Source</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Value</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($leads as $lead)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div>
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $lead->name ?: 'Unknown' }}
                                    </div>
                                    <div class="text-sm text-gray-500">{{ $lead->email }}</div>
                                    @if($lead->phone)
                                        <div class="text-sm text-gray-500">{{ $lead->phone }}</div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    @if($lead->utm_source)
                                        {{ ucfirst($lead->utm_source) }}
                                        @if($lead->utm_medium)
                                            <span class="text-gray-500">/ {{ ucfirst($lead->utm_medium) }}</span>
                                        @endif
                                    @else
                                        <span class="text-gray-500">Direct</span>
                                    @endif
                                </div>
                                @if($lead->utm_campaign)
                                    <div class="text-xs text-gray-500">{{ $lead->utm_campaign }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @switch($lead->status)
                                    @case('new')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            New
                                        </span>
                                        @break
                                    @case('valid')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                            </svg>
                                            Valid
                                        </span>
                                        @break
                                    @case('invalid')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                            </svg>
                                            Invalid
                                        </span>
                                        @break
                                    @case('closed')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                            </svg>
                                            Closed
                                        </span>
                                        @break
                                @endswitch
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                @if($lead->value)
                                    <span class="font-medium">${{ number_format($lead->value, 0) }}</span>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $lead->submitted_at ? $lead->submitted_at->format('M j, Y g:i A') : 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    @if($lead->status === 'new')
                                        <button wire:click="markAsValid({{ $lead->id }})" 
                                            class="text-green-600 hover:text-green-900 transition-colors">
                                            Mark Valid
                                        </button>
                                        <span class="text-gray-300">|</span>
                                        <button wire:click="markAsInvalid({{ $lead->id }})" 
                                            class="text-red-600 hover:text-red-900 transition-colors">
                                            Mark Invalid
                                        </button>
                                    @elseif($lead->status === 'valid')
                                        <button wire:click="openLeadModal({{ $lead->id }})" 
                                            class="text-purple-600 hover:text-purple-900 transition-colors">
                                            Mark Closed
                                        </button>
                                        <span class="text-gray-300">|</span>
                                        <button wire:click="markAsInvalid({{ $lead->id }})" 
                                            class="text-red-600 hover:text-red-900 transition-colors">
                                            Mark Invalid
                                        </button>
                                    @endif
                                    <span class="text-gray-300">|</span>
                                    <button wire:click="openLeadModal({{ $lead->id }})" 
                                        class="text-blue-600 hover:text-blue-900 transition-colors">
                                        View Details
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="text-gray-500">
                                    <svg class="mx-auto h-16 w-16 text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">No leads found</h3>
                                    <p class="text-gray-500">No leads match your current filters.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($leads->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $leads->links() }}
            </div>
        @endif
    </div>

    <!-- Lead Detail Modal -->
    @if($showLeadModal && $selectedLead)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeLeadModal"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4" id="modal-title">
                                    Lead Details
                                </h3>

                                <!-- Lead Information -->
                                <div class="space-y-4 mb-6">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Contact Information</label>
                                        <div class="mt-1 p-3 bg-gray-50 rounded-md">
                                            <div class="text-sm">
                                                <div><strong>Name:</strong> {{ $selectedLead->name ?: 'Not provided' }}</div>
                                                <div><strong>Email:</strong> {{ $selectedLead->email ?: 'Not provided' }}</div>
                                                @if($selectedLead->phone)
                                                    <div><strong>Phone:</strong> {{ $selectedLead->phone }}</div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    @if($selectedLead->message)
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Message</label>
                                            <div class="mt-1 p-3 bg-gray-50 rounded-md text-sm">
                                                {{ $selectedLead->message }}
                                            </div>
                                        </div>
                                    @endif

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Source</label>
                                        <div class="mt-1 p-3 bg-gray-50 rounded-md text-sm">
                                            @if($selectedLead->utm_source)
                                                <div><strong>Source:</strong> {{ $selectedLead->utm_source }}</div>
                                                @if($selectedLead->utm_medium)
                                                    <div><strong>Medium:</strong> {{ $selectedLead->utm_medium }}</div>
                                                @endif
                                                @if($selectedLead->utm_campaign)
                                                    <div><strong>Campaign:</strong> {{ $selectedLead->utm_campaign }}</div>
                                                @endif
                                            @else
                                                Direct traffic
                                            @endif
                                        </div>
                                    </div>

                                    @if($selectedLead->status === 'valid')
                                        <div>
                                            <label for="lead-value" class="block text-sm font-medium text-gray-700">Lead Value ($)</label>
                                            <input type="number" step="0.01" wire:model="leadValue" id="lead-value"
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500"
                                                placeholder="Enter dollar value">
                                        </div>
                                    @endif

                                    <div>
                                        <label for="lead-notes" class="block text-sm font-medium text-gray-700">Notes</label>
                                        <textarea wire:model="leadNotes" id="lead-notes" rows="3"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500"
                                            placeholder="Add any notes about this lead..."></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        @if($selectedLead->status === 'valid')
                            <button type="button" wire:click="markAsClosed"
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-purple-600 text-base font-medium text-white hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 sm:ml-3 sm:w-auto sm:text-sm">
                                Mark as Closed
                            </button>
                        @endif
                        <button type="button" wire:click="closeLeadModal"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>