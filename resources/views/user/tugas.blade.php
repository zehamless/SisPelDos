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
                <div class="grid grid-cols-2">

                    <div class="border">
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
                    <div class=" p-2 border">
                        <p class="pb-4 font-bold">Tugas: </p>
                        @if($materi->tgl_selesai->isFuture())
                            <p >Masa Tenggat: <span class="badge badge-accent">{{ now()->diff($materi->tgl_selesai)->format('%d days %h hours'). ' : ' . $materi->tgl_selesai }}</span></p>
                        @else
                            <p >Masa Tenggat: <span class="badge badge-accent">{{ $materi->tgl_selesai }}</span></p>
                        @endif
                       <p class="">
                           Grade: <span class="badge badge-primary">A+</span>
                       </p>
                        <p class="">
                            Status: <span class="badge badge-primary">Dikerjakan</span>
                        </p>
                    <form class="pt-5" action="{{route('tugas.mengerjakan', $materi->id)}}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input name="slug" hidden="true" value="{{$slug}}">
                        <input type="file" id="tugasUpload"  class="file-input file-input-bordered file-input-sm w-full max-w-xs" name="file" required/>
                        <button type="submit" class="btn btn-neutral btn-sm ">Submit</button>
                    </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
