<x-layouts.app>
    <div class="mt-4 grid grid-cols-1 gap-4">
        <div class="overflow-hidden aspect-video rounded-lg border border-neutral-200 dark:border-neutral-700">
            <livewire:latest-update :project="$project"/>
        </div>
        {{--        <div class="overflow-hidden aspect-video rounded-lg border border-neutral-200 dark:border-neutral-700">--}}
        {{--            <livewire:client-tasks-list :project="$project"/>--}}
        {{--        </div>--}}
    </div>

</x-layouts.app>
