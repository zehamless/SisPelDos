<?php

namespace App\Http\Controllers;

use App\Filament\Resources\KuisResource;
use App\Filament\Resources\TugasResource;
use App\Models\Modul;
use DataTables;

class RekapController extends Controller
{
    public function indexModul(Modul $modul)
    {
        $modul->load('noMateri.peserta.mengerjakan', 'pelatihan.peserta');
        if (request()->ajax()) {
            $data = $modul->pelatihan->peserta->mapWithKeys(function ($peserta) use ($modul) {
                $penilaian = $modul->noMateri->mapWithKeys(function ($tugas) use ($peserta) {
                    $pesertaTugas = $tugas->peserta->where('id', $peserta->id)->first();
                    if ($pesertaTugas) {
                        $penilaianOrStatus = $pesertaTugas->pivot->penilaian ?? ($pesertaTugas->pivot->status === 'selesai' ? 'Belum Dinilai' : '-');
                        if ($penilaianOrStatus === 'Belum Dinilai') {
                            if ($tugas->jenis === 'tugas') {
                                $penilaianOrStatus = '<a href="' . TugasResource::getUrl('penilaian', ['record' => $tugas->id]) . '" target="_blank">Belum Dinilai</a>';
                            } else {
                                $penilaianOrStatus = '<a href="' . KuisResource::getUrl('penilaian', ['record' => $tugas->id]) . '" target="_blank">Belum Dinilai</a>';
                            }
                        }
                    } else {
                        $penilaianOrStatus = 'Belum';
                    }
                    return ['tugas_' . $tugas->id => $penilaianOrStatus];
                });
                return [['peserta' => $peserta->nama] + $penilaian->toArray()];
            });

            return DataTables::of($data)->rawColumns(array_merge(['peserta'], array_map(function ($tugas) {
                return 'tugas_' . $tugas->id;
            }, $modul->noMateri->all())))->make();
        }

        return view('rekapModul', compact('modul'));
    }
}
