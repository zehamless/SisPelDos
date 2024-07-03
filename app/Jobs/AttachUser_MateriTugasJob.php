<?php

namespace App\Jobs;

use App\Models\MateriTugas;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class AttachUser_MateriTugasJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    private $users;
    public function __construct(private readonly MateriTugas $materiTugas, $users)
    {
        $this->users = $users;
    }

    public function handle(): void
    {
        $this->materiTugas->peserta()->sync($this->users);
    }
}
