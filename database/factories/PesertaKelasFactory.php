<?php

namespace Database\Factories;

use App\Models\KelasPerkuliahan;
use App\Models\Mahasiswa;
use App\Models\PesertaKelas;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PesertaKelas>
 */
class PesertaKelasFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'kelas_perkuliahan_id' => KelasPerkuliahan::factory(),
            'mahasiswa_id' => Mahasiswa::factory(),
            'nilai_tugas' => null,
            'nilai_uts' => null,
            'nilai_uas' => null,
            'nilai_akhir' => null,
            'nilai_huruf' => null,
        ];
    }
}
