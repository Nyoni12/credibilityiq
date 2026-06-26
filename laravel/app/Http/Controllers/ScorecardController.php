<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use App\Services\ScoringService;

class ScorecardController extends Controller
{
    public function show(Assessment $assessment)
    {
        $user = auth()->user();

        if (!$user->isSuperAdmin() && $assessment->company_id !== $user->company_id) {
            abort(403);
        }

        if ($assessment->status !== 'closed') {
            return redirect()->route('assessments.index')
                ->with('error', 'The scorecard is only available after the assessment is closed.');
        }

        $assessment->load(['valueRatings.companyValue', 'company']);

        $scoreLabel = ScoringService::gradeLabel($assessment->overall_score ?? 0);
        $scoreColor = ScoringService::gradeColor($assessment->overall_score ?? 0);

        $valueRatings = $assessment->valueRatings->sortByDesc('avg_score');

        $chartLabels = $valueRatings->pluck('companyValue.name');
        $chartScores = $valueRatings->map(fn ($r) => round($r->avg_score * 10, 1));
        $chartColors = $valueRatings->map(fn ($r) => ScoringService::gradeColor($r->avg_score * 10));

        return view('scorecard.show', compact(
            'assessment', 'scoreLabel', 'scoreColor',
            'valueRatings', 'chartLabels', 'chartScores', 'chartColors'
        ));
    }
}
