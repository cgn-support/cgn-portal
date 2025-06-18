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
            <h2 class="gradient-heading">
                Monthly Marketing Report
            </h2>
        </div>

        <!-- Description Section -->
        @if ($report->content)
            <div class="mb-8">
                <div
                    class="prose prose-lg max-w-none prose-headings:text-gray-900 prose-p:text-gray-600 prose-ul:text-gray-600 prose-li:text-gray-600 prose-strong:text-gray-900">
                    <style>
                        .prose ul {
                            list-style-type: disc;
                            padding-left: 1.625rem;
                            margin-top: 1.25rem;
                            margin-bottom: 1.25rem;
                        }

                        .prose ul li {
                            margin-top: 0.5rem;
                            margin-bottom: 0.5rem;
                            padding-left: 0.375rem;
                        }

                        .prose ol {
                            list-style-type: decimal;
                            padding-left: 1.625rem;
                            margin-top: 1.25rem;
                            margin-bottom: 1.25rem;
                        }

                        .prose ol li {
                            margin-top: 0.5rem;
                            margin-bottom: 0.5rem;
                            padding-left: 0.375rem;
                        }

                        .prose p {
                            margin-top: 1.25rem;
                            margin-bottom: 1.25rem;
                            line-height: 1.75;
                        }

                        .prose strong {
                            font-weight: 600;
                        }

                        .prose em {
                            font-style: italic;
                        }

                        .prose u {
                            text-decoration: underline;
                        }

                        .prose a {
                            color: #f97316;
                            text-decoration: underline;
                        }

                        .prose a:hover {
                            color: #ea580c;
                        }
                    </style>
                    {!! $report->content !!}
                </div>
            </div>
        @endif

        <!-- Analytics Screenshot -->
        @if ($report->file_path)
            <div class="mb-8">
                <div class="relative overflow-hidden pt-6">
                    <div class="mx-auto max-w-7xl px-6 lg:px-8">
                        <img src="{{ asset('storage/' . $report->file_path) }}" alt="Analytics Dashboard"
                            class="rounded-xl ring-1 shadow-2xl ring-gray-900/10">
                        <div class="relative" aria-hidden="true">
                            <div class="absolute -inset-x-20 bottom-0 bg-gradient-to-t from-white pt-[7%]"></div>
                        </div>
                    </div>
                </div>
            </div>
            @if ($report->looker_studio_share_link)
                <div class="flex justify-center">
                    <a href="{{ $report->looker_studio_share_link }}" target="_blank"
                        class="p-4 bg-orange-600 text-white rounded-lg shadow hover:bg-orange-700 transition-colors duration-200">Open
                        The Full Report</a>
                </div>
            @endif
        @endif

        <!-- Marketing Metrics Grid -->
        <div class="my-8">
            <livewire:report-metrics :report="$report" />
        </div>

        <!-- Recently Published Content -->
        <div class="mb-8">
            <h3 class="text-xl font-semibold text-gray-800 mb-4">Recently Published Content</h3>
            <p class="text-gray-600 mb-6">Blog posts published on your website during {{ $report->report_month_name }}
                {{ $report->report_year }}.</p>

            <livewire:recent-blog-posts :month="$report->report_year . '-' . str_pad($report->report_month, 2, '0', STR_PAD_LEFT) . '-01'" :project="$report->project" />
        </div>

        <!-- Completed Tasks (Monday.com) -->
        <div class="mb-8">
            <h3 class="text-xl font-semibold text-gray-800 mb-4">Project Completed Tasks</h3>
            <p class="text-gray-600 mb-6">Tasks completed for your project in {{ $report->report_month_name }}
                {{ $report->report_year }}.</p>

            <livewire:report-tasks :report="$report" />
        </div>
    </div>
</x-layouts.app>
