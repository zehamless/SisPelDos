<?php

namespace App\Http\Controllers;

use App\Models\MateriTugas;

class ViewMateriController extends Controller
{
    public function __invoke($lug, MateriTugas $materi)
    {
        activity()
            ->by(auth()->user())
            ->on($materi)
            ->log('membuka materi '.$materi->judul);
        return view('user.materi', compact('materi'));
    }
}
