<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\BiayaKuliah;
use App\Models\PeriodeAkademik;
use App\Models\PesertaKelas;
use App\Services\AcademicTranscriptService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request, AcademicTranscriptService $transcriptService): View
    {
        $mahasiswa = $request->user()->mahasiswa;
        abort_if($mahasiswa === null, 404, 'Data mahasiswa tidak ditemukan.');
        $mahasiswa->load('programStudi');

        $activePeriode = PeriodeAkademik::currentActive();

        $allEnrollments = PesertaKelas::query()
            ->with([
                'kelasPerkuliahan.mataKuliah',
                'kelasPerkuliahan.periodeAkademik',
                'kelasPerkuliahan.dosen',
            ])
            ->where('mahasiswa_id', $mahasiswa->id)
            ->get();

        $summary = $transcriptService->summarize($allEnrollments);

        $activeSchedule = collect();
        $activeSks = 0;
        $activeBill = null;

        if ($activePeriode) {
            $activeSchedule = $allEnrollments
                ->filter(fn (PesertaKelas $peserta): bool => $peserta->kelasPerkuliahan->periode_akademik_id === $activePeriode->id)
                ->values();

            $activeSks = $activeSchedule->sum(
                fn (PesertaKelas $peserta): int => $peserta->kelasPerkuliahan->mataKuliah->sks
            );

            $activeBill = BiayaKuliah::query()
                ->where('mahasiswa_id', $mahasiswa->id)
                ->where('periode_akademik_id', $activePeriode->id)
                ->first();
        }

        return view('mahasiswa.dashboard', [
            'mahasiswa' => $mahasiswa,
            'activePeriode' => $activePeriode,
            'summary' => $summary,
            'activeSks' => $activeSks,
            'activeScheduleCount' => $activeSchedule->count(),
            'activeBill' => $activeBill,
        ]);
    }
}
