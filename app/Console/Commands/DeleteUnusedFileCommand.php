<?php

namespace App\Console\Commands;

use App\Models\MateriTugas;
use App\Models\Pelatihan;
use App\Models\Pendaftaran;
use App\Models\Sertifikat;
use App\Models\Tugas;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class DeleteUnusedFileCommand extends Command
{
    protected $signature = 'delete:unused-file';

    protected $description = 'Command description';

    public function handle(): void
    {
        $this->deleteUnusedDaftar();
        $this->deleteUnusedSertifikat();
        $this->deleteUnusedMateri();
        $this->deleteUnusedPelatihan();
        $this->deleteUnusedTugas();
    }

    protected function deleteUnusedDaftar()
    {
        $daftar = Pendaftaran::pluck('files')->toArray();
        collect(Storage::disk('public')->allFiles('daftar'))
            ->reject(fn(string $file) => $file === '.gitignore')
            ->reject(fn(string $file) => in_array($file, $daftar))
            ->each(fn($file) => Storage::disk('public')->delete($file));
    }

    protected function deleteUnusedSertifikat()
    {
        $sertifikat = Sertifikat::pluck('files')->toArray();
        collect(Storage::disk('public')->allFiles('sertifikat'))
            ->reject(fn(string $file) => $file === '.gitignore')
            ->reject(fn(string $file) => in_array($file, $sertifikat))
            ->each(fn($file) => Storage::disk('public')->delete($file));
    }

    protected function deleteUnusedMateri()
    {
        $materiFiles = MateriTugas::materi()->pluck('files')->toArray();
        $flattenedMateriFiles = collect($materiFiles)->flatten()->filter()->all();
        collect(Storage::disk('public')->allFiles('materi'))
            ->reject(fn(string $file) => $file === '.gitignore')
            ->reject(function ($file) use ($flattenedMateriFiles) {
                return in_array($file, $flattenedMateriFiles);
            })
            ->each(function ($file) {
                Storage::disk('public')->delete($file);
            });
    }

    protected function deleteUnusedPelatihan()
    {
        $pelatihanSampul =Pelatihan::pluck('sampul')->toArray();
        collect(Storage::disk('public')->allFiles('pelatihan-sampul'))
            ->reject(fn(string $file) => $file === '.gitignore')
            ->reject(fn(string $file) => in_array($file, $pelatihanSampul))
            ->each(fn($file) => Storage::disk('public')->delete($file));
    }
    protected function deleteUnusedTugas()
    {
        $materiFiles = MateriTugas::tugas()->pluck('files')->toArray();
        $flattenedMateriFiles = collect($materiFiles)->flatten()->filter()->all();
        collect(Storage::disk('public')->allFiles('tugas'))
            ->reject(fn(string $file) => $file === '.gitignore')
            ->reject(function ($file) use ($flattenedMateriFiles) {
                return in_array($file, $flattenedMateriFiles);
            })
            ->each(function ($file) {
                Storage::disk('public')->delete($file);
            });
    }
}
