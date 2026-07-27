<?php

namespace App\Services;

use App\Models\Assessment;
use App\Models\CompanyValue;
use App\Models\ResponseRating;
use App\Models\ValueRating;
use Illuminate\Support\Facades\DB;

class ScoringService
{
    /**
     * Close an assessment: aggregate survey responses, compute scores and leakage.
     */
    public function closeAssessment(Assessment $assessment): Assessment
    {
        $company = $assessment->company;
        $values  = $company->values;

        DB::transaction(function () use ($assessment, $values) {
            ValueRating::where('assessment_id', $assessment->id)->delete();

            $totalFinancialWeight = (float) $values->sum('financial_weight');

            $weightedScoreSum = 0;
            $totalWeightUsed  = 0;
            $totalLeakage     = 0;

            foreach ($values as $value) {
                $avgScore = $this->averageScoreForValue($assessment->id, $value->id);

                if ($avgScore === null) {
                    continue;
                }

                // Survey scale is 1–10, so normalise: score 1 = 100% leakage, score 10 = 0% leakage
                $leakageRate     = max(0, (10 - $avgScore) / 9);
                $financialImpact = (float) $value->financial_weight * $leakageRate;

                // Scoring weight: proportional to financial_weight; fall back to equal weight
                $scoreWeight = $totalFinancialWeight > 0
                    ? (float) $value->financial_weight / $totalFinancialWeight
                    : 1 / max($values->count(), 1);

                ValueRating::create([
                    'assessment_id'        => $assessment->id,
                    'company_value_id'     => $value->id,
                    'avg_score'            => round($avgScore, 2),
                    'financial_impact'     => round($financialImpact, 2),
                    'training_recommended' => $avgScore <= 5,
                ]);

                $weightedScoreSum += $avgScore * $scoreWeight;
                $totalWeightUsed  += $scoreWeight;
                $totalLeakage     += $financialImpact;
            }

            // Normalise weighted avg from 1–10 scale to 0–100%: (avg-1)/9*100
            $overallScore = $totalWeightUsed > 0
                ? round((($weightedScoreSum / $totalWeightUsed) - 1) / 9 * 100, 2)
                : null;

            $assessment->update([
                'status'        => 'closed',
                'overall_score' => $overallScore,
                'total_leakage' => round($totalLeakage, 2),
                'closed_at'     => now(),
            ]);
        });

        return $assessment->fresh(['valueRatings.companyValue']);
    }

    /**
     * Returns the average score (1-10) for a given value across all survey responses.
     */
    private function averageScoreForValue(int $assessmentId, int $valueId): ?float
    {
        $avg = ResponseRating::whereHas('surveyResponse', fn ($q) =>
                    $q->where('assessment_id', $assessmentId)
                )
                ->where('company_value_id', $valueId)
                ->avg('score');

        return $avg !== null ? (float) $avg : null;
    }

    public static function gradeCode(float $score): string
    {
        return match (true) {
            $score > 89 => 'cc',
            $score > 65 => 'gw',
            $score > 50 => 'hdu',
            default     => 'icu',
        };
    }

    public static function gradeShort(float $score): string
    {
        return match (true) {
            $score > 89 => 'CC',
            $score > 65 => 'GW',
            $score > 50 => 'HDU',
            default     => 'ICU',
        };
    }

    public static function gradeLabel(float $score): string
    {
        return match (true) {
            $score > 89 => 'Celebrity Credibility',
            $score > 65 => 'General Ward',
            $score > 50 => 'High Dependency Unit',
            default     => 'Intensive Care Unit',
        };
    }

    public static function gradeColor(float $score): string
    {
        return match (true) {
            $score > 89 => '#059669',
            $score > 65 => '#ca8a04',
            $score > 50 => '#ea580c',
            default     => '#dc2626',
        };
    }
}
