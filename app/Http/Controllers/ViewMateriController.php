<?php

namespace App\Http\Controllers;

use App\Models\MateriTugas;

class ViewMateriController extends Controller
{
    public function __invoke($slug, MateriTugas $materi)
    {
        $pelatihan = $materi->pelatihan;
        activity()
            ->by(auth()->user())
            ->on($materi)
            ->log('membuka materi '.$materi->judul);
        return view('user.materi', compact('materi', 'pelatihan'));
    }
}
