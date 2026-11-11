<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dosen\StoreNilaiRequest;
use App\Models\KelasPerkuliahan;
use App\Models\PesertaKelas;
use App\Services\GradingCalculator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class NilaiController extends Controller
{
    public function index(Request $request, int $id): View
    {
        $dosen = $request->user()->dosen;
        abort_if($dosen === null, 404, 'Data dosen tidak ditemukan.');

        $kelas = KelasPerkuliahan::query()
            ->with(['mataKuliah', 'periodeAkademik', 'dosen'])
            ->where('dosen_id', $dosen->id)
            ->findOrFail($id);

        $pesertas = PesertaKelas::query()
            ->with(['mahasiswa.programStudi'])
            ->where('kelas_perkuliahan_id', $kelas->id)
            ->get()
            ->sortBy(fn (PesertaKelas $peserta): string => $peserta->mahasiswa->npm)
            ->values();

        return view('dosen.nilai', [
            'kelas' => $kelas,
            'pesertas' => $pesertas,
        ]);
    }

    public function store(StoreNilaiRequest $request, int $id, GradingCalculator $calculator): RedirectResponse
    {
        $dosen = $request->user()->dosen;
        abort_if($dosen === null, 404, 'Data dosen tidak ditemukan.');

        $kelas = KelasPerkuliahan::query()
            ->where('dosen_id', $dosen->id)
            ->findOrFail($id);

        $validated = $request->validated();

        DB::transaction(function () use ($kelas, $validated, $calculator): void {
            $pesertasById = PesertaKelas::query()
                ->where('kelas_perkuliahan_id', $kelas->id)
                ->get()
                ->keyBy('id');

            foreach ($validated['nilai'] as $pesertaId => $scores) {
                $peserta = $pesertasById->get((int) $pesertaId);

                if (! $peserta) {
                    continue;
                }

                if (
                    ! filled($scores['tugas'] ?? null)
                    || ! filled($scores['uts'] ?? null)
                    || ! filled($scores['uas'] ?? null)
                ) {
                    continue;
                }

                $tugas = (float) $scores['tugas'];
                $uts = (float) $scores['uts'];
                $uas = (float) $scores['uas'];
                $evaluation = $calculator->evaluate($tugas, $uts, $uas);

                $peserta->update([
                    'nilai_tugas' => $tugas,
                    'nilai_uts' => $uts,
                    'nilai_uas' => $uas,
                    'nilai_akhir' => $evaluation['nilai_akhir'],
                    'nilai_huruf' => $evaluation['nilai_huruf'],
                ]);
            }
        });

        return redirect()
            ->route('dosen.kelas.nilai', $kelas->id)
            ->with('status', 'Nilai mahasiswa berhasil disimpan dan dihitung otomatis.');
    }
}
