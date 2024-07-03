<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class AttachTugasKuisJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $userId;
    protected $tasks;

    public function __construct($userId, $tasks)
    {
        $this->userId = $userId;
        $this->tasks = $tasks;
    }

    public function handle()
    {
        foreach ($this->tasks as $task) {
            $isKuis = $task->jenis === 'kuis';
            $task->peserta()->syncWithPivotValues($this->userId, [
                'is_kuis' => $isKuis,
            ]);
        }
    }
}
