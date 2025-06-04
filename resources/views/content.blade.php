<x-layouts.app>
    <div class="overflow-hidden rounded-lg border border-neutral-200 dark:border-neutral-700">
        <div class="items-center p-4">
            <h1 class="text-3xl font-bold bg-[linear-gradient(130deg,#003E4A_0.69%,#112629_50.19%,#FC7B3E_79.69%)] bg-clip-text text-transparent">
                Content Strategy</h1>
            <p>Review your published content and your full 12 month content strategy below.</p>

        </div>
    </div>
    <div class="mt-10">
        <h2 class="text-2xl font-bold bg-[linear-gradient(130deg,#003E4A_0.69%,#112629_50.19%,#FC7B3E_79.69%)] bg-clip-text text-transparent">{{ __("Content Published In Last 30 Days") }}</h2>
        <livewire:recent-wordpress-posts :project="$project" :month="2025-03"/>
    </div>
    <div class="mt-10">
        <h2 class="text-2xl font-bold bg-[linear-gradient(130deg,#003E4A_0.69%,#112629_50.19%,#FC7B3E_79.69%)] bg-clip-text text-transparent">{{ __("Content Strategy For 2025") }}</h2>
        <livewire:content-plan :project="$project"/>
    </div>
</x-layouts.app>
