<?php

namespace App\Services;

class GradingCalculator
{
    public function calculateFinalScore(float $tugas, float $uts, float $uas): float
    {
        return round(($tugas * 0.20) + ($uts * 0.30) + ($uas * 0.50), 2);
    }

    public function convertToLetterGrade(float $nilaiAkhir): string
    {
        if ($nilaiAkhir >= 85) {
            return 'A';
        }

        if ($nilaiAkhir >= 75) {
            return 'B';
        }

        if ($nilaiAkhir >= 60) {
            return 'C';
        }

        if ($nilaiAkhir >= 50) {
            return 'D';
        }

        return 'E';
    }

    /**
     * @return array{nilai_akhir: float, nilai_huruf: string}
     */
    public function evaluate(float $tugas, float $uts, float $uas): array
    {
        $nilaiAkhir = $this->calculateFinalScore($tugas, $uts, $uas);

        return [
            'nilai_akhir' => $nilaiAkhir,
            'nilai_huruf' => $this->convertToLetterGrade($nilaiAkhir),
        ];
    }
}
