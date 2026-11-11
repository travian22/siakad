<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\DosenController as AdminDosenController;
use App\Http\Controllers\Admin\KelasPerkuliahanController as AdminKelasPerkuliahanController;
use App\Http\Controllers\Admin\KeuanganController as AdminKeuanganController;
use App\Http\Controllers\Admin\MahasiswaController as AdminMahasiswaController;
use App\Http\Controllers\Admin\MataKuliahController as AdminMataKuliahController;
use App\Http\Controllers\Admin\PeriodeAkademikController as AdminPeriodeAkademikController;
use App\Http\Controllers\Admin\ProgramStudiController as AdminProgramStudiController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Dosen\DashboardController as DosenDashboardController;
use App\Http\Controllers\Dosen\NilaiController as DosenNilaiController;
use App\Http\Controllers\Dosen\ProfilController as DosenProfilController;
use App\Http\Controllers\Mahasiswa\BiayaController as MahasiswaBiayaController;
use App\Http\Controllers\Mahasiswa\DashboardController as MahasiswaDashboardController;
use App\Http\Controllers\Mahasiswa\JadwalController as MahasiswaJadwalController;
use App\Http\Controllers\Mahasiswa\KhsController as MahasiswaKhsController;
use App\Http\Controllers\Mahasiswa\ProfilController as MahasiswaProfilController;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function (Request $request): RedirectResponse {
    if ($request->user()) {
        return redirect()->route($request->user()->dashboardRouteName());
    }

    return redirect()->route('login');
})->name('home');

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])
        ->middleware('throttle:login')
        ->name('login.store');
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function (): void {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        Route::resource('master-prodi', AdminProgramStudiController::class)
            ->parameters(['master-prodi' => 'prodi'])
            ->except(['show']);

        Route::resource('master-matkul', AdminMataKuliahController::class)
            ->parameters(['master-matkul' => 'matkul'])
            ->except(['show']);

        Route::resource('master-dosen', AdminDosenController::class)
            ->parameters(['master-dosen' => 'dosen'])
            ->except(['show']);

        Route::resource('master-mahasiswa', AdminMahasiswaController::class)
            ->parameters(['master-mahasiswa' => 'mahasiswa'])
            ->except(['show']);

        Route::resource('periode-akademik', AdminPeriodeAkademikController::class)
            ->parameters(['periode-akademik' => 'periode'])
            ->except(['show']);
        Route::post('/periode-akademik/{periode}/activate', [AdminPeriodeAkademikController::class, 'activate'])
            ->name('periode-akademik.activate');

        Route::resource('kelas', AdminKelasPerkuliahanController::class)
            ->parameters(['kelas' => 'kelas'])
            ->except(['show']);
        Route::get('/kelas/{kelas}/peserta', [AdminKelasPerkuliahanController::class, 'peserta'])
            ->name('kelas.peserta');
        Route::post('/kelas/{kelas}/peserta', [AdminKelasPerkuliahanController::class, 'storePeserta'])
            ->name('kelas.peserta.store');
        Route::delete('/kelas/{kelas}/peserta/{peserta}', [AdminKelasPerkuliahanController::class, 'destroyPeserta'])
            ->name('kelas.peserta.destroy');

        Route::resource('keuangan', AdminKeuanganController::class)
            ->parameters(['keuangan' => 'biaya'])
            ->except(['show']);
        Route::patch('/keuangan/{biaya}/toggle-status', [AdminKeuanganController::class, 'toggleStatus'])
            ->name('keuangan.toggle-status');
    });

Route::middleware(['auth', 'role:dosen'])
    ->prefix('dosen')
    ->name('dosen.')
    ->group(function (): void {
        Route::get('/profil', [DosenProfilController::class, 'edit'])->name('profil');
        Route::post('/profil', [DosenProfilController::class, 'update'])->name('profil.update');

        Route::middleware('profile.complete')->group(function (): void {
            Route::get('/dashboard', [DosenDashboardController::class, 'index'])->name('dashboard');
            Route::get('/kelas/{id}/nilai', [DosenNilaiController::class, 'index'])->name('kelas.nilai');
            Route::post('/kelas/{id}/nilai', [DosenNilaiController::class, 'store'])->name('kelas.nilai.store');
        });
    });

Route::middleware(['auth', 'role:mahasiswa'])
    ->prefix('mahasiswa')
    ->name('mahasiswa.')
    ->group(function (): void {
        Route::get('/profil', [MahasiswaProfilController::class, 'edit'])->name('profil');
        Route::post('/profil', [MahasiswaProfilController::class, 'update'])->name('profil.update');

        Route::middleware('profile.complete')->group(function (): void {
            Route::get('/dashboard', [MahasiswaDashboardController::class, 'index'])->name('dashboard');
            Route::get('/jadwal', [MahasiswaJadwalController::class, 'index'])->name('jadwal');
            Route::get('/khs', [MahasiswaKhsController::class, 'index'])->name('khs');
            Route::get('/biaya', [MahasiswaBiayaController::class, 'index'])->name('biaya');
        });
    });
