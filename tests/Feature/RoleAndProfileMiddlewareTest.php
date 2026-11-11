<?php

namespace Tests\Feature;

use App\Models\Dosen;
use App\Models\KelasPerkuliahan;
use App\Models\Mahasiswa;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class RoleAndProfileMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    #[DataProvider('protectedRoleEndpoints')]
    public function test_unauthenticated_requests_redirect_to_login(string $uri): void
    {
        $response = $this->get($uri);

        $response->assertRedirect(route('login'));
    }

    /**
     * @return array<string, array{0: string}>
     */
    public static function protectedRoleEndpoints(): array
    {
        return [
            'admin dashboard' => ['/admin/dashboard'],
            'admin master prodi' => ['/admin/master-prodi'],
            'admin master matkul' => ['/admin/master-matkul'],
            'admin master dosen' => ['/admin/master-dosen'],
            'admin master mahasiswa' => ['/admin/master-mahasiswa'],
            'admin periode akademik' => ['/admin/periode-akademik'],
            'admin kelas' => ['/admin/kelas'],
            'admin keuangan' => ['/admin/keuangan'],
            'dosen profil' => ['/dosen/profil'],
            'dosen dashboard' => ['/dosen/dashboard'],
            'dosen nilai' => ['/dosen/kelas/1/nilai'],
            'mahasiswa profil' => ['/mahasiswa/profil'],
            'mahasiswa dashboard' => ['/mahasiswa/dashboard'],
            'mahasiswa jadwal' => ['/mahasiswa/jadwal'],
            'mahasiswa khs' => ['/mahasiswa/khs'],
            'mahasiswa biaya' => ['/mahasiswa/biaya'],
        ];
    }

    #[DataProvider('forbiddenRoleAccessCases')]
    public function test_role_middleware_forbids_access_to_other_roles_routes(string $userRole, string $targetUri): void
    {
        $user = User::factory()->state(['role' => $userRole])->create();

        if ($userRole === 'dosen') {
            Dosen::factory()->for($user)->withProfile()->create();
        } elseif ($userRole === 'mahasiswa') {
            Mahasiswa::factory()->for($user)->withProfile()->create();
        }

        $response = $this->actingAs($user)->get($targetUri);

        $response->assertForbidden();
    }

    /**
     * @return array<string, array{0: string, 1: string}>
     */
    public static function forbiddenRoleAccessCases(): array
    {
        return [
            'admin to dosen dashboard' => ['admin', '/dosen/dashboard'],
            'admin to mahasiswa dashboard' => ['admin', '/mahasiswa/dashboard'],
            'dosen to admin dashboard' => ['dosen', '/admin/dashboard'],
            'dosen to mahasiswa dashboard' => ['dosen', '/mahasiswa/dashboard'],
            'mahasiswa to admin dashboard' => ['mahasiswa', '/admin/dashboard'],
            'mahasiswa to dosen dashboard' => ['mahasiswa', '/dosen/dashboard'],
        ];
    }

    public function test_incomplete_dosen_profile_redirects_to_dosen_profil_without_loop(): void
    {
        $dosenUser = User::factory()->dosen()->create();
        Dosen::factory()->for($dosenUser)->create();

        $dashboardResponse = $this->actingAs($dosenUser)->get('/dosen/dashboard');
        $dashboardResponse->assertRedirect(route('dosen.profil'));
        $dashboardResponse->assertSessionHas('warning');

        $nilaiResponse = $this->actingAs($dosenUser)->get('/dosen/kelas/1/nilai');
        $nilaiResponse->assertRedirect(route('dosen.profil'));

        $profilResponse = $this->actingAs($dosenUser)->get('/dosen/profil');
        $profilResponse->assertOk();
        $profilResponse->assertSee('Lengkapi Data Diri');
    }

    public function test_complete_dosen_profile_can_access_protected_dosen_routes(): void
    {
        $dosenUser = User::factory()->dosen()->create();
        $dosen = Dosen::factory()->for($dosenUser)->withProfile()->create();
        $kelas = KelasPerkuliahan::factory()->for($dosen)->create();

        $this->actingAs($dosenUser)->get('/dosen/dashboard')->assertOk();
        $this->actingAs($dosenUser)->get(route('dosen.kelas.nilai', $kelas->id))->assertOk();
    }

    public function test_incomplete_mahasiswa_profile_redirects_to_mahasiswa_profil_without_loop(): void
    {
        $mhsUser = User::factory()->mahasiswa()->create();
        Mahasiswa::factory()->for($mhsUser)->create();

        foreach (['/mahasiswa/dashboard', '/mahasiswa/jadwal', '/mahasiswa/khs', '/mahasiswa/biaya'] as $uri) {
            $response = $this->actingAs($mhsUser)->get($uri);
            $response->assertRedirect(route('mahasiswa.profil'));
            $response->assertSessionHas('warning');
        }

        $profilResponse = $this->actingAs($mhsUser)->get('/mahasiswa/profil');
        $profilResponse->assertOk();
        $profilResponse->assertSee('Lengkapi Data Diri');
    }

    public function test_complete_mahasiswa_profile_can_access_protected_mahasiswa_routes(): void
    {
        $mhsUser = User::factory()->mahasiswa()->create();
        Mahasiswa::factory()->for($mhsUser)->withProfile()->create();

        $this->actingAs($mhsUser)->get('/mahasiswa/dashboard')->assertOk();
        $this->actingAs($mhsUser)->get('/mahasiswa/jadwal')->assertOk();
        $this->actingAs($mhsUser)->get('/mahasiswa/khs')->assertOk();
        $this->actingAs($mhsUser)->get('/mahasiswa/biaya')->assertOk();
    }
}
