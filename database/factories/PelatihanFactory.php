<?php

namespace Database\Factories;

use App\Models\Pelatihan;
use App\Models\Periode;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class PelatihanFactory extends Factory
{
    protected $model = Pelatihan::class;

    public function definition(): array
    {
//        $filename = $this->faker->image('public/storage/pelatihan-sampul', 640, 360, null, false);

        return [
            'periode_id' => Periode::get('id')->random(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'judul' => $this->faker->words(7, true),
//            'sampul' => 'pelatihan-sampul/' . $filename,
            'slug' => $this->faker->slug(),
            'deskripsi' => $this->faker->paragraph(),
            'tgl_mulai' => Carbon::now(),
            'tgl_selesai' => Carbon::now(),
            'jmlh_user' => $this->faker->randomNumber(),
        ];
    }
}
