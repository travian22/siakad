<?php

namespace Database\Seeders;

use App\Models\Dosen;
use App\Models\Mahasiswa;
use App\Models\PeriodeAkademik;
use App\Models\ProgramStudi;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        User::create([
            'username' => 'admin',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        PeriodeAkademik::create([
            'nama_periode' => '2024/2025 Ganjil',
            'is_active' => true,
        ]);

        $prodi = ProgramStudi::create([
            'kode_prodi' => '01',
            'nama_prodi' => 'Teknik Informatika',
        ]);

        $dosenUser = User::create([
            'username' => 'dosen1',
            'password' => Hash::make('password'),
            'role' => 'dosen',
        ]);

        Dosen::create([
            'user_id' => $dosenUser->id,
            'nip' => '198501012010011001',
            'nama_lengkap' => 'Dr. Ahmad Fauzi',
        ]);

        $mhsUser = User::create([
            'username' => 'mhs1',
            'password' => Hash::make('password'),
            'role' => 'mahasiswa',
        ]);

        Mahasiswa::create([
            'user_id' => $mhsUser->id,
            'program_studi_id' => $prodi->id,
            'npm' => '2024010001',
            'nama_lengkap' => 'Budi Santoso',
            'angkatan' => '2024',
        ]);
    }
}
