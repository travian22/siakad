<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BiayaKuliah;
use App\Models\Dosen;
use App\Models\KelasPerkuliahan;
use App\Models\Mahasiswa;
use App\Models\MataKuliah;
use App\Models\PeriodeAkademik;
use App\Models\ProgramStudi;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $activePeriode = PeriodeAkademik::currentActive();

        $totalMahasiswa = Mahasiswa::query()->count();
        $totalDosen = Dosen::query()->count();
        $totalProdi = ProgramStudi::query()->count();
        $totalMatkul = MataKuliah::query()->count();

        $kelasQuery = KelasPerkuliahan::query();
        if ($activePeriode) {
            $kelasQuery->where('periode_akademik_id', $activePeriode->id);
        }
        $totalKelasAktif = $kelasQuery->count();

        $prodiStats = ProgramStudi::query()
            ->withCount('mahasiswa')
            ->orderByDesc('mahasiswa_count')
            ->orderBy('nama_prodi')
            ->get();

        $maxMahasiswaPerProdi = max(1, (int) ($prodiStats->max('mahasiswa_count') ?? 1));

        $recentKelas = KelasPerkuliahan::query()
            ->with(['mataKuliah', 'dosen', 'periodeAkademik'])
            ->withCount('pesertaKelas')
            ->when($activePeriode, fn ($query) => $query->where('periode_akademik_id', $activePeriode->id))
            ->latest('id')
            ->limit(5)
            ->get();

        $tagihanSummary = [
            'belum_bayar' => BiayaKuliah::query()
                ->when($activePeriode, fn ($query) => $query->where('periode_akademik_id', $activePeriode->id))
                ->where('status_bayar', 'belum_bayar')
                ->count(),
            'lunas' => BiayaKuliah::query()
                ->when($activePeriode, fn ($query) => $query->where('periode_akademik_id', $activePeriode->id))
                ->where('status_bayar', 'lunas')
                ->count(),
        ];

        return view('admin.dashboard', [
            'activePeriode' => $activePeriode,
            'totalMahasiswa' => $totalMahasiswa,
            'totalDosen' => $totalDosen,
            'totalProdi' => $totalProdi,
            'totalMatkul' => $totalMatkul,
            'totalKelasAktif' => $totalKelasAktif,
            'prodiStats' => $prodiStats,
            'maxMahasiswaPerProdi' => $maxMahasiswaPerProdi,
            'recentKelas' => $recentKelas,
            'tagihanSummary' => $tagihanSummary,
        ]);
    }
}
