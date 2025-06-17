<x-layouts.app :title="$report->report_month_name . ' ' . $report->report_year . ' Report'">
    <div class="max-w-4xl mx-auto p-6">
        <!-- Header with Breadcrumb -->
        <div class="mb-6">
            <nav class="flex items-center space-x-2 text-sm text-gray-600 mb-4">
                <a href="{{ route('dashboard') }}" class="hover:text-orange-500">
                    <flux:icon.home class="w-4 h-4" />
                </a>
                <span>/</span>
                <span>{{ $report->report_month_name }} {{ $report->report_year }}</span>
            </nav>

            <h1 class="gradient-heading mb-4">
                {{ $report->report_month_name }} {{ $report->report_year }}
            </h1>
            <h2 class="text-2xl font-semibold text-teal-800 mb-4">
                Monthly Marketing <span class="text-orange-500">Report</span>
            </h2>
        </div>

        <!-- Description Section -->
        @if ($report->content)
            <div class="mb-8">
                <div class="prose prose-lg max-w-none">
                    {!! $report->content !!}
                </div>
            </div>
        @endif

        <!-- Analytics Screenshot -->
        @if ($report->file_path)
            <div class="mb-8">
                <img src="{{ asset('storage/' . $report->file_path) }}" alt="Analytics Dashboard"
                    class="w-full rounded-lg shadow-md border border-gray-200">
            </div>
            <a href="" class="p-4 bg-orange-600 text-white">Open The Full Report</a>
        @endif

        <!-- Marketing Metrics Grid -->
        <div class="my-8">
            <h3 class="text-xl font-semibold text-gray-800 mb-4">Marketing Metrics</h3>
            <p class="text-gray-600 mb-6">The most important KPIs for your marketing campaign are below.</p>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Row 1 -->
                <div class="agency-card p-6 text-center">
                    <h4 class="text-sm font-medium text-gray-600 mb-2">Organic Sessions</h4>
                    <p class="text-3xl font-bold text-gray-900">1022</p>
                </div>
                <div class="agency-card p-6 text-center">
                    <h4 class="text-sm font-medium text-gray-600 mb-2">Contact Button Users</h4>
                    <p class="text-3xl font-bold text-gray-900">106</p>
                </div>
                <div class="agency-card p-6 text-center">
                    <h4 class="text-sm font-medium text-gray-600 mb-2">Form Submissions</h4>
                    <p class="text-3xl font-bold text-gray-900">13</p>
                </div>

                <!-- Row 2 -->
                <div class="agency-card p-6 text-center">
                    <h4 class="text-sm font-medium text-gray-600 mb-2">Web Phone Calls</h4>
                    <p class="text-3xl font-bold text-gray-900">3</p>
                </div>
                <div class="agency-card p-6 text-center">
                    <h4 class="text-sm font-medium text-gray-600 mb-2">GBP Phone Calls</h4>
                    <p class="text-3xl font-bold text-gray-900">8</p>
                </div>
                <div class="agency-card p-6 text-center">
                    <h4 class="text-sm font-medium text-gray-600 mb-2">GBP Listing Clicks</h4>
                    <p class="text-3xl font-bold text-gray-900">12</p>
                </div>

                <!-- Row 3 -->
                <div class="agency-card p-6 text-center">
                    <h4 class="text-sm font-medium text-gray-600 mb-2">GBP Booking Clicks</h4>
                    <p class="text-3xl font-bold text-gray-900">1</p>
                </div>
                <div class="agency-card p-6 text-center">
                    <h4 class="text-sm font-medium text-gray-600 mb-2">Total Citations</h4>
                    <p class="text-3xl font-bold text-gray-900">36</p>
                </div>
                <div class="agency-card p-6 text-center">
                    <h4 class="text-sm font-medium text-gray-600 mb-2">Total Reviews</h4>
                    <p class="text-3xl font-bold text-gray-900">262</p>
                </div>
            </div>
        </div>

        <!-- Recently Published Content -->
        <div class="mb-8">
            <h3 class="text-xl font-semibold text-gray-800 mb-4">Recently Published Content</h3>
            <p class="text-gray-600 mb-6">The content we have created and published for you in the last 30 days is
                below.</p>

            <div class="space-y-4">
                <!-- Blog Post 1 -->
                <div class="agency-card p-4 flex items-center space-x-4">
                    <div class="w-16 h-16 bg-teal-800 rounded-lg flex items-center justify-center">
                        <flux:icon.document-text class="w-8 h-8 text-white" />
                    </div>
                    <div class="flex-1">
                        <h4 class="font-semibold text-gray-900">The Future of SEO: Agency Insights</h4>
                        <p class="text-sm text-gray-600">Jan 23, 2025</p>
                    </div>
                </div>

                <!-- Blog Post 2 -->
                <div class="agency-card p-4 flex items-center space-x-4">
                    <div class="w-16 h-16 bg-orange-500 rounded-lg flex items-center justify-center">
                        <flux:icon.document-text class="w-8 h-8 text-white" />
                    </div>
                    <div class="flex-1">
                        <h4 class="font-semibold text-gray-900">Digital Marketing For Contractors</h4>
                        <p class="text-sm text-gray-600">Jan 19, 2025</p>
                    </div>
                </div>

                <!-- Blog Post 3 -->
                <div class="agency-card p-4 flex items-center space-x-4">
                    <div class="w-16 h-16 bg-teal-800 rounded-lg flex items-center justify-center">
                        <flux:icon.document-text class="w-8 h-8 text-white" />
                    </div>
                    <div class="flex-1">
                        <h4 class="font-semibold text-gray-900">How To Get Construction Leads? Building Your Marketing
                            Funnel</h4>
                        <p class="text-sm text-gray-600">Dec 14, 2024</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Completed Tasks (Monday.com) -->
        <div class="mb-8">
            <h3 class="text-xl font-semibold text-gray-800 mb-4">Project Completed Tasks</h3>
            <p class="text-gray-600 mb-6">Tasks completed for your project in {{ $report->report_month_name }} {{ $report->report_year }}.</p>

            <livewire:report-tasks :report="$report" />
        </div>
    </div>
</x-layouts.app>
