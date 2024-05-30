<?php

namespace App\Jobs;

use App\Models\MateriTugas;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class TerjadwalJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct()
    {
    }

    public function handle(): void
    {
        $materiTugas = MateriTugas::terjadwal()->get();
        foreach ($materiTugas as $task){
            $task->update(['published' => true]);
        }
    }
}
