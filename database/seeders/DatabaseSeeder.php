<?php

namespace Database\Seeders;

use App\Models\BiayaKuliah;
use App\Models\Dosen;
use App\Models\KelasPerkuliahan;
use App\Models\Mahasiswa;
use App\Models\MataKuliah;
use App\Models\PeriodeAkademik;
use App\Models\PesertaKelas;
use App\Models\ProgramStudi;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $password = Hash::make('password');

        User::create([
            'username' => 'admin',
            'password' => $password,
            'role' => 'admin',
        ]);

        $periodeLalu = PeriodeAkademik::create([
            'nama_periode' => '2023/2024 Genap',
            'is_active' => false,
        ]);

        $periodeAktif = PeriodeAkademik::create([
            'nama_periode' => '2024/2025 Ganjil',
            'is_active' => true,
        ]);

        $prodiTI = ProgramStudi::create([
            'kode_prodi' => '01',
            'nama_prodi' => 'Teknik Informatika',
        ]);

        $prodiSI = ProgramStudi::create([
            'kode_prodi' => '02',
            'nama_prodi' => 'Sistem Informasi',
        ]);

        $prodiMI = ProgramStudi::create([
            'kode_prodi' => '03',
            'nama_prodi' => 'Manajemen Informatika',
        ]);

        $mkAlgo = MataKuliah::create([
            'kode_mk' => 'IF101',
            'nama_mk' => 'Algoritma & Pemrograman',
            'sks' => 3,
        ]);

        $mkBasisData = MataKuliah::create([
            'kode_mk' => 'IF102',
            'nama_mk' => 'Basis Data',
            'sks' => 3,
        ]);

        $mkWeb = MataKuliah::create([
            'kode_mk' => 'IF201',
            'nama_mk' => 'Pemrograman Web',
            'sks' => 4,
        ]);

        MataKuliah::create([
            'kode_mk' => 'IF202',
            'nama_mk' => 'Jaringan Komputer',
            'sks' => 3,
        ]);

        $mkBisnis = MataKuliah::create([
            'kode_mk' => 'SI101',
            'nama_mk' => 'Analisis Proses Bisnis',
            'sks' => 2,
        ]);

        $dosenUser1 = User::create([
            'username' => 'dosen1',
            'password' => $password,
            'role' => 'dosen',
        ]);

        $dosen1 = Dosen::create([
            'user_id' => $dosenUser1->id,
            'nip' => '198501012010011001',
            'nama_lengkap' => 'Dr. Ahmad Fauzi',
        ]);

        $dosen2PhotoPath = $this->ensureDemoAvatar('profiles/dosen2.png');

        $dosenUser2 = User::create([
            'username' => 'dosen2',
            'password' => $password,
            'role' => 'dosen',
        ]);

        $dosen2 = Dosen::create([
            'user_id' => $dosenUser2->id,
            'nip' => '198805122015042002',
            'nama_lengkap' => 'Dr. Ratna Dewi, M.Kom.',
            'jenis_kelamin' => 'P',
            'telepon' => '081298765432',
            'alamat' => 'Jl. Ganesha No. 25, Bandung',
            'foto_profil' => $dosen2PhotoPath,
        ]);

        $mhsUser1 = User::create([
            'username' => 'mhs1',
            'password' => $password,
            'role' => 'mahasiswa',
        ]);

        $mhs1 = Mahasiswa::create([
            'user_id' => $mhsUser1->id,
            'program_studi_id' => $prodiTI->id,
            'npm' => '2024010001',
            'nama_lengkap' => 'Budi Santoso',
            'angkatan' => '2024',
        ]);

        $mhsUser2 = User::create([
            'username' => 'mhs2',
            'password' => $password,
            'role' => 'mahasiswa',
        ]);

        $mhs2 = Mahasiswa::create([
            'user_id' => $mhsUser2->id,
            'program_studi_id' => $prodiTI->id,
            'npm' => '2024010002',
            'nama_lengkap' => 'Siti Rahmawati',
            'angkatan' => '2024',
            'jenis_kelamin' => 'P',
            'alamat' => 'Jl. Diponegoro No. 14, Bandung',
        ]);

        $mhsUser3 = User::create([
            'username' => 'mhs3',
            'password' => $password,
            'role' => 'mahasiswa',
        ]);

        $mhs3 = Mahasiswa::create([
            'user_id' => $mhsUser3->id,
            'program_studi_id' => $prodiSI->id,
            'npm' => '2024010003',
            'nama_lengkap' => 'Andi Wijaya',
            'angkatan' => '2024',
            'jenis_kelamin' => 'L',
            'alamat' => 'Jl. Sudirman No. 88, Jakarta',
        ]);

        $mhsUser4 = User::create([
            'username' => 'mhs4',
            'password' => $password,
            'role' => 'mahasiswa',
        ]);

        $mhs4 = Mahasiswa::create([
            'user_id' => $mhsUser4->id,
            'program_studi_id' => $prodiSI->id,
            'npm' => '2024020004',
            'nama_lengkap' => 'Dewi Lestari',
            'angkatan' => '2024',
            'jenis_kelamin' => 'P',
            'alamat' => 'Jl. Malioboro No. 41, Yogyakarta',
        ]);

        $mhsUser5 = User::create([
            'username' => 'mhs5',
            'password' => $password,
            'role' => 'mahasiswa',
        ]);

        $mhs5 = Mahasiswa::create([
            'user_id' => $mhsUser5->id,
            'program_studi_id' => $prodiMI->id,
            'npm' => '2024030005',
            'nama_lengkap' => 'Fajar Pratama',
            'angkatan' => '2024',
            'jenis_kelamin' => 'L',
            'alamat' => 'Jl. Pemuda No. 19, Surabaya',
        ]);

        $kelasLalu = KelasPerkuliahan::create([
            'periode_akademik_id' => $periodeLalu->id,
            'mata_kuliah_id' => $mkAlgo->id,
            'dosen_id' => $dosen2->id,
            'nama_kelas' => 'Kelas A',
            'hari' => 'Senin',
            'jam_mulai' => '08:00',
            'jam_selesai' => '10:30',
            'ruangan' => 'R-201',
        ]);

        PesertaKelas::create([
            'kelas_perkuliahan_id' => $kelasLalu->id,
            'mahasiswa_id' => $mhs2->id,
            'nilai_tugas' => 88.00,
            'nilai_uts' => 84.00,
            'nilai_uas' => 90.00,
            'nilai_akhir' => 87.80,
            'nilai_huruf' => 'A',
        ]);

        PesertaKelas::create([
            'kelas_perkuliahan_id' => $kelasLalu->id,
            'mahasiswa_id' => $mhs3->id,
            'nilai_tugas' => 80.00,
            'nilai_uts' => 76.00,
            'nilai_uas' => 78.00,
            'nilai_akhir' => 77.80,
            'nilai_huruf' => 'B',
        ]);

        $kelasWebA = KelasPerkuliahan::create([
            'periode_akademik_id' => $periodeAktif->id,
            'mata_kuliah_id' => $mkWeb->id,
            'dosen_id' => $dosen1->id,
            'nama_kelas' => 'Kelas A',
            'hari' => 'Senin',
            'jam_mulai' => '08:00',
            'jam_selesai' => '11:20',
            'ruangan' => 'Lab Komputer 1',
        ]);

        $kelasBisnisA = KelasPerkuliahan::create([
            'periode_akademik_id' => $periodeAktif->id,
            'mata_kuliah_id' => $mkBisnis->id,
            'dosen_id' => $dosen1->id,
            'nama_kelas' => 'Kelas A',
            'hari' => 'Rabu',
            'jam_mulai' => '13:00',
            'jam_selesai' => '14:40',
            'ruangan' => 'R-302',
        ]);

        $kelasBasisDataA = KelasPerkuliahan::create([
            'periode_akademik_id' => $periodeAktif->id,
            'mata_kuliah_id' => $mkBasisData->id,
            'dosen_id' => $dosen2->id,
            'nama_kelas' => 'Kelas A',
            'hari' => 'Selasa',
            'jam_mulai' => '09:30',
            'jam_selesai' => '12:00',
            'ruangan' => 'Lab Basis Data',
        ]);

        $kelasAlgoB = KelasPerkuliahan::create([
            'periode_akademik_id' => $periodeAktif->id,
            'mata_kuliah_id' => $mkAlgo->id,
            'dosen_id' => $dosen2->id,
            'nama_kelas' => 'Kelas B',
            'hari' => 'Kamis',
            'jam_mulai' => '10:00',
            'jam_selesai' => '12:30',
            'ruangan' => 'R-104',
        ]);

        foreach ([$mhs1, $mhs2, $mhs3] as $mhs) {
            PesertaKelas::create([
                'kelas_perkuliahan_id' => $kelasWebA->id,
                'mahasiswa_id' => $mhs->id,
            ]);
        }

        PesertaKelas::create([
            'kelas_perkuliahan_id' => $kelasBasisDataA->id,
            'mahasiswa_id' => $mhs1->id,
        ]);

        PesertaKelas::create([
            'kelas_perkuliahan_id' => $kelasBasisDataA->id,
            'mahasiswa_id' => $mhs2->id,
            'nilai_tugas' => 86.00,
            'nilai_uts' => 82.00,
            'nilai_uas' => 88.00,
            'nilai_akhir' => 85.80,
            'nilai_huruf' => 'A',
        ]);

        PesertaKelas::create([
            'kelas_perkuliahan_id' => $kelasBasisDataA->id,
            'mahasiswa_id' => $mhs4->id,
        ]);

        foreach ([$mhs3, $mhs5] as $mhs) {
            PesertaKelas::create([
                'kelas_perkuliahan_id' => $kelasAlgoB->id,
                'mahasiswa_id' => $mhs->id,
            ]);
        }

        foreach ([$mhs3, $mhs4, $mhs5] as $mhs) {
            PesertaKelas::create([
                'kelas_perkuliahan_id' => $kelasBisnisA->id,
                'mahasiswa_id' => $mhs->id,
            ]);
        }

        BiayaKuliah::create([
            'mahasiswa_id' => $mhs1->id,
            'periode_akademik_id' => $periodeAktif->id,
            'nominal_tagihan' => 4500000,
            'status_bayar' => 'belum_bayar',
        ]);

        BiayaKuliah::create([
            'mahasiswa_id' => $mhs2->id,
            'periode_akademik_id' => $periodeAktif->id,
            'nominal_tagihan' => 4500000,
            'status_bayar' => 'lunas',
        ]);

        BiayaKuliah::create([
            'mahasiswa_id' => $mhs3->id,
            'periode_akademik_id' => $periodeAktif->id,
            'nominal_tagihan' => 4250000,
            'status_bayar' => 'lunas',
        ]);

        BiayaKuliah::create([
            'mahasiswa_id' => $mhs4->id,
            'periode_akademik_id' => $periodeAktif->id,
            'nominal_tagihan' => 4250000,
            'status_bayar' => 'belum_bayar',
        ]);

        BiayaKuliah::create([
            'mahasiswa_id' => $mhs5->id,
            'periode_akademik_id' => $periodeAktif->id,
            'nominal_tagihan' => 3900000,
            'status_bayar' => 'lunas',
        ]);
    }

    private function ensureDemoAvatar(string $path): string
    {
        if (Storage::disk('public')->exists($path)) {
            return $path;
        }

        if (function_exists('imagecreatetruecolor')) {
            $image = imagecreatetruecolor(120, 120);
            $navy = imagecolorallocate($image, 30, 58, 95);
            $gold = imagecolorallocate($image, 245, 158, 11);
            imagefill($image, 0, 0, $navy);
            imagefilledrectangle($image, 0, 108, 120, 120, $gold);

            ob_start();
            imagepng($image);
            $pngBytes = (string) ob_get_clean();
            imagedestroy($image);

            Storage::disk('public')->put($path, $pngBytes);
        }

        return $path;
    }
}
