<?php

namespace Database\Factories;

use App\Models\BiayaKuliah;
use App\Models\Mahasiswa;
use App\Models\PeriodeAkademik;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<BiayaKuliah>
 */
class BiayaKuliahFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'mahasiswa_id' => Mahasiswa::factory(),
            'periode_akademik_id' => PeriodeAkademik::factory(),
            'nominal_tagihan' => fake()->randomFloat(2, 1000000, 10000000),
            'status_bayar' => 'belum_bayar',
        ];
    }

    public function lunas(): static
    {
        return $this->state(fn (array $attributes) => [
            'status_bayar' => 'lunas',
        ]);
    }
}
