<?php

namespace Tests\Feature;

use App\Models\MateriTugas;
use App\Models\Pelatihan;
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
}
