<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto ">
            {{\Diglactic\Breadcrumbs\Breadcrumbs::render('materi', $pelatihan, $materi)}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg ">
                <h1 class="font-bold text-center text-2xl py-2">{{$materi->judul}}</h1>
            </div>
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mt-4">
                <div class="p-2">{!!$materi->deskripsi!!}</div>
                <hr>
                <p class="p-2">File : </p>
                <ul class="list-disc p-2">
                    @forelse($materi->file_name ?? [] as $file => $name)
                        <li class="mx-6">
                            <a class="link link-secondary" href="{{route('download', basename($file))}}">{{ $name }}</a>
                        </li>
                    @empty
                        <li>No files found.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</x-app-layout>
