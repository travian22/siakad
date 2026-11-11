<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\PeriodeAkademik;
use App\Models\PesertaKelas;
use App\Services\AcademicTranscriptService;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class KhsController extends Controller
{
    public function index(Request $request, AcademicTranscriptService $transcriptService): View
    {
        $mahasiswa = $request->user()->mahasiswa;
        abort_if($mahasiswa === null, 404, 'Data mahasiswa tidak ditemukan.');
        $mahasiswa->load('programStudi');

        $selectedPeriodeId = $request->integer('periode_akademik_id');

        $allEnrollments = PesertaKelas::query()
            ->with([
                'kelasPerkuliahan.mataKuliah',
                'kelasPerkuliahan.periodeAkademik',
                'kelasPerkuliahan.dosen',
            ])
            ->where('mahasiswa_id', $mahasiswa->id)
            ->get();

        $summary = $transcriptService->summarize($allEnrollments);

        $availablePeriodes = $allEnrollments
            ->map(fn (PesertaKelas $peserta): PeriodeAkademik => $peserta->kelasPerkuliahan->periodeAkademik)
            ->unique('id')
            ->sortByDesc('id')
            ->values();

        $filteredEnrollments = $selectedPeriodeId > 0
            ? $allEnrollments->filter(
                fn (PesertaKelas $peserta): bool => $peserta->kelasPerkuliahan->periode_akademik_id === $selectedPeriodeId
            )
            : $allEnrollments;

        $semesterGroups = $filteredEnrollments
            ->groupBy(fn (PesertaKelas $peserta): int => $peserta->kelasPerkuliahan->periode_akademik_id)
            ->sortKeysDesc()
            ->map(function (Collection $items) use ($transcriptService): array {
                /** @var PesertaKelas $first */
                $first = $items->first();
                $sortedItems = $items->sortBy(
                    fn (PesertaKelas $peserta): string => $peserta->kelasPerkuliahan->mataKuliah->kode_mk
                )->values();

                return [
                    'periode' => $first->kelasPerkuliahan->periodeAkademik,
                    'metrics' => $transcriptService->calculateSemesterMetrics($sortedItems),
                    'items' => $sortedItems,
                ];
            })
            ->values();

        return view('mahasiswa.khs', [
            'mahasiswa' => $mahasiswa,
            'summary' => $summary,
            'availablePeriodes' => $availablePeriodes,
            'selectedPeriodeId' => $selectedPeriodeId,
            'semesterGroups' => $semesterGroups,
        ]);
    }
}
