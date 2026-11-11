<?php

namespace Database\Factories;

use App\Models\MataKuliah;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MataKuliah>
 */
class MataKuliahFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'kode_mk' => fake()->unique()->bothify('MK###'),
            'nama_mk' => fake()->randomElement(['Pemrograman Web', 'Basis Data', 'Algoritma & Struktur Data', 'Jaringan Komputer', 'Kalkulus']),
            'sks' => fake()->randomElement([2, 3, 4]),
        ];
    }
}
