<?php

namespace Database\Factories;

use App\Models\MateriTugas;
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
            'deskripsi' => $this->faker->word(),
            'files' => $this->faker->words(),
            'jenis' => $this->faker->word(),
            'tipe' => $this->faker->word(),
            'tgl_mulai' => Carbon::now(),
            'tgl_selesai' => Carbon::now(),
            'urutan' => $this->faker->randomNumber(),
        ];
    }
}
