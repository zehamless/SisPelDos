<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'password' => Hash::make('password'), // password
            'no_induk' => $this->faker->unique()->numerify('########'),
            'no_hp' => '09999999',
            'jenis_kelamin' => $this->faker->randomElement(['L', 'P']),
            'universitas' => $this->faker->company,
            'prodi' => $this->faker->jobTitle,
            'jabatan_fungsional' => $this->faker->jobTitle,
            'pendidikan_tertinggi' => $this->faker->randomElement(['S1', 'S2', 'S3']),
            'status_kerja' => $this->faker->randomElement(['Aktif', 'Non-aktif']),
            'status_dosen' => $this->faker->randomElement(['Aktif', 'Non-aktif']),
            'status_akun' => $this->faker->randomElement(['Aktif', 'Non-aktif']),
            'pembayaran' => $this->faker->randomElement(['Lunas', 'Belum Lunas']),
        ];
    }
    public function admin()
    {
        return $this->state([
            'nama' => $this->faker->name,
            'email' => 'admin@admin.com',
            'password' => Hash::make('password'), // password
            'no_induk' => $this->faker->unique()->numerify('########'),
            'no_hp' => '099999999',
            'jenis_kelamin' => $this->faker->randomElement(['L', 'P']),
            'universitas' => $this->faker->company,
            'prodi' => $this->faker->jobTitle,
            'jabatan_fungsional' => $this->faker->jobTitle,
            'pendidikan_tertinggi' => $this->faker->randomElement(['S1', 'S2', 'S3']),
            'status_kerja' => $this->faker->randomElement(['Aktif', 'Non-aktif']),
            'status_dosen' => $this->faker->randomElement(['Aktif', 'Non-aktif']),
            'status_akun' => $this->faker->randomElement(['Aktif', 'Non-aktif']),
            'pembayaran' => $this->faker->randomElement(['Lunas', 'Belum Lunas']),
            'role' => 'admin',
        ]);
    }
    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
