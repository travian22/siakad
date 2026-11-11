<?php

namespace Tests\Feature;

use App\Models\BiayaKuliah;
use App\Models\Dosen;
use App\Models\KelasPerkuliahan;
use App\Models\Mahasiswa;
use App\Models\MataKuliah;
use App\Models\PeriodeAkademik;
use App\Models\PesertaKelas;
use App\Models\ProgramStudi;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DatabaseSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_database_seeder_populates_initial_accounts_and_active_periode(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->assertDatabaseHas('users', [
            'username' => 'admin',
            'role' => 'admin',
        ]);
        $this->assertDatabaseHas('users', [
            'username' => 'dosen1',
            'role' => 'dosen',
        ]);
        $this->assertDatabaseHas('users', [
            'username' => 'mhs1',
            'role' => 'mahasiswa',
        ]);

        $periode = PeriodeAkademik::where('nama_periode', '2024/2025 Ganjil')->first();
        $this->assertNotNull($periode);
        $this->assertTrue($periode->is_active);

        $dosen = Dosen::where('nip', '198501012010011001')->first();
        $this->assertNotNull($dosen);
        $this->assertSame('dosen1', $dosen->user->username);

        $mahasiswa = Mahasiswa::where('npm', '2024010001')->first();
        $this->assertNotNull($mahasiswa);
        $this->assertSame('mhs1', $mahasiswa->user->username);
        $this->assertSame('Teknik Informatika', $mahasiswa->programStudi->nama_prodi);
    }

    public function test_eloquent_models_resolve_custom_tables_and_relationships(): void
    {
        $periode = PeriodeAkademik::factory()->active()->create();
        $prodi = ProgramStudi::factory()->create();
        $dosenUser = User::factory()->dosen()->create();
        $dosen = Dosen::factory()->for($dosenUser)->create();
        $mhsUser = User::factory()->mahasiswa()->create();
        $mahasiswa = Mahasiswa::factory()->for($mhsUser)->for($prodi)->create();
        $matkul = MataKuliah::factory()->create(['sks' => 3]);

        $kelas = KelasPerkuliahan::factory()
            ->for($periode)
            ->for($matkul)
            ->for($dosen)
            ->create();

        $peserta = PesertaKelas::factory()
            ->for($kelas)
            ->for($mahasiswa)
            ->create([
                'nilai_tugas' => 80,
                'nilai_uts' => 85,
                'nilai_uas' => 90,
                'nilai_akhir' => 86.50,
                'nilai_huruf' => 'A',
            ]);

        $biaya = BiayaKuliah::factory()
            ->for($mahasiswa)
            ->for($periode)
            ->lunas()
            ->create(['nominal_tagihan' => 5000000]);

        $this->assertTrue($periode->kelasPerkuliahan->contains($kelas));
        $this->assertTrue($periode->biayaKuliah->contains($biaya));
        $this->assertTrue($prodi->mahasiswa->contains($mahasiswa));
        $this->assertTrue($dosen->kelasPerkuliahan->contains($kelas));
        $this->assertTrue($matkul->kelasPerkuliahan->contains($kelas));
        $this->assertTrue($kelas->pesertaKelas->contains($peserta));
        $this->assertTrue($mahasiswa->pesertaKelas->contains($peserta));
        $this->assertTrue($mahasiswa->biayaKuliah->contains($biaya));

        $this->assertSame(3, $matkul->sks);
        $this->assertSame('86.50', $peserta->nilai_akhir);
        $this->assertSame('5000000.00', $biaya->nominal_tagihan);
        $this->assertSame('lunas', $biaya->status_bayar);
        $this->assertSame($dosen->id, $dosenUser->dosen->id);
        $this->assertSame($mahasiswa->id, $mhsUser->mahasiswa->id);
    }
}
