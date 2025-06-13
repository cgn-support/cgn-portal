<x-layouts.app :title="__('Dashboard')">
    <div class="overflow-hidden">
        <div class="flex justify-between items-center p-4">
            <h1
                class="text-3xl font-bold bg-[linear-gradient(130deg,#003E4A_0.69%,#112629_50.19%,#FC7B3E_79.69%)] bg-clip-text text-transparent">
                Welcome Back, {{ ucfirst($user->name) }}</h1>

        </div>
    </div>

    <div
        class="mt-6 px-6 py-8 overflow-hidden rounded-lg border border-neutral-200 dark:border-neutral-700 sm:px-6 lg:px-8">
        @if ($projects->count() > 0)
            <livewire:dashboard-stats :projects="$projects" />
        @endif
    </div>

    <div class="flex w-full flex-1 flex-col gap-4 rounded-lg mt-4">
        <div class="relative h-full flex-1 overflow-hidden rounded-lg border border-neutral-200 dark:border-neutral-700">
            <livewire:projects-table />
        </div>
    </div>

</x-layouts.app>
