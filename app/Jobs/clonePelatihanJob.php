<?php

namespace App\Jobs;

use App\Models\Pelatihan;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class clonePelatihanJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(private readonly Pelatihan $pelatihan)
    {

    }

public function handle(): void
{
    DB::beginTransaction();
    try {
        $newPelatihan = $this->pelatihan->replicate();
        $newPelatihan->slug = $this->pelatihan->slug . '-copy'. '-' . now()->timestamp;
        $newPelatihan->save();

        $moduls = $this->pelatihan->modul;

        foreach ($moduls as $modul) {
            $newModulArray = collect($modul->toArray())->forget('id')->all();
            $newModulArray['slug'] = $newModulArray['slug'] . '-' . now()->timestamp;
            $newModul = $newPelatihan->modul()->create($newModulArray);

            $materiTugass = $modul->allTugas;
            foreach ($materiTugass as $materiTugas) {
                $newMateriTugasArray = collect($materiTugas->toArray())->forget('id')->all();
                $newModul->allTugas()->create($newMateriTugasArray);
            }
        }

        DB::commit();
    } catch (\Exception $e) {
        DB::rollBack();
        throw $e;
    }
}
}
