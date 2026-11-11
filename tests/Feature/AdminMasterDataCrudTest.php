<?php

namespace Tests\Feature;

use App\Models\Dosen;
use App\Models\KelasPerkuliahan;
use App\Models\Mahasiswa;
use App\Models\MataKuliah;
use App\Models\ProgramStudi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminMasterDataCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_crud_program_studi_and_blocks_delete_when_in_use(): void
    {
        $admin = User::factory()->admin()->create();

        $storeResponse = $this->actingAs($admin)->post(route('admin.master-prodi.store'), [
            'kode_prodi' => 'SI01',
            'nama_prodi' => 'Sistem Informasi',
        ]);
        $storeResponse->assertRedirect(route('admin.master-prodi.index'));
        $prodi = ProgramStudi::where('kode_prodi', 'SI01')->firstOrFail();

        $this->actingAs($admin)->post(route('admin.master-prodi.store'), [
            'kode_prodi' => 'SI01',
            'nama_prodi' => 'Duplikat',
        ])->assertSessionHasErrors('kode_prodi');

        $this->actingAs($admin)->put(route('admin.master-prodi.update', $prodi), [
            'kode_prodi' => 'SI01',
            'nama_prodi' => 'Sistem Informasi Bisnis',
        ])->assertRedirect(route('admin.master-prodi.index'));
        $this->assertSame('Sistem Informasi Bisnis', $prodi->fresh()->nama_prodi);

        Mahasiswa::factory()->for($prodi)->create();

        $this->actingAs($admin)
            ->delete(route('admin.master-prodi.destroy', $prodi))
            ->assertRedirect();
        $this->assertModelExists($prodi);

        $prodiKosong = ProgramStudi::factory()->create();
        $this->actingAs($admin)
            ->delete(route('admin.master-prodi.destroy', $prodiKosong))
            ->assertRedirect(route('admin.master-prodi.index'));
        $this->assertModelMissing($prodiKosong);
    }

    public function test_admin_can_crud_mata_kuliah(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->post(route('admin.master-matkul.store'), [
            'kode_mk' => 'IF201',
            'nama_mk' => 'Struktur Data',
            'sks' => 3,
        ])->assertRedirect(route('admin.master-matkul.index'));

        $matkul = MataKuliah::where('kode_mk', 'IF201')->firstOrFail();
        $this->assertSame(3, $matkul->sks);

        $this->actingAs($admin)->put(route('admin.master-matkul.update', $matkul), [
            'kode_mk' => 'IF201',
            'nama_mk' => 'Algoritma dan Struktur Data',
            'sks' => 4,
        ])->assertRedirect(route('admin.master-matkul.index'));
        $this->assertSame('Algoritma dan Struktur Data', $matkul->fresh()->nama_mk);
        $this->assertSame(4, $matkul->fresh()->sks);

        $this->actingAs($admin)
            ->delete(route('admin.master-matkul.destroy', $matkul))
            ->assertRedirect(route('admin.master-matkul.index'));
        $this->assertModelMissing($matkul);
    }

    public function test_admin_can_crud_dosen_and_linked_user_account(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->post(route('admin.master-dosen.store'), [
            'username' => 'dosen_baru',
            'password' => 'secret123',
            'nip' => '199001012020011001',
            'nama_lengkap' => 'Dr. Siti Aminah',
            'jenis_kelamin' => 'P',
            'telepon' => '08123456789',
            'alamat' => 'Jl. Merdeka No. 10',
        ])->assertRedirect(route('admin.master-dosen.index'));

        $dosen = Dosen::where('nip', '199001012020011001')->firstOrFail();
        $this->assertSame('dosen_baru', $dosen->user->username);
        $this->assertSame('dosen', $dosen->user->role);

        $this->actingAs($admin)->put(route('admin.master-dosen.update', $dosen), [
            'username' => 'dosen_baru',
            'password' => '',
            'nip' => '199001012020011001',
            'nama_lengkap' => 'Prof. Siti Aminah',
        ])->assertRedirect(route('admin.master-dosen.index'));
        $this->assertSame('Prof. Siti Aminah', $dosen->fresh()->nama_lengkap);

        $kelas = KelasPerkuliahan::factory()->for($dosen)->create();
        $this->actingAs($admin)->delete(route('admin.master-dosen.destroy', $dosen));
        $this->assertModelExists($dosen);

        $kelas->delete();
        $user = $dosen->user;
        $this->actingAs($admin)
            ->delete(route('admin.master-dosen.destroy', $dosen))
            ->assertRedirect(route('admin.master-dosen.index'));
        $this->assertModelMissing($dosen);
        $this->assertModelMissing($user);
    }

    public function test_admin_can_crud_mahasiswa_and_linked_user_account(): void
    {
        $admin = User::factory()->admin()->create();
        $prodi = ProgramStudi::factory()->create();

        $this->actingAs($admin)->post(route('admin.master-mahasiswa.store'), [
            'username' => 'mhs_baru',
            'password' => 'secret123',
            'npm' => '2025010099',
            'nama_lengkap' => 'Rina Kartika',
            'program_studi_id' => $prodi->id,
            'angkatan' => '2025',
        ])->assertRedirect(route('admin.master-mahasiswa.index'));

        $mahasiswa = Mahasiswa::where('npm', '2025010099')->firstOrFail();
        $this->assertSame('mhs_baru', $mahasiswa->user->username);
        $this->assertSame('mahasiswa', $mahasiswa->user->role);

        $this->actingAs($admin)->put(route('admin.master-mahasiswa.update', $mahasiswa), [
            'username' => 'mhs_baru',
            'password' => '',
            'npm' => '2025010099',
            'nama_lengkap' => 'Rina Kartika Sari',
            'program_studi_id' => $prodi->id,
            'angkatan' => '2025',
        ])->assertRedirect(route('admin.master-mahasiswa.index'));
        $this->assertSame('Rina Kartika Sari', $mahasiswa->fresh()->nama_lengkap);

        $user = $mahasiswa->user;
        $this->actingAs($admin)
            ->delete(route('admin.master-mahasiswa.destroy', $mahasiswa))
            ->assertRedirect(route('admin.master-mahasiswa.index'));
        $this->assertModelMissing($mahasiswa);
        $this->assertModelMissing($user);
    }
}
