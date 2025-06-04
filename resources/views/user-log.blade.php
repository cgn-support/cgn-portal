<x-layouts.app>

    <div class="px-4 sm:px-6 lg:px-8">
        <div class="sm:flex sm:items-center">
            <div class="sm:flex-auto">
                <h1 class="text-base font-semibold text-gray-900">Users</h1>
                <p class="mt-2 text-sm text-gray-700">A list of all user events captured from your website.</p>
            </div>
            <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none">
                <a href="/project/{{ $project->id}}"
                   class="block rounded-full bg-orange-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                    Back To Project
                </a>
            </div>
        </div>
        <div class="mt-8 flow-root">
            <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                    <table class="min-w-full divide-y divide-gray-300">
                        <thead>
                        <tr>
                            <th scope="col"
                                class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-3">
                                Session ID
                            </th>
                            <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Event</th>
                            <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Page URL
                            </th>
                            <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Referrer
                            </th>
                            <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">UTMs
                            </th>
                        </tr>
                        </thead>
                        <tbody class="bg-white">
                        <tr class="even:bg-gray-50">
                            <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-3">
                                123456
                            </td>
                            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">Phone Call</td>
                            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">/kitchen-remodeling/
                            </td>
                            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">google.com</td>
                            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                <a href="" class="text-blue-600 border-b">Check For UTMs</a>
                            </td>

                        </tr>

                        <!-- More people... -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
