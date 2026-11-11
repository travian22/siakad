<?php

namespace Tests\Feature;

use App\Models\BiayaKuliah;
use App\Models\KelasPerkuliahan;
use App\Models\Mahasiswa;
use App\Models\MataKuliah;
use App\Models\PeriodeAkademik;
use App\Models\PesertaKelas;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MahasiswaModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_incomplete_mahasiswa_sees_full_page_profile_form_without_sidebar(): void
    {
        $mhsUser = User::factory()->mahasiswa()->create();
        Mahasiswa::factory()->for($mhsUser)->create();

        $response = $this->actingAs($mhsUser)->get(route('mahasiswa.profil'));

        $response->assertOk();
        $response->assertSee('Lengkapi Data Diri');
        $response->assertDontSee('id="app-sidebar"', false);
    }

    public function test_mahasiswa_can_complete_profile_and_unlock_dashboard(): void
    {
        Storage::fake('public');
        $mhsUser = User::factory()->mahasiswa()->create();
        $mahasiswa = Mahasiswa::factory()->for($mhsUser)->create();

        $response = $this->actingAs($mhsUser)->post(route('mahasiswa.profil.update'), [
            'nama_lengkap' => 'Budi Santoso',
            'jenis_kelamin' => 'L',
            'alamat' => 'Jl. Cihampelas No. 5, Bandung',
            'foto_profil' => UploadedFile::fake()->image('mhs.png', 200, 200),
        ]);

        $response->assertRedirect(route('mahasiswa.dashboard'));
        $mahasiswa->refresh();

        $this->assertTrue($mahasiswa->isProfileComplete());
        $this->assertNotNull($mahasiswa->foto_profil);
        Storage::disk('public')->assertExists($mahasiswa->foto_profil);

        $revisit = $this->actingAs($mhsUser)->get(route('mahasiswa.profil'));
        $revisit->assertOk();
        $revisit->assertSee('id="app-sidebar"', false);
    }

    public function test_mahasiswa_dashboard_displays_ipk_sks_and_ips_chart(): void
    {
        $periodeLalu = PeriodeAkademik::factory()->create(['nama_periode' => '2023/2024 Genap']);
        $periodeAktif = PeriodeAkademik::factory()->active()->create(['nama_periode' => '2024/2025 Ganjil']);

        $mhsUser = User::factory()->mahasiswa()->create();
        $mahasiswa = Mahasiswa::factory()->for($mhsUser)->withProfile()->create();

        $mk1 = MataKuliah::factory()->create(['sks' => 3]);
        $mk2 = MataKuliah::factory()->create(['sks' => 4]);

        $kelas1 = KelasPerkuliahan::factory()->for($periodeLalu)->for($mk1)->create();
        $kelas2 = KelasPerkuliahan::factory()->for($periodeAktif)->for($mk2)->create();

        PesertaKelas::factory()->for($kelas1)->for($mahasiswa)->create([
            'nilai_akhir' => 88.00,
            'nilai_huruf' => 'A',
        ]);
        PesertaKelas::factory()->for($kelas2)->for($mahasiswa)->create([
            'nilai_akhir' => 78.00,
            'nilai_huruf' => 'B',
        ]);

        BiayaKuliah::factory()->for($mahasiswa)->for($periodeAktif)->lunas()->create([
            'nominal_tagihan' => 4500000,
        ]);

        $response = $this->actingAs($mhsUser)->get(route('mahasiswa.dashboard'));

        $response->assertOk();
        // (3*4 + 4*3) / 7 = 24 / 7 = 3.43
        $response->assertSee('3.43');
        $response->assertSee('7 SKS');
        $response->assertSee('IPS 4.00');
        $response->assertSee('IPS 3.00');
        $response->assertSee('Lunas');
    }

    public function test_jadwal_kuliah_shows_only_own_classes_in_active_periode(): void
    {
        $periodeLalu = PeriodeAkademik::factory()->create(['nama_periode' => '2023/2024 Genap']);
        $periodeAktif = PeriodeAkademik::factory()->active()->create(['nama_periode' => '2024/2025 Ganjil']);

        $mhsUser = User::factory()->mahasiswa()->create();
        $mahasiswa = Mahasiswa::factory()->for($mhsUser)->withProfile()->create();
        $otherMahasiswa = Mahasiswa::factory()->withProfile()->create();

        $mkAktif = MataKuliah::factory()->create(['kode_mk' => 'IF201', 'nama_mk' => 'Pemrograman Web']);
        $mkLalu = MataKuliah::factory()->create(['kode_mk' => 'IF101', 'nama_mk' => 'Algoritma Lama']);
        $mkLain = MataKuliah::factory()->create(['kode_mk' => 'IF999', 'nama_mk' => 'Kelas Mahasiswa Lain']);

        $kelasAktif = KelasPerkuliahan::factory()->for($periodeAktif)->for($mkAktif)->create();
        $kelasLalu = KelasPerkuliahan::factory()->for($periodeLalu)->for($mkLalu)->create();
        $kelasLain = KelasPerkuliahan::factory()->for($periodeAktif)->for($mkLain)->create();

        PesertaKelas::factory()->for($kelasAktif)->for($mahasiswa)->create();
        PesertaKelas::factory()->for($kelasLalu)->for($mahasiswa)->create();
        PesertaKelas::factory()->for($kelasLain)->for($otherMahasiswa)->create();

        $response = $this->actingAs($mhsUser)->get(route('mahasiswa.jadwal'));

        $response->assertOk();
        $response->assertSee('Pemrograman Web');
        $response->assertDontSee('Algoritma Lama');
        $response->assertDontSee('Kelas Mahasiswa Lain');
    }

    public function test_khs_displays_grades_and_filters_by_periode(): void
    {
        $periodeLalu = PeriodeAkademik::factory()->create(['nama_periode' => '2023/2024 Genap']);
        $periodeAktif = PeriodeAkademik::factory()->active()->create(['nama_periode' => '2024/2025 Ganjil']);

        $mhsUser = User::factory()->mahasiswa()->create();
        $mahasiswa = Mahasiswa::factory()->for($mhsUser)->withProfile()->create();

        $mk1 = MataKuliah::factory()->create(['kode_mk' => 'IF101', 'nama_mk' => 'Algoritma Dasar', 'sks' => 3]);
        $mk2 = MataKuliah::factory()->create(['kode_mk' => 'IF201', 'nama_mk' => 'Basis Data Lanjut', 'sks' => 3]);

        $kelas1 = KelasPerkuliahan::factory()->for($periodeLalu)->for($mk1)->create();
        $kelas2 = KelasPerkuliahan::factory()->for($periodeAktif)->for($mk2)->create();

        PesertaKelas::factory()->for($kelas1)->for($mahasiswa)->create([
            'nilai_tugas' => 85,
            'nilai_uts' => 85,
            'nilai_uas' => 85,
            'nilai_akhir' => 85.00,
            'nilai_huruf' => 'A',
        ]);
        PesertaKelas::factory()->for($kelas2)->for($mahasiswa)->create();

        $allResponse = $this->actingAs($mhsUser)->get(route('mahasiswa.khs'));
        $allResponse->assertOk();
        $allResponse->assertSee('Algoritma Dasar');
        $allResponse->assertSee('Basis Data Lanjut');
        $allResponse->assertSee('85.00');
        $allResponse->assertSee('Belum dinilai');

        $filteredResponse = $this->actingAs($mhsUser)->get(route('mahasiswa.khs', ['periode_akademik_id' => $periodeAktif->id]));
        $filteredResponse->assertOk();
        $filteredResponse->assertSee('Basis Data Lanjut');
        $filteredResponse->assertDontSee('Algoritma Dasar');
    }

    public function test_biaya_kuliah_shows_only_own_billing_records(): void
    {
        $periode = PeriodeAkademik::factory()->active()->create(['nama_periode' => '2024/2025 Ganjil']);
        $mhsUser = User::factory()->mahasiswa()->create();
        $mahasiswa = Mahasiswa::factory()->for($mhsUser)->withProfile()->create();
        $otherMahasiswa = Mahasiswa::factory()->withProfile()->create();

        BiayaKuliah::factory()->for($mahasiswa)->for($periode)->create([
            'nominal_tagihan' => 4500000,
            'status_bayar' => 'belum_bayar',
        ]);

        BiayaKuliah::factory()->for($otherMahasiswa)->for($periode)->lunas()->create([
            'nominal_tagihan' => 9999000,
        ]);

        $response = $this->actingAs($mhsUser)->get(route('mahasiswa.biaya'));

        $response->assertOk();
        $response->assertSee('4.500.000');
        $response->assertSee('Belum Bayar');
        $response->assertDontSee('9.999.000');
    }
}
