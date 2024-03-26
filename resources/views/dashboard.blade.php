<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto ">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg ">
                <form class="flex flex-row p-6" method="get" action="{{route('user-dashboard')}}">
                    <input type="text" placeholder="Cari Judul" name="query" class="input input-bordered w-full"
                           id="searchPelatihan"/>
                    <button type="submit" class="btn mx-3" id="buttonSearchPelatihan">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                             fill="none" stroke="#000000" stroke-width="2" stroke-linecap="round"
                             stroke-linejoin="round" class="lucide lucide-search">
                            <circle cx="11" cy="11" r="8"/>
                            <path d="m21 21-4.3-4.3"/>
                        </svg>
                    </button>
                </form>
                <div class="p-6 text-gray-900 dark:text-gray-100 grid grid-cols-4 gap-6">
                    @foreach($pelatihan as $p)
                        <!-- Card -->
                        <div
                            class="group flex flex-col h-full bg-white border border-gray-200 shadow-sm rounded-xl dark:bg-slate-900 dark:border-gray-700 dark:shadow-slate-700/[.7]">
                            <div
                                class="flex flex-col justify-center items-center bg-blue-600 rounded-t-xl overflow-hidden">

                                <img src="{{asset('storage/' . $p->sampul)}}" alt="sampul" loading="lazy">
                            </div>
                            <div class="p-4 md:p-6 max-h-96">
        <span class="block mb-1 text-xs font-semibold uppercase text-red-600 dark:text-blue-500">
         End: {{$p->tgl_selesai->format('d M Y')}}
        </span>
                                <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-300 dark:hover:text-white text-ellipsis trunen">
                                    {{$p->judul}}
                                </h3>

                                    <p class="mt-3 text-gray-500 trunen ">
                                        {{$p->deskripsi}}
                                    </p>

                            </div>
                            <div
                                class="mt-auto flex border-t border-gray-200 divide-x divide-gray-200 dark:border-gray-700 dark:divide-gray-700">
                                <a class="w-full py-3 px-4 inline-flex justify-center items-center gap-x-2 text-sm font-medium rounded-es-xl bg-white text-gray-800 shadow-sm hover:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none dark:bg-slate-900 dark:border-gray-700 dark:text-white dark:hover:bg-gray-800 dark:focus:outline-none dark:focus:ring-1 dark:focus:ring-gray-600"
                                   href="{{route('pelatihan.show', $p->slug)}}">
                                    View
                                </a>
                                <a class="w-full py-3 px-4 inline-flex justify-center items-center gap-x-2 text-sm font-medium rounded-ee-xl bg-white text-gray-800 shadow-sm hover:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none dark:bg-slate-900 dark:border-gray-700 dark:text-white dark:hover:bg-gray-800 dark:focus:outline-none dark:focus:ring-1 dark:focus:ring-gray-600"
                                   href="#">
                                    View API
                                </a>
                            </div>
                        </div>
                        <!-- End Card -->
                    @endforeach
                </div>
                <div class="p-5 px-9">
                    {{$pelatihan->links('pagination::tailwind')}}
                </div>
            </div>
        </div>
    </div>
@push('scripts')
    <script>
        document.querySelectorAll('.trunen').forEach(element => {
            let words = element.innerText.split(' ', 11);
            if (words.length > 10) words[10] = '...';
            element.innerText = words.join(' ');
        });
    </script>

@endpush
</x-app-layout>
