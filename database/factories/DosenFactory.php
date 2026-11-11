<?php

namespace Database\Factories;

use App\Models\Dosen;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Dosen>
 */
class DosenFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory()->dosen(),
            'nip' => fake()->unique()->numerify('##################'),
            'nama_lengkap' => fake()->name(),
            'jenis_kelamin' => null,
            'telepon' => null,
            'alamat' => null,
            'foto_profil' => null,
        ];
    }

    public function withProfile(): static
    {
        return $this->state(fn (array $attributes) => [
            'jenis_kelamin' => fake()->randomElement(['L', 'P']),
            'telepon' => fake()->phoneNumber(),
            'alamat' => fake()->address(),
            'foto_profil' => 'profiles/dosen-default.jpg',
        ]);
    }
}
