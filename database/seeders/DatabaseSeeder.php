<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\ChatbotDatas;
use App\Models\Kategori;
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
//        User::truncate();
//        Periode::truncate();
//        \Storage::deleteDirectory('pelatihan-sampul');
//        User::factory()->count(10)->create();
//        //make 1 user with admin role
//        User::factory()->admin()->create();
        Periode::factory()->count(2)->create();
        Kategori::factory()->count(10)->create();
        Pelatihan::factory()->count(10)->create();
        Modul::factory()->count(10)->create();
        MateriTugas::factory()->count(10)->create();
        User::create([
            'nama' => 'Admin',
            'email' => 'mahez@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);
        User::create([
            'nama' => 'Admin',
            'email' => 'batubattery25@gmail.com',
            'password' => Hash::make('password25'),
            'role' => 'admin',
        ]);
        $datas =[
            [
                'question' => 'Bagaimana cara membuat akun?',
                'answer' => 'Untuk membuat akun, silahkan klik tombol "Daftar" pada halaman login. ',
                'admin' => false,
            ],
            [
                'question' => 'Bagaimana cara mengubah password?',
                'answer' => 'Untuk mengubah password, silahkan klik tombol "Lupa Password" pada halaman login. ',
                'admin' => false,
            ],
            [
                'question' => 'Bagaimana cara mengubah email?',
                'answer' => 'Untuk mengubah email, silahkan hubungi admin. ',
                'admin' => false,
            ],
            [
                'question' => 'Bagaimana cara mengubah nama?',
                'answer' => 'Untuk mengubah nama, silahkan hubungi admin. ',
                'admin' => false,
            ],
            [
                'question' => 'Bagaimana cara mengubah foto profil?',
                'answer' => 'Untuk mengubah foto profil, silahkan hubungi admin. ',
                'admin' => false,
            ],
            [
                'question' => 'Bagaimana cara mengubah foto sampul?',
                'answer' => 'Untuk mengubah foto sampul, silahkan hubungi admin. ',
                'admin' => false,
            ],
            [
                'question' => 'Bagaimana cara mengubah data diri?',
                'answer' => 'Untuk mengubah data diri, silahkan hubungi admin. ',
                'admin' => false,
            ],
        ];
        ChatbotDatas::create($datas);
    }
}
