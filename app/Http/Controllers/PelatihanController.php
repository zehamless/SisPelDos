<?php

namespace App\Http\Controllers;

use App\Models\MateriTugas;
use App\Models\Pelatihan;

class PelatihanController extends Controller
{
    public function __invoke($slug)
    {
        $materi = Pelatihan::with('allTugas:id,judul,pelatihan_id,jenis,tgl_mulai,tgl_selesai')->where('slug', $slug)->firstOrFail(['id', 'judul', 'deskripsi']);
        return view('user.pelatihan', compact('materi'));
    }
}
