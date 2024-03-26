<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto">
            <div tabindex="0" class="collapse collapse-arrow border border-base-300 bg-white">
                <div class="collapse-title text-xl font-medium">
                    <h2 class="font-bold">{{$materi->judul??' '}}</h2>
                </div>
                <div class="collapse-content">
                    <p>{!!$materi->deskripsi!!}</p>
                </div>
            </div>
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg my-2">
                <div class="overflow-x-auto">
                    <table class="table table-zebra">
                        <!-- head -->
                        <thead>
                        <tr>
                            <th></th>
                            <th>Judul</th>
                            <th>Jenis</th>
                            <th>Tanggal Mulai</th>
                            <th>Tanggal Selesai</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                        </thead>
                            <tbody>
                            <!-- row 1 -->
                            @forelse($materi->allTugas as $tugas)
                                <tr class="hover:bg-gray-50">
                                    <th>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                             viewBox="0 0 24 24"
                                             fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                             stroke-linejoin="round" class="lucide lucide-book">
                                            <path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1 0-5H20"/>
                                        </svg>
                                    </th>
                                    <td class="truncate max-w-xs">{{$tugas->judul}}</td>
                                    <td>{{$tugas->jenis}}</td>
                                    <td>{{ $tugas->tgl_mulai ? $tugas->tgl_mulai->format('d M Y') : '-' }}</td>
                                    <td>{{ $tugas->tgl_selesai ? $tugas->tgl_selesai->format('d M Y') : '-' }}</td>
                                    <td>
                                        <div class="badge badge-primary text-white">Dikerjakan</div>
                                    </td>
                                    <td><a class="link link-primary"
                                           href="{{route('materi.show', ['pelatihan'=>$materi->slug, 'materi'=>$tugas->id])}}">Buka</a>
                                    </td>
                                </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center">Tidak ada tugas atau materi</td>
                            </tr>
                            @endforelse
                            </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
