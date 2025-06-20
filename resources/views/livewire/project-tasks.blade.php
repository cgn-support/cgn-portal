<div>
    <!-- Success Message -->
    @if (session()->has('success'))
        <div class="mb-6 bg-green-50 border border-green-200 rounded-full p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                            clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800">
                        {{ session('success') }}
                    </p>
                </div>
            </div>
        </div>
    @endif

    <!-- Header -->
    <div class="mb-8">
        <h1 class="gradient-heading">Project Tasks</h1>
        <p class="text-gray-600 mt-2">Complete assigned tasks to help your project progress smoothly</p>
    </div>

    <!-- Task Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <!-- Total Tasks -->
        <div class="agency-card p-6 text-center">
            <div class="mb-2">
                <div
                    class="w-12 h-12 bg-gradient-to-br from-blue-400 to-blue-600 rounded-full mx-auto flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                </div>
            </div>
            <div class="text-2xl font-bold text-gray-900">{{ $taskSummary['total'] }}</div>
            <div class="text-sm text-gray-600">Total Tasks</div>
        </div>

        <!-- Pending Tasks -->
        <div class="agency-card p-6 text-center">
            <div class="mb-2">
                <div
                    class="w-12 h-12 bg-gradient-to-br from-yellow-400 to-yellow-600 rounded-full mx-auto flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            <div class="text-2xl font-bold text-gray-900">{{ $taskSummary['pending'] }}</div>
            <div class="text-sm text-gray-600">Pending</div>
        </div>

        <!-- Completed Tasks -->
        <div class="agency-card p-6 text-center">
            <div class="mb-2">
                <div
                    class="w-12 h-12 bg-gradient-to-br from-green-400 to-green-600 rounded-full mx-auto flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            <div class="text-2xl font-bold text-gray-900">{{ $taskSummary['completed'] }}</div>
            <div class="text-sm text-gray-600">Completed</div>
        </div>

        <!-- Overdue Tasks -->
        <div class="agency-card p-6 text-center">
            <div class="mb-2">
                <div
                    class="w-12 h-12 bg-gradient-to-br from-red-400 to-red-600 rounded-full mx-auto flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.864-.833-2.634 0L3.232 16.5c-.77.833.192 2.5 1.732 2.5z" />
                    </svg>
                </div>
            </div>
            <div class="text-2xl font-bold text-gray-900">{{ $taskSummary['overdue'] }}</div>
            <div class="text-sm text-gray-600">Overdue</div>
        </div>
    </div>

    <!-- Filters -->
    <div class="agency-card p-6 mb-6">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-medium text-gray-900">Task List</h3>
            <select wire:model.live="statusFilter"
                class="rounded-full border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500">
                <option value="all">All Tasks</option>
                <option value="pending">Pending</option>
                <option value="completed">Completed</option>
                <option value="overdue">Overdue</option>
            </select>
        </div>
    </div>

    <!-- Tasks List -->
    <div class="space-y-4">
        @forelse($tasks as $task)
            <div
                class="agency-card p-6 {{ $task->status === 'overdue' ? 'border-l-4 border-red-500' : '' }} {{ $task->status === 'completed' ? 'opacity-75' : '' }}">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <div class="flex items-center space-x-3 mb-2">
                            <h3 class="text-lg font-semibold text-gray-900">{{ $task->title }}</h3>

                            <!-- Status Badge -->
                            @switch($task->status)
                                @case('completed')
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        Completed
                                    </span>
                                @break

                                @case('overdue')
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        Overdue
                                    </span>
                                @break

                                @default
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        Pending
                                    </span>
                            @endswitch
                        </div>

                        <p class="text-gray-600 mb-4">{{ $task->description }}</p>

                        <div class="flex flex-wrap items-center gap-4 text-sm text-gray-500">
                            <span>Assigned by: {{ $task->assignedBy->name }}</span>
                            @if ($task->due_date)
                                <span>Due: {{ $task->due_date->format('M j, Y g:i A') }}</span>
                            @endif
                            @if ($task->completed_at)
                                <span>Completed: {{ $task->completed_at->format('M j, Y g:i A') }}</span>
                            @endif
                        </div>

                        @if ($task->completion_notes)
                            <div class="mt-3 p-3 bg-gray-50 rounded-full">
                                <p class="text-sm text-gray-700">
                                    <span class="font-medium">Completion Notes:</span> {{ $task->completion_notes }}
                                </p>
                            </div>
                        @endif
                    </div>

                    <div class="ml-6 flex flex-col space-y-2">
                        @if ($task->link)
                            <a href="{{ $task->link }}" target="_blank"
                                class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-full text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                </svg>
                                Open Link
                            </a>
                        @endif

                        @if (!$task->is_completed)
                            <button wire:click="openCompletionModal({{ $task->id }})"
                                class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-full text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                                Mark Complete
                            </button>
                        @endif
                    </div>
                </div>
            </div>
            @empty
                <div class="agency-card p-12 text-center">
                    <svg class="mx-auto h-16 w-16 text-gray-400 mb-4" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                            d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No tasks found</h3>
                    <p class="text-gray-500">No tasks match your current filter. Try selecting a different status.</p>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if ($tasks->hasPages())
            <div class="mt-8">
                {{ $tasks->links() }}
            </div>
        @endif

        <!-- Task Completion Modal -->
        @if ($showCompletionModal && $selectedTask)
            <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog"
                aria-modal="true">
                <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
                        wire:click="closeCompletionModal"></div>

                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                    <div
                        class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start">
                                <div
                                    class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-100 sm:mx-0 sm:h-10 sm:w-10">
                                    <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                    <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                        Complete Task
                                    </h3>
                                    <div class="mt-2">
                                        <p class="text-sm text-gray-500 mb-4">
                                            Are you sure you want to mark "{{ $selectedTask->title }}" as completed?
                                        </p>

                                        <div>
                                            <label for="completion-notes" class="block text-sm font-medium text-gray-700">
                                                Completion Notes (Optional)
                                            </label>
                                            <textarea wire:model="completionNotes" id="completion-notes" rows="3"
                                                class="mt-1 block w-full rounded-full border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"
                                                placeholder="Add any notes about completing this task..."></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button type="button" wire:click="markTaskComplete"
                                class="w-full inline-flex justify-center rounded-full border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm">
                                Mark as Completed
                            </button>
                            <button type="button" wire:click="closeCompletionModal"
                                class="mt-3 w-full inline-flex justify-center rounded-full border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                Cancel
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
