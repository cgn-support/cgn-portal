<x-layouts.app title="Reports">
    <div class="max-w-7xl mx-auto p-6">
        <!-- Breadcrumb -->
        <div class="mb-6">
            <nav class="flex items-center space-x-2 text-sm text-gray-600">
                <a href="{{ route('dashboard') }}" class="hover:text-orange-500">
                    <flux:icon.home class="w-4 h-4" />
                </a>
                <span>/</span>
                <a href="{{ route('project', ['uuid' => $project->id]) }}" class="hover:text-orange-500">
                    {{ $project->display_name }}
                </a>
                <span>/</span>
                <span>Reports</span>
            </nav>
        </div>

        <!-- Reports Component -->
        <livewire:project-reports :project="$project" />
    </div>
</x-layouts.app>