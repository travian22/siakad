<?php

namespace Database\Factories;

use App\Models\Mahasiswa;
use App\Models\ProgramStudi;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Mahasiswa>
 */
class MahasiswaFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory()->mahasiswa(),
            'program_studi_id' => ProgramStudi::factory(),
            'npm' => fake()->unique()->numerify('##########'),
            'nama_lengkap' => fake()->name(),
            'angkatan' => (string) fake()->year(),
            'jenis_kelamin' => null,
            'alamat' => null,
            'foto_profil' => null,
        ];
    }

    public function withProfile(): static
    {
        return $this->state(fn (array $attributes) => [
            'jenis_kelamin' => fake()->randomElement(['L', 'P']),
            'alamat' => fake()->address(),
        ]);
    }
}
