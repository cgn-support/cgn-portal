<x-layouts.app>
    <nav class="flex" aria-label="Breadcrumb">
        <ol role="list" class="flex space-x-4 rounded-full bg-white px-6 shadow">
            <li class="flex">
                <div class="flex items-center">
                    <a href="/dashboard" class="text-gray-400 hover:text-gray-500">
                        <svg class="size-5 shrink-0" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"
                            data-slot="icon">
                            <path fill-rule="evenodd"
                                d="M9.293 2.293a1 1 0 0 1 1.414 0l7 7A1 1 0 0 1 17 11h-1v6a1 1 0 0 1-1 1h-2a1 1 0 0 1-1-1v-3a1 1 0 0 0-1-1H9a1 1 0 0 0-1 1v3a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-6H3a1 1 0 0 1-.707-1.707l7-7Z"
                                clip-rule="evenodd" />
                        </svg>
                        <span class="sr-only">Home</span>
                    </a>
                </div>
            </li>
            <li class="flex">
                <div class="flex items-center">
                    <svg class="h-full w-6 shrink-0 text-gray-200" viewBox="0 0 24 44" preserveAspectRatio="none"
                        fill="currentColor" aria-hidden="true">
                        <path d="M.293 0l22 22-22 22h1.414l22-22-22-22H.293z" />
                    </svg>
                    <a href="/project/{{ $project->id }}"
                        class="ml-4 text-sm font-medium text-gray-500 hover:text-gray-700">{{ $project->name }}</a>
                </div>
            </li>
            <li class="flex">
                <div class="flex items-center">
                    <svg class="h-full w-6 shrink-0 text-gray-200" viewBox="0 0 24 44" preserveAspectRatio="none"
                        fill="currentColor" aria-hidden="true">
                        <path d="M.293 0l22 22-22 22h1.414l22-22-22-22H.293z" />
                    </svg>
                    <a href="#" class="ml-4 text-sm font-medium text-gray-500 hover:text-gray-700"
                        aria-current="page">My Updates</a>
                </div>
            </li>
        </ol>
    </nav>

    <div class="container mx-auto px-4 py-6">
        <livewire:updates-list :project="$project" />
    </div>
</x-layouts.app>
