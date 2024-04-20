<?php

namespace Database\Factories;

use App\Models\Modul;
use App\Models\Pelatihan;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class ModulFactory extends Factory
{
    protected $model = Modul::class;

    public function definition(): array
    {
        return [
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'judul' => $this->faker->word,
            'slug' => $this->faker->slug(),
            'deskripsi' => $this->faker->paragraph,
            'pelatihan_id' => Pelatihan::first()->id,
        ];
    }
}
