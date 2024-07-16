<?php

namespace App\Jobs;

use App\Models\Pelatihan;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class unpublishPelatihanJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(private readonly Pelatihan $pelatihan)
    {
    }

    /**
     * @throws \Exception
     */
    public function handle(): void
    {
        \DB::beginTransaction();
        try {
            foreach ($this->pelatihan->modul as $modul) {
                $modul->allTugas()->update(['published' => false]);
                $modul->update(['published' => false]);
            }
            $this->pelatihan->update(['published' => false]);
            \DB::commit();
        } catch (\Exception $e) {
            \DB::rollBack();
            throw $e;
        }
    }
}
