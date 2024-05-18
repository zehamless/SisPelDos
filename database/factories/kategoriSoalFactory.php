<?php

namespace Database\Factories;

use App\Models\kategoriSoal;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class kategoriSoalFactory extends Factory
{
    protected $model = kategoriSoal::class;

    public function definition(): array
    {
        return [
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'kategori' => $this->faker->word(),
        ];
    }
}
