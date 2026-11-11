<?php

namespace App\Services;

use App\Models\PeriodeAkademik;
use App\Models\PesertaKelas;
use Illuminate\Support\Collection;

class AcademicTranscriptService
{
    public function gradeWeight(?string $nilaiHuruf): float
    {
        return match ($nilaiHuruf) {
            'A' => 4.0,
            'B' => 3.0,
            'C' => 2.0,
            'D' => 1.0,
            default => 0.0,
        };
    }

    /**
     * @param  Collection<int, PesertaKelas>  $pesertaKelasList
     * @return array{
     *     ipk: float,
     *     sks_kumulatif: int,
     *     sks_dinilai: int,
     *     semester_ips: array<int, array{periode: PeriodeAkademik, sks: int, ips: float, matkul_count: int}>
     * }
     */
    public function summarize(Collection $pesertaKelasList): array
    {
        $graded = $pesertaKelasList->filter(
            fn (PesertaKelas $peserta): bool => $peserta->nilai_huruf !== null
        );

        $totalSksDinilai = 0;
        $sksKumulatif = 0;
        $totalQualityPoints = 0.0;

        foreach ($graded as $peserta) {
            $sks = $peserta->kelasPerkuliahan->mataKuliah->sks;
            $weight = $this->gradeWeight($peserta->nilai_huruf);

            $totalSksDinilai += $sks;
            $totalQualityPoints += $weight * $sks;

            if ($peserta->nilai_huruf !== 'E') {
                $sksKumulatif += $sks;
            }
        }

        $ipk = $totalSksDinilai > 0 ? round($totalQualityPoints / $totalSksDinilai, 2) : 0.0;

        $semesterIps = $graded
            ->groupBy(fn (PesertaKelas $peserta): int => $peserta->kelasPerkuliahan->periode_akademik_id)
            ->sortKeys()
            ->map(function (Collection $items): array {
                /** @var PesertaKelas $first */
                $first = $items->first();
                $metrics = $this->calculateSemesterMetrics($items);

                return [
                    'periode' => $first->kelasPerkuliahan->periodeAkademik,
                    'sks' => $metrics['sks_dinilai'],
                    'ips' => $metrics['ips'],
                    'matkul_count' => $items->count(),
                ];
            })
            ->values()
            ->all();

        return [
            'ipk' => $ipk,
            'sks_kumulatif' => $sksKumulatif,
            'sks_dinilai' => $totalSksDinilai,
            'semester_ips' => $semesterIps,
        ];
    }

    /**
     * @param  Collection<int, PesertaKelas>  $semesterPesertas
     * @return array{sks_diambil: int, sks_dinilai: int, ips: float}
     */
    public function calculateSemesterMetrics(Collection $semesterPesertas): array
    {
        $sksDiambil = 0;
        $sksDinilai = 0;
        $qualityPoints = 0.0;

        foreach ($semesterPesertas as $peserta) {
            $sks = $peserta->kelasPerkuliahan->mataKuliah->sks;
            $sksDiambil += $sks;

            if ($peserta->nilai_huruf !== null) {
                $sksDinilai += $sks;
                $qualityPoints += $this->gradeWeight($peserta->nilai_huruf) * $sks;
            }
        }

        return [
            'sks_diambil' => $sksDiambil,
            'sks_dinilai' => $sksDinilai,
            'ips' => $sksDinilai > 0 ? round($qualityPoints / $sksDinilai, 2) : 0.0,
        ];
    }
}
