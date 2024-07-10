<?php

namespace Database\Factories;

use App\Models\MateriTugas;
use App\Models\Modul;
use App\Models\Pelatihan;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class MateriTugasFactory extends Factory
{
    protected $model = MateriTugas::class;

    public function definition(): array
    {
        return [
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'judul' => $this->faker->word(),
            'deskripsi' => $this->faker->paragraph(),
            'files' => null,
            'jenis' => $this->faker->randomElement(['materi', 'tugas', 'kuis','diskusi']),
            'tgl_tenggat'=> Carbon::now()->addWeek(),
            'tgl_mulai' => Carbon::now(),
            'tgl_selesai' => Carbon::now()->addMonth(),
            'urutan' => $this->faker->randomNumber(),
            'modul_id' => Modul::first()->id,
        ];
    }
}
