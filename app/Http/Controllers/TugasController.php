<?php

namespace App\Http\Controllers;

use App\Models\MateriTugas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TugasController extends Controller
{
    public function index($slug, MateriTugas $materi)
    {
        $pelatihan = $materi->pelatihan;
//        dd($pelatihan);
//        activity()
//            ->by(auth()->user())
//            ->on($materi)
//            ->log('membuka materi '.$materi->judul);
        return view('user.tugas', compact('materi', 'pelatihan', 'slug'));
    }
    public function mengerjakan(Request $request, MateriTugas $materi)
    {
        $this->validate($request, [
            'file' => 'required|max:2048'
        ]);
        $slug= $request->slug;
        $user = auth()->user();
        $file = $request->file('file');
//        dd($file);
        $filePath= $file->store('tugas', 'public');
        $user->mengerjakan()->attach($materi->id, ['file' => $filePath]);
        return redirect()->route('tugas.show', ['pelatihan' => $slug, 'materi' => $materi->id]);
    }
}
