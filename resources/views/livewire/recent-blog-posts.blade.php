<div class="space-y-4">
    @forelse($posts as $post)
        <div class="bg-white shadow-sm rounded-lg p-4 hover:shadow-md transition-shadow duration-200">
            <a href="{{ $post['link'] }}" target="_blank" class="block group">
                <div class="flex items-start gap-4">
                    @if ($post['thumbnail'])
                        <img src="{{ $post['thumbnail'] }}" alt="{{ $post['title'] }}"
                            class="w-20 h-20 object-cover rounded-full flex-shrink-0">
                    @else
                        <div class="w-20 h-20 bg-gray-200 flex items-center justify-center rounded-full flex-shrink-0">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                    @endif
                    <div class="flex-1 min-w-0">
                        <h3
                            class="text-lg font-semibold text-gray-900 group-hover:text-blue-600 transition-colors duration-200 line-clamp-2">
                            {{ $post['title'] }}
                        </h3>
                        <p class="text-sm text-gray-500 mt-1">{{ $post['date'] }}</p>
                        @if (!empty($post['excerpt']))
                            <p class="text-sm text-gray-600 mt-2 line-clamp-2">
                                {{ Str::limit($post['excerpt'], 120) }}
                            </p>
                        @endif
                    </div>
                </div>
            </a>
        </div>
    @empty
        <div class="text-center py-8">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <p class="mt-2 text-sm font-medium text-gray-900">No blog posts published</p>
            <p class="mt-1 text-sm text-gray-500">No posts were published during
                {{ \Carbon\Carbon::parse($month)->format('F Y') }}</p>
        </div>
    @endforelse
</div>
