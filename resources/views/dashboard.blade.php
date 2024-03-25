<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto ">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg ">
                <div class="p-6 text-gray-900 dark:text-gray-100 grid grid-cols-4 gap-6" >
                    @foreach($pelatihan as $p)
                        <!-- Card -->
                        <div class="group flex flex-col h-full bg-white border border-gray-200 shadow-sm rounded-xl dark:bg-slate-900 dark:border-gray-700 dark:shadow-slate-700/[.7]">
                            <div class="flex flex-col justify-center items-center bg-blue-600 rounded-t-xl overflow-hidden">
                               <img src="https://upload.wikimedia.org/wikipedia/commons/7/7c/Aspect_ratio_16_9_example.jpg">
                            </div>
                            <div class="p-4 md:p-6">
        <span class="block mb-1 text-xs font-semibold uppercase text-blue-600 dark:text-blue-500">
          Atlassian API
        </span>
                                <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-300 dark:hover:text-white">
                                    Atlassian
                                </h3>
                                <p class="mt-3 text-gray-500">
                                    A software that develops products for software developers and developments.
                                </p>
                            </div>
                            <div class="mt-auto flex border-t border-gray-200 divide-x divide-gray-200 dark:border-gray-700 dark:divide-gray-700">
                                <a class="w-full py-3 px-4 inline-flex justify-center items-center gap-x-2 text-sm font-medium rounded-es-xl bg-white text-gray-800 shadow-sm hover:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none dark:bg-slate-900 dark:border-gray-700 dark:text-white dark:hover:bg-gray-800 dark:focus:outline-none dark:focus:ring-1 dark:focus:ring-gray-600" href="#">
                                    View sample
                                </a>
                                <a class="w-full py-3 px-4 inline-flex justify-center items-center gap-x-2 text-sm font-medium rounded-ee-xl bg-white text-gray-800 shadow-sm hover:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none dark:bg-slate-900 dark:border-gray-700 dark:text-white dark:hover:bg-gray-800 dark:focus:outline-none dark:focus:ring-1 dark:focus:ring-gray-600" href="#">
                                    View API
                                </a>
                            </div>
                        </div>
                        <!-- End Card -->
                    @endforeach
                </div>
            </div>
                    {{$pelatihan->links('pagination::tailwind')}}

        </div>
    </div>
</x-app-layout>
