<main class="py-10 bg-gray-50 dark:bg-gray-800">
    <!-- Page header -->
    <div
        class="mx-auto max-w-3xl px-4 sm:px-6 md:flex md:items-center md:justify-between md:space-x-5 lg:max-w-7xl lg:px-8">
        <div class="flex items-center space-x-5">
            <div class="shrink-0">
                <div class="relative">
                    <img class="h-16 w-16 rounded-full"
                        src="https://files.monday.com/use1/photos/55207138/small/55207138-user_photo_2024_01_30_15_45_21.png?1706629521"
                        alt="{{ $project->accountManager->name ?? 'Account Manager' }}">
                    <span class="absolute inset-0 rounded-full shadow-inner" aria-hidden="true"></span>
                </div>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                    {{ $project->accountManager->name }}
                </h1>
                <p class="mt-1 text-sm font-medium text-gray-500 dark:text-gray-400">
                    Your CGN Account Manager since
                    <time datetime="2020-08-25">August 25, 2020</time>
                </p>
            </div>
        </div>
        <div
            class="mt-6 flex flex-col-reverse justify-stretch space-y-4 space-y-reverse sm:flex-row-reverse sm:justify-end sm:space-y-0 sm:space-x-3 sm:space-x-reverse md:mt-0 md:flex-row md:space-x-3">
            <a href="https://slack.com" target="_blank"
                class="inline-flex items-center justify-center rounded-md bg-white px-4 py-2 text-sm font-medium text-gray-900 shadow ring-1 ring-gray-300 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:bg-gray-700 dark:text-gray-100 dark:ring-gray-600 dark:hover:bg-gray-600">
                Chat With Team
            </a>
            <a href="https://www.local-marketing-reports.com/location-dashboard/c567222026bcf219cee102a98dc43fd811bdd61c/summary"
                target="_blank"
                class="inline-flex items-center justify-center rounded-md bg-orange-600 px-4 py-2 text-sm font-medium text-white shadow hover:bg-orange-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">
                My Local SEO
            </a>
        </div>
    </div>

    <div class="mx-auto mt-8 max-w-7xl grid grid-cols-1 gap-6 sm:px-6 lg:grid-cols-12">
        <!-- Left: Project Info (spans 8/12 at lg and above) -->
        <div class="space-y-6 lg:col-span-8">
            <!-- Project Info Card -->
            <section aria-labelledby="project-information-title">
                <div class="bg-white shadow-sm rounded-lg">
                    <!-- Header with subtle bg and bottom border -->
                    <div class="px-6 py-6 bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
                        <h2 id="project-information-title" class="text-lg font-semibold text-gray-900 dark:text-white">
                            {{ $name }}
                        </h2>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            Project Start Date {{ date('F j, Y', strtotime($project->project_start_date)) }}
                        </p>
                    </div>

                    <!-- Details -->
                    <div class="px-6 py-6 sm:px-6">
                        <dl class="grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-2">
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-semibold text-gray-700 dark:text-gray-300">Website</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                    <a href="{{ $project->project_url }}"
                                        class="text-indigo-600 hover:underline dark:text-indigo-400">
                                        {{ $project->project_url }}
                                    </a>
                                </dd>
                            </div>
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-semibold text-gray-700 dark:text-gray-300">Address</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                    {{ $business->address_line1 }}
                                    @if ($business->address_line2)
                                        {{ $business->address_line2 }}
                                    @endif,
                                    {{ $business->city }}, {{ $business->state }}
                                    {{ $business->zip_code }}
                                </dd>
                            </div>
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-semibold text-gray-700 dark:text-gray-300">Phone Number</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                    {{ $business->phone_number }}
                                </dd>
                            </div>
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-semibold text-gray-700 dark:text-gray-300">Maps Link</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                    <a href="{{ $business->gbp_link }}"
                                        class="text-indigo-600 hover:underline dark:text-indigo-400">
                                        {{ $business->google_maps_url }}
                                    </a>
                                </dd>
                            </div>
                            <div class="sm:col-span-2">
                                <dt class="text-sm font-semibold text-gray-700 dark:text-gray-300">Shared Success Goal
                                </dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                    {{ $business->project_goal }}
                                </dd>
                            </div>
                            <div class="sm:col-span-2">
                                <dt class="text-sm font-semibold text-gray-700 dark:text-gray-300">Attachments</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                    <ul role="list"
                                        class="divide-y divide-gray-200 dark:divide-gray-600 rounded-md border border-gray-200 dark:border-gray-600">
                                        <li class="flex items-center justify-between py-2 px-3">
                                            <div class="flex w-0 flex-1 items-center space-x-2">
                                                <svg class="h-5 w-5 text-gray-400 dark:text-gray-500"
                                                    viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                    <path fill-rule="evenodd"
                                                        d="M15.621 4.379a3 3 0 0 0-4.242 0l-7 7a3 3 0 0 0 4.241 4.243h.001l.497-.5a.75.75 0 0 1 1.064 1.057l-.498.501-.002.002a4.5 4.5 0 0 1-6.364-6.364l7-7a4.5 4.5 0 0 1 6.368 6.36l-3.455 3.553A2.625 2.625 0 1 1 9.52 9.52l3.45-3.451a.75.75 0 1 1 1.061 1.06l-3.45 3.451a1.125 1.125 0 0 0 1.587 1.595l3.454-3.553a3 3 0 0 0 0-4.242Z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                                <span class="truncate">marketing_contract.pdf</span>
                                            </div>
                                            <div class="ml-4 flex-shrink-0">
                                                <a href="#"
                                                    class="font-medium text-indigo-600 hover:text-indigo-500 dark:text-indigo-400 dark:hover:text-indigo-300">
                                                    Download
                                                </a>
                                            </div>
                                        </li>
                                        <li class="flex items-center justify-between py-2 px-3">
                                            <div class="flex w-0 flex-1 items-center space-x-2">
                                                <svg class="h-5 w-5 text-gray-400 dark:text-gray-500"
                                                    viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                    <path fill-rule="evenodd"
                                                        d="M15.621 4.379a3 3 0 0 0-4.242 0l-7 7a3 3 0 0 0 4.241 4.243h.001l.497-.5a.75.75 0 0 1 1.064 1.057l-.498.501-.002.002a4.5 4.5 0 0 1-6.364-6.364l7-7a4.5 4.5 0 0 1 6.368 6.36l-3.455 3.553A2.625 2.625 0 1 1 9.52 9.52l3.45-3.451a.75.75 0 1 1 1.061 1.06l-3.45 3.451a1.125 1.125 0 0 0 1.587 1.595l3.454-3.553a3 3 0 0 0 0-4.242Z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                                <span class="truncate">project_start_ticket.pdf</span>
                                            </div>
                                            <div class="ml-4 flex-shrink-0">
                                                <a href="#"
                                                    class="font-medium text-indigo-600 hover:text-indigo-500 dark:text-indigo-400 dark:hover:text-indigo-300">
                                                    Download
                                                </a>
                                            </div>
                                        </li>
                                    </ul>
                                </dd>
                            </div>
                        </dl>
                    </div>

                    <!-- Open Drive Folder Button -->
                    <div class="border-t border-gray-200 dark:border-gray-600 px-6 py-4">
                        <a href="#"
                            class="block text-center text-sm font-medium text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                            Open Google Drive Folder
                        </a>
                    </div>
                </div>
            </section>
        </div>

        <!-- Right: Project Stages (spans 4/12 at lg and above) -->
        <section aria-labelledby="timeline-title" class="lg:col-span-4">
            <div class="bg-white p-6 shadow-sm rounded-lg dark:bg-gray-700">
                <h2 id="timeline-title" class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                    Project Statuses
                </h2>

                <!-- Timeline -->
                <div class="mt-6 flow-root">
                    <ul role="list" class="-mb-8">
                        <!-- Onboarding -->
                        <li>
                            <div class="relative pb-6">
                                <span class="absolute top-4 left-4 -ml-px h-full w-1.5 bg-gray-200 dark:bg-gray-600"
                                    aria-hidden="true"></span>
                                <div class="relative flex items-center space-x-3">
                                    <div>
                                        <span
                                            class="flex h-8 w-8 items-center justify-center rounded-full bg-green-500 ring-8 ring-white dark:ring-gray-700">
                                            <svg class="h-4 w-4 text-white" viewBox="0 0 20 20" fill="currentColor"
                                                aria-hidden="true">
                                                <path fill-rule="evenodd"
                                                    d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        </span>
                                    </div>
                                    <div class="flex min-w-0 flex-1 flex-col space-y-1 pl-4">
                                        <p class="text-sm font-semibold text-gray-700 dark:text-gray-300">Onboarding
                                        </p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Onboard Complete</p>
                                    </div>
                                    <div class="text-right text-sm text-gray-500 dark:text-gray-400">
                                        <time datetime="2020-09-20">Sep 20</time>
                                    </div>
                                </div>
                            </div>
                        </li>

                        <!-- SEO Project -->
                        <li>
                            <div class="relative pb-6">
                                <span class="absolute top-4 left-4 -ml-px h-full w-1.5 bg-gray-200 dark:bg-gray-600"
                                    aria-hidden="true"></span>
                                <div class="relative flex items-center space-x-3">
                                    <div>
                                        <span
                                            class="flex h-8 w-8 items-center justify-center rounded-full bg-green-500 ring-8 ring-white dark:ring-gray-700">
                                            <svg class="h-4 w-4 text-white" viewBox="0 0 20 20" fill="currentColor"
                                                aria-hidden="true">
                                                <path fill-rule="evenodd"
                                                    d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        </span>
                                    </div>
                                    <div class="flex min-w-0 flex-1 flex-col space-y-1 pl-4">
                                        <p class="text-sm font-semibold text-gray-700 dark:text-gray-300">SEO Project
                                        </p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Strategy</p>
                                    </div>
                                    <div class="text-right text-sm text-gray-500 dark:text-gray-400">
                                        <time datetime="2020-09-22">Sep 22</time>
                                    </div>
                                </div>
                            </div>
                        </li>

                        <!-- Website Project -->
                        <li>
                            <div class="relative pb-6">
                                <span class="absolute top-4 left-4 -ml-px h-full w-1.5 bg-gray-200 dark:bg-gray-600"
                                    aria-hidden="true"></span>
                                <div class="relative flex items-center space-x-3">
                                    <div>
                                        <span
                                            class="flex h-8 w-8 items-center justify-center rounded-full bg-blue-500 ring-8 ring-white dark:bg-blue-700 dark:ring-gray-700">
                                            <svg class="h-4 w-4 text-white" viewBox="0 0 20 20" fill="currentColor"
                                                aria-hidden="true">
                                                <path fill-rule="evenodd"
                                                    d="M10 2a8 8 0 018 8h-2l3 3-3 3v-2a6 6 0 10-6-6h2l-3-3 3-3v2z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        </span>

                                    </div>
                                    <div class="flex min-w-0 flex-1 flex-col space-y-1 pl-4">
                                        <p class="text-sm font-semibold text-gray-700 dark:text-gray-300">Website
                                            Project</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Building</p>
                                    </div>
                                    <div class="text-right text-sm text-gray-500 dark:text-gray-400">
                                        <time datetime="2020-09-28">Sep 28</time>
                                    </div>
                                </div>
                            </div>
                        </li>

                        <!-- Branding Project (last item: no bottom padding, no line) -->
                        <li>
                            <div class="relative">
                                <div class="relative flex items-center space-x-3">
                                    <div>
                                        <span
                                            class="flex h-8 w-8 items-center justify-center rounded-full bg-gray-300 ring-8 ring-white dark:bg-gray-600 dark:ring-gray-700">
                                            <svg class="h-4 w-4 text-gray-600 dark:text-gray-400" viewBox="0 0 20 20"
                                                fill="none" stroke="currentColor" stroke-width="2"
                                                stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                <circle cx="10" cy="10" r="8" />
                                                <line x1="4" y1="16" x2="16" y2="4" />
                                            </svg>
                                        </span>

                                    </div>
                                    <div class="flex min-w-0 flex-1 flex-col space-y-1 pl-4">
                                        <p class="text-sm font-semibold text-gray-700 dark:text-gray-300">Branding
                                            Project</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Designing</p>
                                    </div>
                                    <div class="text-right text-sm text-gray-500 dark:text-gray-400">
                                        <time datetime="2020-10-04">Oct 4</time>
                                    </div>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>

                <!-- Action Buttons -->
                <div class="mt-14 space-y-3">
                    <a href="{{ route('project.reports', ['uuid' => $project->id]) }}"
                        class="inline-flex w-full items-center justify-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        View Reports
                    </a>
                    <a href="{{ route('project.map', ['uuid' => $project->id ?? 'default-uuid']) }}"
                        class="inline-flex w-full items-center justify-center rounded-md bg-orange-600 px-4 py-2 text-sm font-medium text-white shadow hover:bg-orange-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">
                        Project Gantt Chart
                    </a>
                </div>
            </div>
        </section>

    </div>
