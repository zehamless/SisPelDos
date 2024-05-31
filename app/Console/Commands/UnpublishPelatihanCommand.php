<?php

namespace App\Console\Commands;

use App\Models\Pelatihan;
use Illuminate\Console\Command;

class UnpublishPelatihanCommand extends Command
{
    protected $signature = 'unpublish:pelatihan';

    protected $description = 'Command description';

    public function handle(): void
    {
        $pelatihans = Pelatihan::where('published', true)->whereTime('tgl_selesai','>=', now())->get();
        foreach ($pelatihans as $pelatihan) {
            $pelatihan->update(['published' => false]);
        }
    }
}
