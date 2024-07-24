<?php

namespace App\Jobs;

use App\Models\MateriTugas;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class cloneKuisJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private MateriTugas $materiTugas;

    public function __construct(MateriTugas $materiTugas)
    {
        $this->materiTugas = $materiTugas;
    }

public function handle(): void
{
    DB::beginTransaction();
    try {
        // Replicate the MateriTugas instance
        $newMateriTugas = $this->materiTugas->replicate();
        $newMateriTugas->save();

        // Assuming 'kuis' is a many-to-many relationship, we need to replicate this relationship
        $kuisIds = $this->materiTugas->kuis()->pluck('kuis_id');
        $newMateriTugas->kuis()->attach($kuisIds);

        DB::commit();
    } catch (\Exception $e) {
        DB::rollBack();
        throw $e;
    }
}
}
