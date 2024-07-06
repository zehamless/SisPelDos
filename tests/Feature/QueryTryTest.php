<?php

namespace Tests\Feature;

use App\Jobs\AttachTugasKuisJob;
use App\Jobs\clonePelatihanJob;
use App\Models\kuis;
use App\Models\MateriTugas;
use App\Models\Pelatihan;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class QueryTryTest extends TestCase
{
    public function testBasic()
    {
        $userId = '01hy27vmpgjyap0ewt2fxppckw'; // Replace with the actual user ID
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
        $userId = '01hy27vmpgjyap0ewt2fxppckw'; // Replace with the actual user ID
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
        $materiFiles = MateriTugas::materi()->pluck('files')->toArray();
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
        $user = User::admin()->first();
        $query = MateriTugas::whereHas('modul.pelatihan.peserta', function ($query) use ($user) {
            $query->where('users_id', $user->id);
        })->get();

        dump($query);
    }

    public function testQuery1()
    {
        $kuis = kuis::with('kategories')->get();
        dd($kuis);
    }

    public function testJob()
    {
        $modul = Pelatihan::find(1)->allTugas;
        $user = User::first();
        $err = dispatch(new AttachTugasKuisJob($user, $modul));
        dd($err);
//        $modul = Pelatihan::find(1)->peserta()->syncWithPivotValues();
    }
}