</main>




{{-- <div class="p-4"> --}}
{{-- <div class="flex flex-col space-y-4"> --}}

{{-- <!-- Row 1: Project Name and Account Manager --> --}}
{{-- <div id="project-name-account-manager-block" class="flex justify-between items-center"> --}}

{{-- <h1 class="border-b pb-2 text-2xl font-bold text-gray-900 dark:text-white sm:text-3xl sm:tracking-tight">{{ $name }}</h1> --}}


{{-- <div class="flex flex-col items-center"> --}}
{{-- <div class="w-20 h-20 rounded-full mb-2 overflow-hidden"> --}}
{{-- <img src="{{ $pulseData['accountManagerPhoto'] ?? asset('images/default-avatar.png') }}" --}}
{{-- alt="{{ $pulseData['accountManagerName'] ?? 'Account Manager' }}" --}}
{{-- class="w-full h-full object-cover"> --}}
{{-- </div> --}}
{{-- <span --}}
{{-- class="text-gray-900 dark:text-white font-medium mb-2">{{ $pulseData['accountManagerName'] ?? 'none' }}</span> --}}
{{-- <span><a href="#" class="p-2 bg-orange-600 text-white font-bold rounded-full">Contact</a></span> --}}
{{-- </div> --}}
{{-- </div> --}}

