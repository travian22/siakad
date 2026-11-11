<?php

namespace Tests\Feature;

use App\Models\Dosen;
use App\Models\Mahasiswa;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\TestWith;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_renders_for_guest(): void
    {
        $response = $this->get('/login');

        $response->assertOk();
        $response->assertSee('Masuk ke Akun Anda');
    }

    public function test_authenticated_user_visiting_login_redirects_to_role_dashboard(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get('/login');

        $response->assertRedirect(route('admin.dashboard'));
    }

    public function test_login_validates_required_username_and_password(): void
    {
        $response = $this->from('/login')->post('/login', []);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors([
            'username' => 'Username wajib diisi.',
            'password' => 'Kata sandi wajib diisi.',
        ]);
        $this->assertGuest();
    }

    public function test_login_rejects_invalid_credentials(): void
    {
        User::factory()->admin()->create([
            'username' => 'admin',
            'password' => 'secret-pass',
        ]);

        $response = $this->from('/login')->post('/login', [
            'username' => 'admin',
            'password' => 'wrong-pass',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors([
            'username' => 'Username atau kata sandi tidak sesuai.',
        ]);
        $this->assertSame('admin', session()->getOldInput('username'));
        $this->assertGuest();
    }

    #[TestWith(['admin', 'admin.dashboard'])]
    #[TestWith(['dosen', 'dosen.dashboard'])]
    #[TestWith(['mahasiswa', 'mahasiswa.dashboard'])]
    public function test_login_redirects_each_role_to_its_dashboard(string $role, string $expectedRoute): void
    {
        $user = User::factory()->state(['role' => $role])->create([
            'username' => 'user_'.$role,
            'password' => 'password123',
        ]);

        if ($role === 'dosen') {
            Dosen::factory()->for($user)->withProfile()->create();
        } elseif ($role === 'mahasiswa') {
            Mahasiswa::factory()->for($user)->withProfile()->create();
        }

        $response = $this->post('/login', [
            'username' => 'user_'.$role,
            'password' => 'password123',
        ]);

        $response->assertRedirect(route($expectedRoute));
        $this->assertAuthenticatedAs($user);
    }

    public function test_login_throttles_after_five_attempts(): void
    {
        User::factory()->admin()->create(['username' => 'admin']);

        for ($i = 0; $i < 5; $i++) {
            $this->post('/login', [
                'username' => 'admin',
                'password' => 'wrong-password',
            ])->assertRedirect();
        }

        $response = $this->post('/login', [
            'username' => 'admin',
            'password' => 'wrong-password',
        ]);

        $response->assertTooManyRequests();
    }

    public function test_logout_clears_session_and_redirects_to_login(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post('/logout');

        $response->assertRedirect(route('login'));
        $this->assertGuest();
    }
}
