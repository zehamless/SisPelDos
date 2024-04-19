<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\MateriTugas;
use App\Models\Modul;
use App\Models\Pelatihan;
use App\Models\Periode;
use App\Models\User;
use Database\Factories\MateriTugasFactory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
        //delete all users
        User::truncate();
        Periode::truncate();
        \Storage::deleteDirectory('pelatihan-sampul');
        User::factory()->count(10)->create();
        //make 1 user with admin role
        User::factory()->admin()->create();
        Periode::factory()->count(2)->create();
        Pelatihan::factory()->count(10)->create();
        Modul::factory()->count(10)->create();
        MateriTugas::factory()->count(10)->create();
    }
}
