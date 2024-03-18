<?php

namespace Database\Factories;

use App\Models\Pelatihan;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class PelatihanFactory extends Factory
{
    protected $model = Pelatihan::class;

    public function definition(): array
    {
        return [
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'judul' => $this->faker->word(),
            'sampul' => $this->faker->word(),
            'slug' => $this->faker->slug(),
            'deskripsi' => $this->faker->word(),
            'tgl_mulai' => Carbon::now(),
            'tgl_selesai' => Carbon::now(),
            'jmlh_user' => $this->faker->randomNumber(),
        ];
    }
}
