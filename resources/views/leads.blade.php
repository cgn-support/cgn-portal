<x-layouts.app>
    <div class="mt-4 grid grid-cols-1 gap-4">
        <div class="overflow-hidden aspect-video rounded-lg border border-neutral-200 dark:border-neutral-700">
            <div class="mt-8 flow-root">
                <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                    <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                        <div class="overflow-hidden shadow-sm ring-1 ring-black/5 sm:rounded-lg">
                            <table class="min-w-full divide-y divide-gray-300">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col"
                                            class="py-3.5 pr-3 pl-4 text-left text-sm font-semibold text-gray-900 sm:pl-6">
                                            Name
                                        </th>
                                        <th scope="col"
                                            class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Validate
                                        </th>
                                        <th scope="col"
                                            class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Value</th>

                                        <th scope="col"
                                            class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Date</th>
                                        <th scope="col" class="relative py-3.5 pr-4 pl-3 sm:pr-6">Save
                                            <span class="sr-only">Edit</span>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 bg-white">
                                    <tr>
                                        <td
                                            class="py-4 pr-3 pl-4 text-sm font-medium whitespace-nowrap text-gray-900 sm:pl-6">
                                            Lindsay Walton
                                        </td>
                                        <td
                                            class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">
                                            <select>
                                                <option>To Do</option>
                                                <option>Is Valid</option>
                                                <option>Low Quality / Spam</option>
                                            </select>
                                        </td>
                                        <td><input type="text" class="" placeholder="Value" /></td>


                                        <td class="px-3 py-4 text-sm whitespace-nowrap text-gray-500">05/12/2025</td>
                                        <td><input type="submit"
                                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
                                                value="Save" /></td>

                                    </tr>

                                    <!-- More people... -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{--    <div class="text-black dark:text-white mt-4"> --}}
                {{--        <div> --}}
                {{--            {!! $note->content !!} --}}
                {{--        </div> --}}
                {{--    </div> --}}
            </div>
        </div>
        {{--        <div class="overflow-hidden aspect-video rounded-lg border border-neutral-200 dark:border-neutral-700"> --}}
        {{--            <livewire:client-tasks-list :project="$project"/> --}}
        {{--        </div> --}}
    </div>

</x-layouts.app>
