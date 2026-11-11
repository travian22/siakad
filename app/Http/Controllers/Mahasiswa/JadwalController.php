<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\PeriodeAkademik;
use App\Models\PesertaKelas;
use Illuminate\Http\Request;
use Illuminate\View\View;

class JadwalController extends Controller
{
    public function index(Request $request): View
    {
        $mahasiswa = $request->user()->mahasiswa;
        abort_if($mahasiswa === null, 404, 'Data mahasiswa tidak ditemukan.');

        $activePeriode = PeriodeAkademik::currentActive();
        $jadwalList = collect();
        $totalSks = 0;

        if ($activePeriode) {
            $jadwalList = PesertaKelas::query()
                ->with([
                    'kelasPerkuliahan.mataKuliah',
                    'kelasPerkuliahan.dosen',
                    'kelasPerkuliahan.periodeAkademik',
                ])
                ->where('mahasiswa_id', $mahasiswa->id)
                ->whereHas(
                    'kelasPerkuliahan',
                    fn ($query) => $query->where('periode_akademik_id', $activePeriode->id)
                )
                ->get()
                ->sortBy(fn (PesertaKelas $peserta): array => [
                    $peserta->kelasPerkuliahan->daySortOrder(),
                    $peserta->kelasPerkuliahan->jam_mulai,
                ])
                ->values();

            $totalSks = $jadwalList->sum(
                fn (PesertaKelas $peserta): int => $peserta->kelasPerkuliahan->mataKuliah->sks
            );
        }

        return view('mahasiswa.jadwal', [
            'mahasiswa' => $mahasiswa,
            'activePeriode' => $activePeriode,
            'jadwalList' => $jadwalList,
            'totalSks' => $totalSks,
        ]);
    }
}
