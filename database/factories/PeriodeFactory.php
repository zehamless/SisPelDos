<?php

namespace Database\Factories;

use App\Models\Periode;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class PeriodeFactory extends Factory
{
    protected $model = Periode::class;

    public function definition(): array
    {
        return [
            'tahun' => $this->faker->randomNumber(),
            'deleted_at' => Carbon::now(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
