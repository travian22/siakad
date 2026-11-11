<?php

namespace Tests\Unit;

use App\Models\KelasPerkuliahan;
use App\Models\MataKuliah;
use App\Models\PeriodeAkademik;
use App\Models\PesertaKelas;
use App\Services\AcademicTranscriptService;
use Illuminate\Support\Collection;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;

class AcademicTranscriptServiceTest extends TestCase
{
    #[TestWith(['A', 4.0])]
    #[TestWith(['B', 3.0])]
    #[TestWith(['C', 2.0])]
    #[TestWith(['D', 1.0])]
    #[TestWith(['E', 0.0])]
    #[TestWith([null, 0.0])]
    public function test_returns_expected_grade_weight(?string $letter, float $expected): void
    {
        $service = new AcademicTranscriptService;

        $this->assertSame($expected, $service->gradeWeight($letter));
    }

    public function test_summarizes_ipk_sks_and_semester_ips_accurately(): void
    {
        $service = new AcademicTranscriptService;

        $periode1 = new PeriodeAkademik(['nama_periode' => '2023/2024 Genap']);
        $periode1->id = 1;

        $periode2 = new PeriodeAkademik(['nama_periode' => '2024/2025 Ganjil']);
        $periode2->id = 2;

        $items = new Collection([
            $this->makeEnrollment($periode1, 3, 'A'),
            $this->makeEnrollment($periode1, 3, 'B'),
            $this->makeEnrollment($periode2, 4, 'A'),
            $this->makeEnrollment($periode2, 2, 'E'),
            $this->makeEnrollment($periode2, 3, null),
        ]);

        $summary = $service->summarize($items);

        // Total points = (3*4 + 3*3) + (4*4 + 2*0) = 21 + 16 = 37; Graded SKS = 12 => 37/12 = 3.08
        $this->assertSame(3.08, $summary['ipk']);
        // Passed SKS (excluding E and null) = 3 + 3 + 4 = 10
        $this->assertSame(10, $summary['sks_kumulatif']);
        $this->assertSame(12, $summary['sks_dinilai']);

        $this->assertCount(2, $summary['semester_ips']);
        $this->assertSame(3.5, $summary['semester_ips'][0]['ips']);
        $this->assertSame(6, $summary['semester_ips'][0]['sks']);
        $this->assertSame(2.67, $summary['semester_ips'][1]['ips']);
        $this->assertSame(6, $summary['semester_ips'][1]['sks']);
    }

    private function makeEnrollment(PeriodeAkademik $periode, int $sks, ?string $letter): PesertaKelas
    {
        $matkul = new MataKuliah(['sks' => $sks]);
        $kelas = new KelasPerkuliahan;
        $kelas->periode_akademik_id = $periode->id;
        $kelas->setRelation('periodeAkademik', $periode);
        $kelas->setRelation('mataKuliah', $matkul);

        $peserta = new PesertaKelas(['nilai_huruf' => $letter]);
        $peserta->setRelation('kelasPerkuliahan', $kelas);

        return $peserta;
    }
}
