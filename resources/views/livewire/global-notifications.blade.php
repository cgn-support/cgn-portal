<div class="space-y-3">
    @if(count($notifications) > 0)
        @foreach($notifications as $notification)
            <div class="bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200">
                <div class="p-4">
                    <div class="flex items-start justify-between">
                        <div class="flex items-start space-x-3 flex-1">
                            <!-- Notification Icon -->
                            <div class="flex-shrink-0">
                                @php
                                    $iconBgColor = match($notification['type']) {
                                        'announcement' => 'bg-blue-100 dark:bg-blue-900/20',
                                        'feature' => 'bg-green-100 dark:bg-green-900/20',
                                        'blog' => 'bg-purple-100 dark:bg-purple-900/20',
                                        'podcast' => 'bg-orange-100 dark:bg-orange-900/20',
                                        'video' => 'bg-red-100 dark:bg-red-900/20',
                                        default => 'bg-gray-100 dark:bg-gray-800',
                                    };
                                    
                                    $iconColor = match($notification['type']) {
                                        'announcement' => 'text-blue-600 dark:text-blue-400',
                                        'feature' => 'text-green-600 dark:text-green-400',
                                        'blog' => 'text-purple-600 dark:text-purple-400',
                                        'podcast' => 'text-orange-600 dark:text-orange-400',
                                        'video' => 'text-red-600 dark:text-red-400',
                                        default => 'text-gray-500 dark:text-gray-400',
                                    };
                                    
                                    $icon = $notification['icon'] ?? match($notification['type']) {
                                        'announcement' => 'megaphone',
                                        'feature' => 'sparkles',
                                        'blog' => 'document-text',
                                        'podcast' => 'microphone',
                                        'video' => 'play-circle',
                                        default => 'bell',
                                    };
                                @endphp
                                
                                <div class="w-10 h-10 rounded-lg {{ $iconBgColor }} flex items-center justify-center">
                                    <x-dynamic-component :component="'heroicon-o-' . $icon" class="w-5 h-5 {{ $iconColor }}" />
                                </div>
                            </div>
                            
                            <!-- Notification Content -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between mb-2">
                                    <div class="flex items-center space-x-2">
                                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white">{{ $notification['title'] }}</h3>
                                        
                                        <!-- Type Badge -->
                                        @php
                                            $badgeColor = match($notification['type']) {
                                                'announcement' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300',
                                                'feature' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300',
                                                'blog' => 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300',
                                                'podcast' => 'bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-300',
                                                'video' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300',
                                                default => 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-300',
                                            };
                                            
                                            $typeLabel = match($notification['type']) {
                                                'announcement' => 'Announcement',
                                                'feature' => 'New Feature',
                                                'blog' => 'Blog Post',
                                                'podcast' => 'Podcast',
                                                'video' => 'Video',
                                                default => 'General',
                                            };
                                        @endphp
                                        
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $badgeColor }}">
                                            {{ $typeLabel }}
                                        </span>
                                    </div>
                                    
                                    <!-- Timestamp -->
                                    <span class="text-xs text-gray-500 dark:text-gray-400 flex-shrink-0">
                                        {{ \Carbon\Carbon::parse($notification['created_at'])->diffForHumans() }}
                                    </span>
                                </div>
                                
                                <p class="text-sm text-gray-600 dark:text-gray-300 mb-3">{{ $notification['content'] }}</p>
                                
                                <!-- Action Link -->
                                @if($notification['link'])
                                    <a href="{{ $notification['link'] }}" 
                                       target="_blank" 
                                       class="inline-flex items-center text-sm text-blue-600 hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300 font-medium transition-colors duration-200">
                                        Learn more
                                        <x-heroicon-o-arrow-top-right-on-square class="w-4 h-4 ml-1" />
                                    </a>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Dismiss Button -->
                        <button 
                            wire:click="dismissNotification({{ $notification['id'] }})"
                            class="flex-shrink-0 p-2 text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition-colors duration-200"
                            title="Dismiss notification"
                        >
                            <x-heroicon-o-x-mark class="w-4 h-4" />
                        </button>
                    </div>
                </div>
            </div>
        @endforeach
    @else
        <div class="text-center py-12 bg-gray-50 dark:bg-gray-700/50 rounded-lg border border-gray-200 dark:border-gray-600">
            <div class="w-16 h-16 bg-gray-100 dark:bg-gray-600 rounded-full flex items-center justify-center mx-auto mb-4">
                <x-heroicon-o-bell class="w-8 h-8 text-gray-400 dark:text-gray-500" />
            </div>
            <h3 class="text-sm font-medium text-gray-900 dark:text-white mb-1">No new notifications</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">Check back later for updates and announcements</p>
        </div>
    @endif
</div>