{{-- resources/views/components/metric-card.blade.php --}}
@props(['value', 'label', 'icon'])

@php
$icons = [
'mail' => '<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-orange-600 dark:text-orange-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
        d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8m-18 8h18a2 2 0 002-2V8a2 2 0 00-2-2H3a2 2 0 00-2 2v6a2 2 0 002 2z" />
</svg>',
'phone' => '<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-orange-600 dark:text-orange-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
        d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.518 4.555a1 1 0 01-.217.98l-2.2 2.2a11.042 11.042 0 005.293 5.293l2.2-2.2a1 1 0 01.98-.217l4.555 1.518a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.163 21 3 14.837 3 7V5z" />
</svg>',
'users' => '<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-orange-600 dark:text-orange-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
        d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87M16 7a4 4 0 11-8 0 4 4 0 018 0zm6 2a6 6 0 10-12 0 6 6 0 0012 0z" />
</svg>',
];
@endphp

<div {{ $attributes->merge(['class' => 'bg-white dark:bg-gray-800 bg-opacity-70 dark:bg-opacity-70 backdrop-blur-md rounded-lg p-6 flex items-center space-x-4']) }}>
    <span class="flex-shrink-0 h-10 w-10 bg-orange-100 dark:bg-orange-600 rounded-full flex items-center justify-center">
        {!! $icons[$icon] ?? '' !!}
    </span>
    <div>
        <div class="text-3xl font-semibold text-gray-900 dark:text-white">{{ $value }}</div>
        <div class="text-sm text-gray-500 dark:text-gray-400">{{ $label }}</div>
    </div>
</div>