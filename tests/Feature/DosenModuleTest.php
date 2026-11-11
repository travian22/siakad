<?php

namespace Tests\Feature;

use App\Models\Dosen;
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

class DosenModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_incomplete_dosen_sees_full_page_profile_form_without_sidebar(): void
    {
        $dosenUser = User::factory()->dosen()->create();
        Dosen::factory()->for($dosenUser)->create();

        $response = $this->actingAs($dosenUser)->get(route('dosen.profil'));

        $response->assertOk();
        $response->assertSee('Lengkapi Data Diri');
        $response->assertDontSee('id="app-sidebar"', false);
    }

    public function test_dosen_can_complete_profile_with_biodata_and_photo(): void
    {
        Storage::fake('public');
        $dosenUser = User::factory()->dosen()->create();
        $dosen = Dosen::factory()->for($dosenUser)->create();

        $response = $this->actingAs($dosenUser)->post(route('dosen.profil.update'), [
            'nama_lengkap' => 'Dr. Ahmad Fauzi, M.T.',
            'jenis_kelamin' => 'L',
            'telepon' => '081234567890',
            'alamat' => 'Jl. Pendidikan No. 1',
            'foto_profil' => UploadedFile::fake()->image('dosen.jpg', 300, 300),
        ]);

        $response->assertRedirect(route('dosen.dashboard'));
        $dosen->refresh();

        $this->assertSame('Dr. Ahmad Fauzi, M.T.', $dosen->nama_lengkap);
        $this->assertSame('L', $dosen->jenis_kelamin);
        $this->assertNotNull($dosen->foto_profil);
        $this->assertTrue($dosen->isProfileComplete());
        Storage::disk('public')->assertExists($dosen->foto_profil);

        $revisit = $this->actingAs($dosenUser)->get(route('dosen.profil'));
        $revisit->assertOk();
        $revisit->assertSee('id="app-sidebar"', false);
    }

    public function test_dosen_dashboard_shows_only_own_classes_in_active_periode(): void
    {
        $activePeriode = PeriodeAkademik::factory()->active()->create(['nama_periode' => '2024/2025 Ganjil']);
        $inactivePeriode = PeriodeAkademik::factory()->create(['nama_periode' => '2023/2024 Genap']);

        $dosenUser = User::factory()->dosen()->create();
        $dosen = Dosen::factory()->for($dosenUser)->withProfile()->create();
        $otherDosen = Dosen::factory()->withProfile()->create();

        $mkActive = MataKuliah::factory()->create(['kode_mk' => 'IF201', 'nama_mk' => 'Pemrograman Web']);
        $mkInactive = MataKuliah::factory()->create(['kode_mk' => 'IF100', 'nama_mk' => 'Matkul Lama']);
        $mkOtherDosen = MataKuliah::factory()->create(['kode_mk' => 'IF999', 'nama_mk' => 'Matkul Dosen Lain']);

        KelasPerkuliahan::factory()->for($activePeriode)->for($mkActive)->for($dosen)->create();
        KelasPerkuliahan::factory()->for($inactivePeriode)->for($mkInactive)->for($dosen)->create();
        KelasPerkuliahan::factory()->for($activePeriode)->for($mkOtherDosen)->for($otherDosen)->create();

        $response = $this->actingAs($dosenUser)->get(route('dosen.dashboard'));

        $response->assertOk();
        $response->assertSee('Pemrograman Web');
        $response->assertDontSee('Matkul Lama');
        $response->assertDontSee('Matkul Dosen Lain');
    }

    public function test_dosen_cannot_view_or_grade_another_dosens_class(): void
    {
        $dosenUser = User::factory()->dosen()->create();
        Dosen::factory()->for($dosenUser)->withProfile()->create();
        $otherKelas = KelasPerkuliahan::factory()->create();
        $otherPeserta = PesertaKelas::factory()->for($otherKelas)->create();

        $this->actingAs($dosenUser)
            ->get(route('dosen.kelas.nilai', $otherKelas->id))
            ->assertNotFound();

        $this->actingAs($dosenUser)
            ->post(route('dosen.kelas.nilai.store', $otherKelas->id), [
                'nilai' => [
                    $otherPeserta->id => ['tugas' => 90, 'uts' => 90, 'uas' => 90],
                ],
            ])
            ->assertNotFound();
    }

    public function test_input_nilai_page_renders_required_tabs_and_vanilla_js_toggle(): void
    {
        $dosenUser = User::factory()->dosen()->create();
        $dosen = Dosen::factory()->for($dosenUser)->withProfile()->create();
        $kelas = KelasPerkuliahan::factory()->for($dosen)->create();
        PesertaKelas::factory()->for($kelas)->create();

        $response = $this->actingAs($dosenUser)->get(route('dosen.kelas.nilai', $kelas->id));

        $response->assertOk();
        $response->assertSee('id="tab-peserta"', false);
        $response->assertSee('id="tab-form-nilai"', false);
        $response->assertSee("classList.toggle('hidden')", false);
    }

    public function test_dosen_batch_saves_grades_and_computes_final_score_and_letter_in_transaction(): void
    {
        $dosenUser = User::factory()->dosen()->create();
        $dosen = Dosen::factory()->for($dosenUser)->withProfile()->create();
        $kelas = KelasPerkuliahan::factory()->for($dosen)->create();
        $otherKelas = KelasPerkuliahan::factory()->create();

        $mhs1 = Mahasiswa::factory()->create(['npm' => '2024010001', 'nama_lengkap' => 'Mahasiswa A']);
        $mhs2 = Mahasiswa::factory()->create(['npm' => '2024010002', 'nama_lengkap' => 'Mahasiswa B']);
        $mhs3 = Mahasiswa::factory()->create(['npm' => '2024010003', 'nama_lengkap' => 'Mahasiswa C']);

        $peserta1 = PesertaKelas::factory()->for($kelas)->for($mhs1)->create();
        $peserta2 = PesertaKelas::factory()->for($kelas)->for($mhs2)->create();
        $peserta3 = PesertaKelas::factory()->for($kelas)->for($mhs3)->create();
        $foreignPeserta = PesertaKelas::factory()->for($otherKelas)->create();

        $response = $this->actingAs($dosenUser)->post(route('dosen.kelas.nilai.store', $kelas->id), [
            'nilai' => [
                $peserta1->id => ['tugas' => '80', 'uts' => '90', 'uas' => '86'],
                $peserta2->id => ['tugas' => '60', 'uts' => '70', 'uas' => '50'],
                $peserta3->id => ['tugas' => '0', 'uts' => '0', 'uas' => '0'],
                $foreignPeserta->id => ['tugas' => '100', 'uts' => '100', 'uas' => '100'],
            ],
        ]);

        $response->assertRedirect(route('dosen.kelas.nilai', $kelas->id));
        $response->assertSessionHas('status');

        $peserta1->refresh();
        $this->assertSame('86.00', $peserta1->nilai_akhir);
        $this->assertSame('A', $peserta1->nilai_huruf);

        $peserta2->refresh();
        $this->assertSame('58.00', $peserta2->nilai_akhir);
        $this->assertSame('D', $peserta2->nilai_huruf);

        $peserta3->refresh();
        $this->assertSame('0.00', $peserta3->nilai_akhir);
        $this->assertSame('E', $peserta3->nilai_huruf);

        $foreignPeserta->refresh();
        $this->assertNull($foreignPeserta->nilai_akhir);
        $this->assertNull($foreignPeserta->nilai_huruf);
    }

    public function test_grading_validation_rejects_partial_scores(): void
    {
        $dosenUser = User::factory()->dosen()->create();
        $dosen = Dosen::factory()->for($dosenUser)->withProfile()->create();
        $kelas = KelasPerkuliahan::factory()->for($dosen)->create();
        $peserta = PesertaKelas::factory()->for($kelas)->create();

        $response = $this->actingAs($dosenUser)
            ->from(route('dosen.kelas.nilai', $kelas->id))
            ->post(route('dosen.kelas.nilai.store', $kelas->id), [
                'nilai' => [
                    $peserta->id => ['tugas' => '85', 'uts' => '', 'uas' => '90'],
                ],
            ]);

        $response->assertRedirect(route('dosen.kelas.nilai', $kelas->id));
        $response->assertSessionHasErrors("nilai.{$peserta->id}");
        $this->assertNull($peserta->fresh()->nilai_akhir);
    }
}
