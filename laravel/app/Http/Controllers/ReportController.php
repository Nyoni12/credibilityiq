<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use App\Services\ScoringService;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    public function download(Assessment $assessment)
    {
        $user = auth()->user();
        if (!$user->isSuperAdmin() && $assessment->company_id !== $user->company_id) {
            abort(403);
        }

        if ($assessment->status !== 'closed') {
            abort(404, 'Report only available for closed assessments.');
        }

        $assessment->load(['valueRatings.companyValue', 'company']);

        $scoreLabel   = ScoringService::gradeLabel($assessment->overall_score ?? 0);
        $scoreColor   = ScoringService::gradeColor($assessment->overall_score ?? 0);
        $valueRatings = $assessment->valueRatings->sortByDesc('avg_score');

        $pdf = Pdf::loadView('reports.pdf', compact(
            'assessment', 'scoreLabel', 'scoreColor', 'valueRatings'
        ))->setPaper('a4', 'portrait');

        $filename = 'credibilityiq-scorecard-' . $assessment->id . '.pdf';
        return $pdf->download($filename);
    }
}
