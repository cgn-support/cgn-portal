<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            {{ $heading }}
        </x-slot>

        @if ($isEmpty)
            <div class="flex flex-col items-center justify-center py-8 text-center">
                <x-heroicon-o-chart-bar class="w-12 h-12 text-gray-400 mb-4" />
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">No Project Data</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">No projects with tracking data found.</p>
            </div>
        @else
            <div class="overflow-hidden">
                <table class="w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th
                                class="px-3 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Rank
                            </th>
                            <th
                                class="px-3 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Project
                            </th>
                            <th
                                class="px-3 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Leads
                            </th>
                            <th
                                class="px-3 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Calls
                            </th>
                            <th
                                class="px-3 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Forms
                            </th>
                            <th
                                class="px-3 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Conv %
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach ($projects as $project)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                                <td class="px-3 py-4 whitespace-nowrap">
                                    <span class="{{ $project['rank_class'] }} text-sm">
                                        {{ $project['rank_icon'] }}
                                    </span>
                                </td>
                                <td class="px-3 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                        {{ $project['business_name'] }}
                                    </div>
                                </td>
                                <td class="px-3 py-4 whitespace-nowrap text-center">
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                        {{ number_format($project['total_leads']) }}
                                    </span>
                                </td>
                                <td class="px-3 py-4 whitespace-nowrap text-center">
                                    <span class="text-sm text-blue-600 dark:text-blue-400 font-medium">
                                        {{ number_format($project['phone_calls']) }}
                                    </span>
                                </td>
                                <td class="px-3 py-4 whitespace-nowrap text-center">
                                    <span class="text-sm text-orange-600 dark:text-orange-400 font-medium">
                                        {{ number_format($project['form_submissions']) }}
                                    </span>
                                </td>
                                <td class="px-3 py-4 whitespace-nowrap text-center">
                                    <span
                                        class="text-sm font-medium {{ $project['conversion_rate'] >= 5 ? 'text-green-600' : ($project['conversion_rate'] >= 2 ? 'text-yellow-600' : 'text-red-600') }}">
                                        {{ $project['conversion_rate'] }}%
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>
