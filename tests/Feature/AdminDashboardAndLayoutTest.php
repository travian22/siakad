<?php

namespace Tests\Feature;

use App\Models\Dosen;
use App\Models\KelasPerkuliahan;
use App\Models\Mahasiswa;
use App\Models\PeriodeAkademik;
use App\Models\ProgramStudi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDashboardAndLayoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_dashboard_renders_erp_layout_and_real_statistics(): void
    {
        $admin = User::factory()->admin()->create();
        $periode = PeriodeAkademik::factory()->active()->create(['nama_periode' => '2025/2026 Ganjil']);
        $prodi = ProgramStudi::factory()->create(['kode_prodi' => '01', 'nama_prodi' => 'Teknik Informatika']);
        $dosen = Dosen::factory()->create();
        Mahasiswa::factory()->count(3)->for($prodi)->create();
        KelasPerkuliahan::factory()->count(2)->for($periode)->for($dosen)->create();

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertOk();
        $response->assertSee('Dashboard Akademik');
        $response->assertSee('2025/2026 Ganjil');
        $response->assertSee('id="app-sidebar"', false);
        $response->assertSee('id="sidebar-toggle"', false);
        $response->assertSee('id="sidebar-close"', false);
        $response->assertSee('id="sidebar-backdrop"', false);
        $response->assertSee('3 mhs');
        $response->assertSee('Teknik Informatika');
    }

    public function test_prodi_index_escapes_html_in_program_studi_name(): void
    {
        $admin = User::factory()->admin()->create();
        ProgramStudi::factory()->create([
            'kode_prodi' => '99',
            'nama_prodi' => "Sistem <script>alert('xss')</script>",
        ]);

        $response = $this->actingAs($admin)->get(route('admin.master-prodi.index'));

        $response->assertOk();
        $response->assertSee('&lt;script&gt;', false);
        $response->assertDontSee("<script>alert('xss')</script>", false);
    }
}
