<?php

namespace Database\Factories;

use App\Models\kuis;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class kuisFactory extends Factory
{
    protected $model = kuis::class;

    public function definition(): array
    {
        return [
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'pertanyaan' => $this->faker->words(),
            'jawaban' => $this->faker->words(),
        ];
    }
}
