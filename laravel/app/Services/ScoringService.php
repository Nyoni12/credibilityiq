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
        $revenue = $company->annual_revenue ?: 0;

        DB::transaction(function () use ($assessment, $values, $revenue) {
            // Remove any previously computed value ratings
            ValueRating::where('assessment_id', $assessment->id)->delete();

            $weightedScoreSum = 0;
            $totalWeight      = 0;
            $totalLeakage     = 0;

            foreach ($values as $value) {
                $avgScore = $this->averageScoreForValue($assessment->id, $value->id);

                if ($avgScore === null) {
                    continue; // no responses for this value
                }

                $weight        = $value->weight_percentage / 100;
                $leakageRate   = max(0, (10 - $avgScore) / 10); // deficit fraction
                $financialImpact = $revenue * $weight * $leakageRate;

                ValueRating::create([
                    'assessment_id'        => $assessment->id,
                    'company_value_id'     => $value->id,
                    'avg_score'            => round($avgScore, 2),
                    'financial_impact'     => round($financialImpact, 2),
                    'training_recommended' => $avgScore < 5,
                ]);

                $weightedScoreSum += $avgScore * $weight;
                $totalWeight      += $weight;
                $totalLeakage     += $financialImpact;
            }

            $overallScore = $totalWeight > 0
                ? round(($weightedScoreSum / $totalWeight) * 10, 2) // scale to 100
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

    /**
     * Grade label for a 0-100 score.
     */
    public static function gradeLabel(float $score): string
    {
        return match (true) {
            $score >= 85 => 'Excellent',
            $score >= 70 => 'Good',
            $score >= 55 => 'Fair',
            $score >= 40 => 'Needs Improvement',
            default      => 'Critical',
        };
    }

    /**
     * Hex colour for a 0-100 score.
     */
    public static function gradeColor(float $score): string
    {
        return match (true) {
            $score >= 85 => '#00A651',
            $score >= 70 => '#1F2192',
            $score >= 55 => '#f59e0b',
            $score >= 40 => '#f97316',
            default      => '#ef4444',
        };
    }
}
