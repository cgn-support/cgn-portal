<!--
  This example requires updating your template:

  ```
  <html class="h-full bg-gray-100">
  <body class="h-full">
  ```
-->
<x-layouts.app>
    <nav class="flex" aria-label="Breadcrumb">
        <ol role="list" class="flex space-x-4 rounded-md bg-white px-6 shadow">
            <li class="flex">
                <div class="flex items-center">
                    <a href="/project/{{ $project->id }}">
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
                    <a href="#" class="ml-4 text-sm font-medium text-gray-500 hover:text-gray-700"
                        aria-current="page">{{ \Carbon\Carbon::parse($report->report_date)->format('F Y') }}</a>
                </div>
            </li>
        </ol>
    </nav>

    <div class="mx-auto max-w-7xl pt-6 px-6 lg:px-8">
        <div class="mx-auto max-w-2xl lg:mx-0 lg:max-w-none">
            <h1 class="text-3xl font-bold">{{ \Carbon\Carbon::parse($report->report_date)->format('F Y') }}</h1>
            <p class="mt-2 text-4xl font-semibold tracking-tight bg-[linear-gradient(130deg,#003E4A_0.69%,#112629_50.19%,#FC7B3E_79.69%)] bg-clip-text text-transparent sm:text-5xl">
                Monthly
                Marketing Report</p>
            <div
                class="mt-6 grid max-w-xl grid-cols-1 gap-8 text-base/7 text-gray-700 ">
                <div class="border p-4 rounded-lg bg-gray-100">
                    {!! str($report->report_summary)->sanitizeHtml() !!}
                </div>

            </div>

        </div>
    </div>
    <div class="relative overflow-hidden pt-6">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <img class="rounded-lg ring-1 shadow-2xl ring-gray-900/10"
                src="{{ asset($report->data_studio_screenshot) }}" alt="">
            <div class="relative" aria-hidden="true">
                <div class="absolute -inset-x-20 bottom-0 bg-gradient-to-t from-white pt-[7%]"></div>
            </div>
        </div>
    </div>

    <div class="relative overflow-hidden pt-6">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="border-b border-gray-200 pb-5">
                <h2 class="text-xl font-semibold text-gray-900">Marketing Metrics</h2>
                <p class="mt-2 max-w-4xl text-sm text-gray-500">The most important KPIs for your marketing campaign are
                    below.</p>
            </div>

            <dl class="mx-auto grid grid-cols-1 gap-px bg-gray-900/5 sm:grid-cols-2 lg:grid-cols-3">
                <div
                    class="flex flex-wrap items-baseline justify-between gap-x-4 gap-y-2 bg-white px-4 py-10 sm:px-6 xl:px-8">
                    <dt class="text-sm/6 font-medium text-gray-500">Organic Sessions</dt>

                    <dd class="w-full flex-none text-3xl/10 font-medium tracking-tight text-gray-900">1022</dd>
                </div>
                <div
                    class="flex flex-wrap items-baseline justify-between gap-x-4 gap-y-2 bg-white px-4 py-10 sm:px-6 xl:px-8">
                    <dt class="text-sm/6 font-medium text-gray-500">CTA Button Clicks</dt>

                    <dd class="w-full flex-none text-3xl/10 font-medium tracking-tight text-gray-900">106</dd>
                </div>
                <div
                    class="flex flex-wrap items-baseline justify-between gap-x-4 gap-y-2 bg-white px-4 py-10 sm:px-6 xl:px-8">
                    <dt class="text-sm/6 font-medium text-gray-500">Form Submissions</dt>

                    <dd class="w-full flex-none text-3xl/10 font-medium tracking-tight text-gray-900">13</dd>
                </div>
                <div
                    class="flex flex-wrap items-baseline justify-between gap-x-4 gap-y-2 bg-white px-4 py-10 sm:px-6 xl:px-8">
                    <dt class="text-sm/6 font-medium text-gray-500">Web Phone Calls</dt>

                    <dd class="w-full flex-none text-3xl/10 font-medium tracking-tight text-gray-900">3</dd>
                </div>
                <div
                    class="flex flex-wrap items-baseline justify-between gap-x-4 gap-y-2 bg-white px-4 py-10 sm:px-6 xl:px-8">
                    <dt class="text-sm/6 font-medium text-gray-500">GBP Phone Calls</dt>

                    <dd class="w-full flex-none text-3xl/10 font-medium tracking-tight text-gray-900">8</dd>
                </div>
                <div
                    class="flex flex-wrap items-baseline justify-between gap-x-4 gap-y-2 bg-white px-4 py-10 sm:px-6 xl:px-8">
                    <dt class="text-sm/6 font-medium text-gray-500">GBP Listing Clicks</dt>

                    <dd class="w-full flex-none text-3xl/10 font-medium tracking-tight text-gray-900">12</dd>
                </div>
                <div
                    class="flex flex-wrap items-baseline justify-between gap-x-4 gap-y-2 bg-white px-4 py-10 sm:px-6 xl:px-8">
                    <dt class="text-sm/6 font-medium text-gray-500">GBP Booking Clicks</dt>

                    <dd class="w-full flex-none text-3xl/10 font-medium tracking-tight text-gray-900">1</dd>
                </div>
                <div
                    class="flex flex-wrap items-baseline justify-between gap-x-4 gap-y-2 bg-white px-4 py-10 sm:px-6 xl:px-8">
                    <dt class="text-sm/6 font-medium text-gray-500">Qualified Leads</dt>

                    <dd class="w-full flex-none text-3xl/10 font-medium tracking-tight text-gray-900">1</dd>
                </div>
                <div
                    class="flex flex-wrap items-baseline justify-between gap-x-4 gap-y-2 bg-white px-4 py-10 sm:px-6 xl:px-8">
                    <dt class="text-sm/6 font-medium text-gray-500">Keywords In Top 10</dt>

                    <dd class="w-full flex-none text-3xl/10 font-medium tracking-tight text-gray-900">1</dd>
                </div>

            </dl>
        </div>
    </div>

    <div class="relative overflow-hidden pt-6">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="border-b border-gray-200 pb-5">
                <h2 class="text-xl font-semibold text-gray-900">Content Published In May 2025</h2>
                <p class="mt-2 max-w-4xl text-sm text-gray-500">The content we have created and published for you in the
                    last 30 days is below.</p>
            </div>

            <livewire:recent-wordpress-posts :project="$project" :month="2025-03" />
        </div>
    </div>


</x-layouts.app>