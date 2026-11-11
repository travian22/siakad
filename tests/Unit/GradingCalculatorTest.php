<?php

namespace Tests\Unit;

use App\Services\GradingCalculator;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;

class GradingCalculatorTest extends TestCase
{
    public function test_calculates_weighted_final_score_with_20_30_50_formula(): void
    {
        $calculator = new GradingCalculator;

        $result = $calculator->evaluate(80.0, 90.0, 86.0);

        $this->assertSame(86.0, $result['nilai_akhir']);
        $this->assertSame('A', $result['nilai_huruf']);
    }

    #[TestWith([100.0, 'A'])]
    #[TestWith([85.0, 'A'])]
    #[TestWith([84.99, 'B'])]
    #[TestWith([75.0, 'B'])]
    #[TestWith([74.99, 'C'])]
    #[TestWith([60.0, 'C'])]
    #[TestWith([59.99, 'D'])]
    #[TestWith([50.0, 'D'])]
    #[TestWith([49.99, 'E'])]
    #[TestWith([0.0, 'E'])]
    public function test_converts_final_score_to_letter_grade_boundaries(float $score, string $expectedLetter): void
    {
        $calculator = new GradingCalculator;

        $this->assertSame($expectedLetter, $calculator->convertToLetterGrade($score));
    }
}
