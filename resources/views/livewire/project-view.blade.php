<div>
    <div class="overflow-hidden rounded-lg border border-neutral-200 dark:border-neutral-700">
        <livewire:project-details :project="$project"/>
    </div>

    <!-- Projects -->
    {{--    <div class="bg-white px-4 py-5 sm:px-6">--}}
    {{--        <div class="-ml-4 -mt-2 flex flex-wrap items-center justify-between sm:flex-nowrap">--}}
    {{--            <div class="ml-4 my-2">--}}
    {{--                <h2 class="text-2xl font-bold bg-[linear-gradient(130deg,#003E4A_0.69%,#112629_50.19%,#FC7B3E_79.69%)] bg-clip-text text-transparent">{{ __("My Leads") }}</h2>--}}


    {{--            </div>--}}

    {{--        </div>--}}

    {{--    <dl class="mt-4 border rounded-lg grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">--}}
    {{--        <livewire:last-period-phone-calls-count :project="$project"/>--}}
    {{--        <livewire:last-period-form-submissions-count :project="$project"/>--}}
    {{--        <livewire:last-period-google-sessions-count :project="$project"/>--}}
    {{--    </dl>--}}

    {{--            <div>--}}
    {{--                <div class=" grid grid-cols-1 gap-4 sm:grid-cols-2 sm:gap-6">--}}
    {{--                    <ul role="list" class="mt-3">--}}
    {{--                        <li class="relative col-span-1 flex rounded-md shadow-xs mt-2">--}}
    {{--                            <div--}}
    {{--                                class="flex w-16 shrink-0 items-center justify-center rounded-l-md bg-green-600 text-sm font-medium text-white">--}}
    {{--                                <svg class="mx-auto size-12" xmlns="http://www.w3.org/2000/svg" fill="none"--}}
    {{--                                     viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">--}}
    {{--                                    <path stroke-linecap="round" stroke-linejoin="round"--}}
    {{--                                          d="M7.5 14.25v2.25m3-4.5v4.5m3-6.75v6.75m3-9v9M6 20.25h12A2.25 2.25 0 0 0 20.25 18V6A2.25 2.25 0 0 0 18 3.75H6A2.25 2.25 0 0 0 3.75 6v12A2.25 2.25 0 0 0 6 20.25Z"/>--}}
    {{--                                </svg>--}}
    {{--                            </div>--}}
    {{--                            <div--}}
    {{--                                class="flex flex-1 items-center justify-between truncate rounded-r-md border-t border-r border-b border-gray-200 bg-white">--}}
    {{--                                <div class="flex-1 truncate px-4 py-2 text-sm">--}}
    {{--                                    <a href="#" class="font-medium text-gray-900 hover:text-gray-600">SEO--}}
    {{--                                        Project</a>--}}
    {{--                                    <p class="text-gray-500">--}}
    {{--                                                    <span--}}
    {{--                                                        class="inline-flex items-center gap-x-1.5 rounded-full bg-green-100 px-2 py-1 text-xs font-medium text-green-700">--}}
    {{--                          <svg class="size-1.5 fill-green-500" viewBox="0 0 6 6" aria-hidden="true">--}}
    {{--                            <circle cx="3" cy="3" r="3"/>--}}
    {{--                          </svg>--}}
    {{--                          On track--}}
    {{--                        </span>--}}
    {{--                                    </p>--}}
    {{--                                </div>--}}
    {{--                            </div>--}}
    {{--                        </li>--}}
    {{--                        <li class="relative col-span-1 flex rounded-md shadow-xs mt-2">--}}
    {{--                            <div--}}
    {{--                                class="flex w-16 shrink-0 items-center justify-center rounded-l-md bg-green-600 text-sm font-medium text-white">--}}
    {{--                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"--}}
    {{--                                     stroke-width="1.5"--}}
    {{--                                     stroke="currentColor" class="mx-auto size-12">--}}
    {{--                                    <path stroke-linecap="round" stroke-linejoin="round"--}}
    {{--                                          d="M9 17.25v1.007a3 3 0 0 1-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0 1 15 18.257V17.25m6-12V15a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 15V5.25m18 0A2.25 2.25 0 0 0 18.75 3H5.25A2.25 2.25 0 0 0 3 5.25m18 0V12a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 12V5.25"/>--}}
    {{--                                </svg>--}}
    {{--                            </div>--}}
    {{--                            <div--}}
    {{--                                class="flex flex-1 items-center justify-between truncate rounded-r-md border-t border-r border-b border-gray-200 bg-white">--}}
    {{--                                <div class="flex-1 truncate px-4 py-2 text-sm">--}}
    {{--                                    <a href="#" class="font-medium text-gray-900 hover:text-gray-600">Website--}}
    {{--                                        Project</a>--}}
    {{--                                    <p class="text-gray-500">--}}
    {{--                                                    <span--}}
    {{--                                                        class="inline-flex items-center gap-x-1.5 rounded-full bg-green-100 px-2 py-1 text-xs font-medium text-green-700">--}}
    {{--                          <svg class="size-1.5 fill-green-500" viewBox="0 0 6 6" aria-hidden="true">--}}
    {{--                            <circle cx="3" cy="3" r="3"/>--}}
    {{--                          </svg>--}}
    {{--                          On track--}}
    {{--                        </span>--}}
    {{--                                    </p>--}}
    {{--                                </div>--}}
    {{--                            </div>--}}
    {{--                        </li>--}}
    {{--                        <li class="relative col-span-1 flex rounded-md shadow-xs mt-2">--}}
    {{--                            <div--}}
    {{--                                class="flex w-16 shrink-0 items-center justify-center rounded-l-md bg-gray-600 text-sm font-medium text-white">--}}
    {{--                                <svg class="mx-auto size-12" fill="none" viewBox="0 0 24 24" stroke="currentColor"--}}
    {{--                                     aria-hidden="true">--}}
    {{--                                    <path vector-effect="non-scaling-stroke" stroke-linecap="round"--}}
    {{--                                          stroke-linejoin="round" stroke-width="2"--}}
    {{--                                          d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>--}}
    {{--                                </svg>--}}
    {{--                            </div>--}}
    {{--                            <div--}}
    {{--                                class="flex flex-1 items-center justify-between truncate rounded-r-md border-t border-r border-b border-gray-200 bg-white">--}}
    {{--                                <div class="flex-1 truncate px-4 py-2 text-sm">--}}
    {{--                                    <a href="#" class="font-medium text-gray-900 hover:text-gray-600">Branding--}}
    {{--                                        Project</a>--}}
    {{--                                    <p class="text-gray-500">--}}
    {{--                                                    <span--}}
    {{--                                                        class="inline-flex items-center gap-x-1.5 rounded-full bg-gray-100 px-2 py-1 text-xs font-medium text-gray-700">--}}
    {{--                          <svg class="size-1.5 fill-gray-500" viewBox="0 0 6 6" aria-hidden="true">--}}
    {{--                            <circle cx="3" cy="3" r="3"/>--}}
    {{--                          </svg>--}}
    {{--                          Not active--}}
    {{--                        </span>--}}
    {{--                                    </p>--}}
    {{--                                </div>--}}
    {{--                            </div>--}}
    {{--                        </li>--}}
    {{--                        <li class="relative col-span-1 flex rounded-md shadow-xs mt-2">--}}
    {{--                            <div--}}
    {{--                                class="flex w-16 shrink-0 items-center justify-center rounded-l-md bg-gray-600 text-sm font-medium text-white">--}}
    {{--                                <svg class="mx-auto size-12" fill="none" viewBox="0 0 24 24" stroke="currentColor"--}}
    {{--                                     aria-hidden="true">--}}
    {{--                                    <path vector-effect="non-scaling-stroke" stroke-linecap="round"--}}
    {{--                                          stroke-linejoin="round" stroke-width="2"--}}
    {{--                                          d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>--}}
    {{--                                </svg>--}}
    {{--                            </div>--}}
    {{--                            <div--}}
    {{--                                class="flex flex-1 items-center justify-between truncate rounded-r-md border-t border-r border-b border-gray-200 bg-white">--}}
    {{--                                <div class="flex-1 truncate px-4 py-2 text-sm">--}}
    {{--                                    <a href="#" class="font-medium text-gray-900 hover:text-gray-600">Video--}}
    {{--                                        Project</a>--}}
    {{--                                    <p class="text-gray-500">--}}
    {{--                                                    <span--}}
    {{--                                                        class="inline-flex items-center gap-x-1.5 rounded-full bg-gray-100 px-2 py-1 text-xs font-medium text-gray-700">--}}
    {{--                          <svg class="size-1.5 fill-gray-500" viewBox="0 0 6 6" aria-hidden="true">--}}
    {{--                            <circle cx="3" cy="3" r="3"/>--}}
    {{--                          </svg>--}}
    {{--                          Not active--}}
    {{--                        </span>--}}
    {{--                                    </p>--}}
    {{--                                </div>--}}
    {{--                            </div>--}}
    {{--                        </li>--}}
    {{--                    </ul>--}}
    {{--                    <div--}}
    {{--                        class="overflow-hidden aspect-video rounded-lg border border-neutral-200 dark:border-neutral-700">--}}

    {{--                    </div>--}}
    {{--                </div>--}}

    {{--            </div>--}}

    {{--    </div>--}}


    {{--    <div class="mt-4 grid grid-cols-1 gap-4">--}}
    {{--        <div class="overflow-hidden aspect-video rounded-lg border border-neutral-200 dark:border-neutral-700">--}}
    {{--            <livewire:latest-update :project="$project"/>--}}
    {{--        </div>--}}
    {{--        --}}{{--        <div class="overflow-hidden aspect-video rounded-lg border border-neutral-200 dark:border-neutral-700">--}}
    {{--        --}}{{--            <livewire:client-tasks-list :project="$project"/>--}}
    {{--        --}}{{--        </div>--}}
    {{--    </div>--}}

    {{--    <div class="mt-4 grid grid-cols-1 gap-4">--}}
    {{--        <div class="overflow-hidden aspect-video">--}}
    {{--            <div class="bg-white px-4 py-5 sm:px-6">--}}
    {{--                <div class="-ml-4 -mt-2 flex flex-wrap items-center justify-between sm:flex-nowrap">--}}
    {{--                    <div class="ml-4 mt-2">--}}
    {{--                        <h2 class="text-2xl font-bold bg-[linear-gradient(130deg,#003E4A_0.69%,#112629_50.19%,#FC7B3E_79.69%)] bg-clip-text text-transparent">{{ __("My Reports") }}</h2>--}}
    {{--                        <p>Your project reports are below starting with your most recent on top. Click to view.</p>--}}

    {{--                    </div>--}}
    {{--                    <!-- <div class="ml-4 mt-2 shrink-0">--}}
    {{--                        <a href="/project/{{ $project->id }}/updates" class="relative inline-flex items-center rounded-full bg-gray-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-gray-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">All Notes</a>--}}
    {{--                    </div> -->--}}
    {{--                </div>--}}

    {{--                <div class="mt-4">--}}
    {{--                    <livewire:project-reports :project="$project"/>--}}
    {{--                </div>--}}
    {{--            </div>--}}
    {{--        </div>--}}
    {{--    </div>--}}

</div>
