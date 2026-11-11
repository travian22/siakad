<?php

namespace Tests\Feature;

use App\Models\BiayaKuliah;
use App\Models\Dosen;
use App\Models\KelasPerkuliahan;
use App\Models\Mahasiswa;
use App\Models\MataKuliah;
use App\Models\PeriodeAkademik;
use App\Models\PesertaKelas;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminSchedulingAndKeuanganTest extends TestCase
{
    use RefreshDatabase;

    public function test_activating_periode_akademik_keeps_only_one_active_periode(): void
    {
        $admin = User::factory()->admin()->create();
        $periodeLama = PeriodeAkademik::factory()->active()->create(['nama_periode' => '2024/2025 Ganjil']);

        $this->actingAs($admin)->post(route('admin.periode-akademik.store'), [
            'nama_periode' => '2024/2025 Genap',
            'is_active' => '0',
        ])->assertRedirect(route('admin.periode-akademik.index'));

        $periodeBaru = PeriodeAkademik::where('nama_periode', '2024/2025 Genap')->firstOrFail();
        $this->assertFalse($periodeBaru->is_active);

        $this->actingAs($admin)
            ->post(route('admin.periode-akademik.activate', $periodeBaru))
            ->assertRedirect(route('admin.periode-akademik.index'));

        $this->assertFalse($periodeLama->fresh()->is_active);
        $this->assertTrue($periodeBaru->fresh()->is_active);
        $this->assertSame(1, PeriodeAkademik::where('is_active', true)->count());

        $this->actingAs($admin)->delete(route('admin.periode-akademik.destroy', $periodeBaru));
        $this->assertModelExists($periodeBaru);
    }

    public function test_admin_can_create_update_kelas_and_manage_peserta(): void
    {
        $admin = User::factory()->admin()->create();
        $periode = PeriodeAkademik::factory()->active()->create();
        $matkul = MataKuliah::factory()->create();
        $dosen = Dosen::factory()->create();
        $mhs1 = Mahasiswa::factory()->create();
        $mhs2 = Mahasiswa::factory()->create();

        $this->actingAs($admin)->post(route('admin.kelas.store'), [
            'periode_akademik_id' => $periode->id,
            'mata_kuliah_id' => $matkul->id,
            'dosen_id' => $dosen->id,
            'nama_kelas' => 'Kelas A',
            'hari' => 'Senin',
            'jam_mulai' => '10:00',
            'jam_selesai' => '09:00',
        ])->assertSessionHasErrors(['jam_selesai', 'ruangan']);

        $this->actingAs($admin)->post(route('admin.kelas.store'), [
            'periode_akademik_id' => $periode->id,
            'mata_kuliah_id' => $matkul->id,
            'dosen_id' => $dosen->id,
            'nama_kelas' => 'Kelas A',
            'hari' => 'Senin',
            'jam_mulai' => '08:00',
            'jam_selesai' => '10:30',
            'ruangan' => 'Lab 1',
        ])->assertRedirect(route('admin.kelas.index'));

        $kelas = KelasPerkuliahan::where('nama_kelas', 'Kelas A')->firstOrFail();

        $this->actingAs($admin)->post(route('admin.kelas.peserta.store', $kelas), [
            'mahasiswa_ids' => [$mhs1->id, $mhs2->id],
        ])->assertRedirect(route('admin.kelas.peserta', $kelas));

        $this->assertSame(2, $kelas->pesertaKelas()->count());

        $otherKelas = KelasPerkuliahan::factory()->create();
        $otherPeserta = PesertaKelas::factory()->for($otherKelas)->create();

        $this->actingAs($admin)
            ->delete(route('admin.kelas.peserta.destroy', [$kelas, $otherPeserta]))
            ->assertNotFound();

        $peserta1 = PesertaKelas::where('kelas_perkuliahan_id', $kelas->id)
            ->where('mahasiswa_id', $mhs1->id)
            ->firstOrFail();

        $this->actingAs($admin)
            ->delete(route('admin.kelas.peserta.destroy', [$kelas, $peserta1]))
            ->assertRedirect(route('admin.kelas.peserta', $kelas));

        $this->assertModelMissing($peserta1);
    }

    public function test_admin_can_manage_biaya_kuliah_and_toggle_status(): void
    {
        $admin = User::factory()->admin()->create();
        $periode = PeriodeAkademik::factory()->active()->create();
        $mahasiswa = Mahasiswa::factory()->create();

        $this->actingAs($admin)->post(route('admin.keuangan.store'), [
            'mahasiswa_id' => $mahasiswa->id,
            'periode_akademik_id' => $periode->id,
            'nominal_tagihan' => '4500000',
            'status_bayar' => 'belum_bayar',
        ])->assertRedirect(route('admin.keuangan.index'));

        $biaya = BiayaKuliah::where('mahasiswa_id', $mahasiswa->id)->firstOrFail();
        $this->assertSame('belum_bayar', $biaya->status_bayar);

        $this->actingAs($admin)->post(route('admin.keuangan.store'), [
            'mahasiswa_id' => $mahasiswa->id,
            'periode_akademik_id' => $periode->id,
            'nominal_tagihan' => '5000000',
            'status_bayar' => 'belum_bayar',
        ])->assertSessionHasErrors('periode_akademik_id');

        $this->actingAs($admin)
            ->patch(route('admin.keuangan.toggle-status', $biaya))
            ->assertRedirect();
        $this->assertSame('lunas', $biaya->fresh()->status_bayar);

        $this->actingAs($admin)
            ->delete(route('admin.keuangan.destroy', $biaya))
            ->assertRedirect(route('admin.keuangan.index'));
        $this->assertModelMissing($biaya);
    }

    public function test_non_admin_user_cannot_perform_admin_mutations(): void
    {
        $dosenUser = User::factory()->dosen()->create();
        Dosen::factory()->for($dosenUser)->withProfile()->create();

        $this->actingAs($dosenUser)
            ->post(route('admin.master-prodi.store'), [
                'kode_prodi' => '99',
                'nama_prodi' => 'Illegal',
            ])
            ->assertForbidden();
    }
}
