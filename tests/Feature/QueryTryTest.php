<?php

namespace Tests\Feature;

use App\Models\MateriTugas;
use App\Models\Pelatihan;
use App\Models\Pendaftaran;
use App\Models\Periode;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class QueryTryTest extends TestCase
{
    public function testBasic()
    {
        $userId ='01hy27vmpgjyap0ewt2fxppckw'; // Replace with the actual user ID
        $pelatihanId = 1; // Replace with the actual Pelatihan ID
        $completedTugasCount = MateriTugas::whereHas('modul.pelatihan', function ($query) use ($pelatihanId) {
            $query->where('pelatihan_id', $pelatihanId);
        })->whereHas('peserta', function ($query) use ($userId) {
            $query->where('users_id', $userId);
        })->get();

        self::assertIsNotObject($completedTugasCount);
        dump($completedTugasCount);
    }
    public function testSudahDikerjakan()
    {
        $userId ='01hy27vmpgjyap0ewt2fxppckw'; // Replace with the actual user ID
        $pelatihanId = 1; // Replace with the actual Pelatihan ID

        $completedTugasCount = MateriTugas::whereHas('modul.pelatihan', function ($query) use ($pelatihanId) {
            $query->where('pelatihan_id', $pelatihanId);
        })->whereHas('peserta', function ($query) use ($userId) {
            $query->where('users_id', $userId);
        })->count();
        self::assertEquals(2, $completedTugasCount);
        dump($completedTugasCount);
    }

    public function testSchedule()
    {
        $materiFiles = MateriTugas::materi()->pluck('files')->toArray();;
        $flattenedMateriFiles = collect($materiFiles)->flatten()->filter()->all();
        $po = collect(Storage::disk('public')->allFiles('materi'))
            ->reject(fn(string $file) => $file === '.gitignore') // Ignore .gitignore
            ->reject(function ($file) use ($flattenedMateriFiles) {
                return in_array($file, $flattenedMateriFiles); // Reject if the file is in materiFiles
            })
            ->each(function ($file) {
                Storage::disk('public')->delete($file); // Delete the file
            });
        self::assertIsNotArray($materiFiles);
    }
    public function testQuery()
    {
        $periode = Periode::all();
        $data = [];
        $labels = [];
        foreach ($periode as $p) {
            $data[] = $p->peserta()->count();
            $labels[] = $p->tahun_ajar;
        }
        dump($labels);
    }
}
