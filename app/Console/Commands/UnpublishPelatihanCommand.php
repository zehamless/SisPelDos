<?php

namespace App\Console\Commands;

use App\Models\Pelatihan;
use App\Models\User;
use Filament\Notifications\Notification;
use Illuminate\Console\Command;

class UnpublishPelatihanCommand extends Command
{
    protected $signature = 'unpublish:pelatihan';

    protected $description = 'Command description';

    public function handle(): void
    {
        $pelatihans = Pelatihan::where('published', true)->get();
        $yesterday = now()->subDay();
        foreach ($pelatihans as $pelatihan) {
            if ($pelatihan->tgl_selesai < now()){
            $pelatihan->update(['published' => false]);
            }elseif ($pelatihan->tgl_selesai < $yesterday){
                Notification::make()
                    ->title('Pelatihan akan segera berakhir')
                    ->body("Pelatihan {$pelatihan->nama} akan segera berakhir")
                    ->status('warning')
                    ->sendToDatabase(User::admin()->get());
                Notification::make()
                    ->title('Pelatihan akan segera berakhir')
                    ->body("Pelatihan {$pelatihan->nama} akan segera berakhir")
                    ->status('warning')
                    ->sendToDatabase(Pelatihan::find($pelatihan->id)->peserta()->get());

            }
        }
    }
}
