<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\KelasPerkuliahan;
use App\Models\PeriodeAkademik;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $dosen = $request->user()->dosen;
        abort_if($dosen === null, 404, 'Data dosen tidak ditemukan.');

        $activePeriode = PeriodeAkademik::currentActive();
        $jadwalMengajar = collect();

        if ($activePeriode) {
            $jadwalMengajar = KelasPerkuliahan::query()
                ->with(['mataKuliah', 'periodeAkademik'])
                ->withCount([
                    'pesertaKelas',
                    'pesertaKelas as dinilai_count' => fn ($query) => $query->whereNotNull('nilai_akhir'),
                ])
                ->where('dosen_id', $dosen->id)
                ->where('periode_akademik_id', $activePeriode->id)
                ->orderBy('jam_mulai')
                ->get()
                ->sortBy(fn (KelasPerkuliahan $kelas): array => [
                    $kelas->daySortOrder(),
                    $kelas->jam_mulai,
                ])
                ->values();
        }

        return view('dosen.dashboard', [
            'dosen' => $dosen,
            'activePeriode' => $activePeriode,
            'jadwalMengajar' => $jadwalMengajar,
        ]);
    }
}
