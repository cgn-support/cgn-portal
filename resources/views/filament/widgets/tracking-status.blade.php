<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            Tracking System Status
        </x-slot>

        <x-slot name="headerEnd">
            <x-filament::button wire:click="$refresh" size="sm" color="gray">
                <x-heroicon-m-arrow-path class="w-4 h-4" />
                Refresh
            </x-filament::button>
        </x-slot>

        <div class="space-y-4">
            <!-- API Status -->
            <div
                class="flex items-center justify-between p-4 rounded-lg {{ $api_healthy ? 'bg-green-50 dark:bg-green-900/20' : 'bg-red-50 dark:bg-red-900/20' }}">
                <div class="flex items-center space-x-3">
                    @if ($api_healthy)
                        <x-heroicon-s-check-circle class="w-6 h-6 text-green-600" />
                        <div>
                            <p class="font-medium text-green-900 dark:text-green-100">API Healthy</p>
                            <p class="text-sm text-green-700 dark:text-green-300">Tracking API is responding normally
                            </p>
                        </div>
                    @else
                        <x-heroicon-s-x-circle class="w-6 h-6 text-red-600" />
                        <div>
                            <p class="font-medium text-red-900 dark:text-red-100">API Down</p>
                            <p class="text-sm text-red-700 dark:text-red-300">Using cached data only</p>
                        </div>
                    @endif
                </div>
                <div class="text-right">
                    <p class="text-sm text-gray-600 dark:text-gray-400">Last checked:</p>
                    <p class="text-sm font-medium">{{ $last_health_check->diffForHumans() }}</p>
                </div>
            </div>

            <!-- Cache Status -->
            <div class="grid grid-cols-2 gap-4">
                <div class="p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                    <div class="flex items-center space-x-2">
                        <x-heroicon-s-cube class="w-5 h-5 text-blue-600" />
                        <div>
                            <p class="text-sm text-blue-700 dark:text-blue-300">Cache Keys</p>
                            <p class="text-lg font-semibold text-blue-900 dark:text-blue-100">
                                {{ is_numeric($cache_stats['total_cache_keys']) ? number_format($cache_stats['total_cache_keys']) : $cache_stats['total_cache_keys'] }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="p-4 bg-purple-50 dark:bg-purple-900/20 rounded-lg">
                    <div class="flex items-center space-x-2">
                        <x-heroicon-s-cpu-chip class="w-5 h-5 text-purple-600" />
                        <div>
                            <p class="text-sm text-purple-700 dark:text-purple-300">Cache Driver</p>
                            <p class="text-lg font-semibold text-purple-900 dark:text-purple-100">
                                {{ $cache_stats['cache_driver'] }}</p>
                        </div>
                    </div>
                </div>
            </div>

            @if (isset($cache_stats['memory_usage']) && $cache_stats['memory_usage'] !== 'N/A')
                <div class="p-4 bg-green-50 dark:bg-green-900/20 rounded-lg">
                    <div class="flex items-center space-x-2">
                        <x-heroicon-s-server class="w-5 h-5 text-green-600" />
                        <div>
                            <p class="text-sm text-green-700 dark:text-green-300">Memory Usage</p>
                            <p class="text-lg font-semibold text-green-900 dark:text-green-100">
                                {{ $cache_stats['memory_usage'] }}</p>
                        </div>
                    </div>
                </div>
            @endif


            <!-- Quick Actions -->
            <div class="flex space-x-2">
                <x-filament::button wire:click="refreshCache" size="sm" color="primary">
                    <x-heroicon-m-arrow-path class="w-4 h-4 mr-1" />
                    Refresh Cache
                </x-filament::button>

                <x-filament::button wire:click="clearCache" size="sm" color="danger" outlined>
                    <x-heroicon-m-trash class="w-4 h-4 mr-1" />
                    Clear Cache
                </x-filament::button>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
