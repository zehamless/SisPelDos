<?php

namespace App\Jobs;

use App\Models\MateriTugas;
use App\Models\Pelatihan;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DetachUser_MateriTugasJob implements ShouldQueue
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

            $task->peserta()->detach($this->userId);
        }
    }
}