{{-- <div> --}}
{{-- <dl class="grid grid-cols-1 sm:grid-cols-2"> --}}
{{-- <div class="px-4 py-6 sm:col-span-1 sm:px-0"> --}}
{{-- <dt class="flex text-sm/6 font-medium text-gray-700"> --}}
{{-- <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" --}}
{{-- stroke-width="1.5" --}}
{{-- stroke="currentColor" class="size-6 mr-2"> --}}
{{-- <path stroke-linecap="round" stroke-linejoin="round" --}}
{{-- d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/> --}}
{{-- <path stroke-linecap="round" stroke-linejoin="round" --}}
{{-- d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z"/> --}}
{{-- </svg> --}}
{{-- Address --}}
{{-- </dt> --}}
{{-- <dd class="mt-1 text-sm/6 text-gray-700 sm:mt-2">{{ $business->address_one . " " . $business->address_two . " " . $business->city . ", " . $business->state . " " . $business->zip_code }}</dd> --}}
{{-- </div> --}}
{{-- <div class="border-t border-gray-100 px-4 py-6 sm:col-span-1 sm:px-0"> --}}
{{-- <dt class="flex text-sm/6 font-medium text-gray-700"> --}}
{{-- <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" --}}
{{-- stroke-width="1.5" stroke="currentColor" class="size-6 mr-2"> --}}
{{-- <path stroke-linecap="round" stroke-linejoin="round" --}}
{{-- d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z"/> --}}
{{-- </svg> --}}
{{-- Phone Number --}}
{{-- </dt> --}}
{{-- <dd class="mt-1 text-sm/6 text-gray-700 sm:mt-2">{{ $business->phone_number }}</dd> --}}
{{-- </div> --}}
{{-- <div class="border-t border-gray-100 px-4 py-6 sm:col-span-1 sm:px-0"> --}}
{{-- <dt class="flex text-sm/6 font-medium text-gray-700"> --}}
{{-- <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" --}}
{{-- stroke-width="1.5" stroke="currentColor" class="size-6 mr-2"> --}}
{{-- <path stroke-linecap="round" stroke-linejoin="round" --}}
{{-- d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/> --}}
{{-- </svg> --}}
{{-- Google Business Profile --}}
{{-- </dt> --}}
{{-- <dd class="mt-1 text-sm/6 text-gray-700 sm:mt-2"><a class="text-blue-500 underline" --}}
{{-- href="{{$business->gbp_link}}">{{ $business->gbp_link }}</a> --}}
{{-- </dd> --}}
{{-- </div> --}}
{{-- <div class="border-t border-gray-100 px-4 py-6 sm:col-span-1 sm:px-0"> --}}
{{-- <dt class="flex text-sm/6 font-medium text-gray-700"> --}}
{{-- <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" --}}
{{-- stroke="currentColor" class="size-6 mr-2"> --}}
{{-- <path stroke-linecap="round" stroke-linejoin="round" --}}
{{-- d="M12 21a9.004 9.004 0 0 0 8.716-6.747M12 21a9.004 9.004 0 0 1-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 0 1 7.843 4.582M12 3a8.997 8.997 0 0 0-7.843 4.582m15.686 0A11.953 11.953 0 0 1 12 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0 1 21 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0 1 12 16.5c-3.162 0-6.133-.815-8.716-2.247m0 0A9.015 9.015 0 0 1 3 12c0-1.605.42-3.113 1.157-4.418"/> --}}
{{-- </svg> --}}

{{-- Website Status --}}
{{-- </dt> --}}
{{-- <livewire:get-website-status/> --}}
{{-- </div> --}}
{{-- <div class="border-t border-gray-100 px-4 py-6 sm:col-span-2 sm:px-0"> --}}
{{-- <dt class="text-lg font-medium text-gray-900">Goal</dt> --}}
{{-- <dd class="mt-1 text-sm/6 text-gray-700 sm:mt-2">{{ $business->project_goal }}</dd> --}}
{{-- </div> --}}
{{-- </dl> --}}
{{-- </div> --}}

{{-- </div> --}}

{{-- </div> --}}
