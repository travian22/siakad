<?php

namespace Database\Factories;

use App\Models\Dosen;
use App\Models\KelasPerkuliahan;
use App\Models\MataKuliah;
use App\Models\PeriodeAkademik;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<KelasPerkuliahan>
 */
class KelasPerkuliahanFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'periode_akademik_id' => PeriodeAkademik::factory(),
            'mata_kuliah_id' => MataKuliah::factory(),
            'dosen_id' => Dosen::factory(),
            'nama_kelas' => fake()->randomElement(['Kelas A', 'Kelas B', 'Kelas C']),
            'hari' => fake()->randomElement(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat']),
            'jam_mulai' => '08:00',
            'jam_selesai' => '10:00',
            'ruangan' => fake()->bothify('R-###'),
        ];
    }
}
