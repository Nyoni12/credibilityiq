<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use App\Models\ResponseRating;
use App\Services\ScoringService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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

        $assessment->load(['valueRatings.companyValue', 'company', 'surveyResponses']);

        $score      = $assessment->overall_score ?? 0;
        $scoreBand  = ScoringService::gradeCode($score);
        $scoreLabel = ScoringService::gradeLabel($score);
        $scoreColor = ScoringService::gradeColor($score);
        $company    = $assessment->company;

        $bandMeta = [
            'cc'  => ['color' => '#059669', 'short' => 'CC',  'label' => 'Celebrity Credibility'],
            'gw'  => ['color' => '#ca8a04', 'short' => 'GW',  'label' => 'General Ward'],
            'hdu' => ['color' => '#ea580c', 'short' => 'HDU', 'label' => 'High Dependency Unit'],
            'icu' => ['color' => '#dc2626', 'short' => 'ICU', 'label' => 'Intensive Care Unit'],
        ];
        $bm = $bandMeta[$scoreBand];

        $valueRatings   = $assessment->valueRatings->sortByDesc('avg_score');
        $totalResponses = $assessment->surveyResponses->count();
        $trainingCount  = $valueRatings->where('training_recommended', true)->count();
        $totalLeakage   = $assessment->total_leakage ?? 0;
        $top3Gaps       = $assessment->valueRatings->sortByDesc('financial_impact')->take(3);

        // ── Score distribution ──
        $distribution = ResponseRating::whereHas('surveyResponse', fn ($q) =>
                $q->where('assessment_id', $assessment->id)
            )
            ->select(
                'company_value_id',
                DB::raw('MIN(score)              as min_score'),
                DB::raw('MAX(score)              as max_score'),
                DB::raw('ROUND(STDDEV(score),2)  as std_dev'),
                DB::raw('SUM(CASE WHEN score <= 5 THEN 1 ELSE 0 END) as below_threshold')
            )
            ->groupBy('company_value_id')
            ->get()
            ->keyBy('company_value_id');

        // ── Recovery potential (normalised to 1–10 scale, denominator = 9) ──
        $targetScore   = 9.0;
        $totalRecovery = 0;
        foreach ($valueRatings as $vr) {
            $fw = (float)($vr->companyValue->financial_weight ?? 0);
            if ($fw === 0.0 && $vr->avg_score < 10) {
                $fw = round($vr->financial_impact / ((10 - $vr->avg_score) / 9));
            }
            $saving = max(0, $fw * ($targetScore - $vr->avg_score) / 9);
            $totalRecovery += $saving;
            $vr->recovery_potential = round($saving, 2);
            $vr->effective_weight   = $fw;
        }
        $totalRecovery = round($totalRecovery, 2);

        // ── Auto-generated narrative ──
        $strongest  = $valueRatings->first();
        $weakest    = $valueRatings->last();
        $riskiest   = $assessment->valueRatings->sortByDesc('financial_impact')->first();
        $bandDesc   = [
            'cc'  => 'an excellent level of credibility',
            'gw'  => 'a satisfactory level of credibility with clear opportunities for improvement',
            'hdu' => 'a concerning credibility gap that warrants urgent management attention',
            'icu' => 'a critical credibility deficit requiring immediate intervention',
        ][$scoreBand];

        $trainingLine = $trainingCount > 0
            ? "{$trainingCount} value" . ($trainingCount > 1 ? 's' : '') . " scored below the 5.0/10 training threshold, recommending targeted learning interventions. "
            : "All values are performing above the minimum training threshold. ";

        $narrative = sprintf(
            "%s recorded an overall credibility score of %s%% (%s — %s), reflecting %s across %d values assessed. "
            . "Based on anonymous feedback from %d respondent%s, %s emerged as the strongest performing value at %s/10, "
            . "while %s carries the highest financial risk at \$%s in estimated annual leakage. "
            . "%s"
            . "Total financial leakage stands at \$%s, with \$%s potentially recoverable if all values reach %s/10.",
            e($company->name),
            number_format($score, 1),
            $bm['short'],
            $bm['label'],
            $bandDesc,
            $valueRatings->count(),
            $totalResponses,
            $totalResponses === 1 ? '' : 's',
            e($strongest?->companyValue?->name ?? '—'),
            number_format($strongest?->avg_score ?? 0, 1),
            e($riskiest?->companyValue?->name ?? '—'),
            number_format($riskiest?->financial_impact ?? 0),
            $trainingLine,
            number_format($totalLeakage),
            number_format($totalRecovery),
            $targetScore
        );

        // ── Priority action plan ──
        $actions = collect();
        foreach ($valueRatings->where('training_recommended', true)->sortByDesc('financial_impact') as $vr) {
            $valueName = e($vr->companyValue->name);
            $actions->push([
                'priority' => 'critical',
                'label'    => "Initiate training programme for <strong>{$valueName}</strong> "
                            . "(score: " . number_format($vr->avg_score, 1) . "/10 — below threshold; "
                            . "leakage: \$" . number_format($vr->financial_impact) . ")",
            ]);
        }
        foreach ($top3Gaps->whereNotIn('id', $valueRatings->where('training_recommended', true)->pluck('id')) as $vr) {
            $valueName = e($vr->companyValue->name);
            $actions->push([
                'priority' => 'high',
                'label'    => "Monitor and coach <strong>{$valueName}</strong> "
                            . "(leakage: \$" . number_format($vr->financial_impact) . ")",
            ]);
        }
        foreach ($distribution->filter(fn ($d) => $d->std_dev > 2.5) as $valueId => $dist) {
            $vr = $valueRatings->firstWhere('company_value_id', $valueId);
            if ($vr && !$actions->contains('label', fn ($_, $a) => str_contains($a['label'], $vr->companyValue->name ?? ''))) {
                $valueName = e($vr->companyValue->name);
                $actions->push([
                    'priority' => 'medium',
                    'label'    => "Investigate high score variance for <strong>{$valueName}</strong> "
                                . "(σ=" . number_format($dist->std_dev, 1) . ") — inconsistent staff perception",
                ]);
            }
        }
        if ($actions->isEmpty()) {
            $actions->push(['priority' => 'low', 'label' => 'Maintain current values performance and schedule the next assessment in 6 months.']);
        }

        $filename  = 'cfa-scorecard-' . Str::slug($company->name) . '-' . $assessment->id . '.pdf';
        $cachePath = 'reports/' . $filename;

        // Serve cached PDF if it was generated after the last assessment update
        if (Storage::exists($cachePath)) {
            $cachedAt = Storage::lastModified($cachePath);
            if ($cachedAt >= $assessment->updated_at->timestamp) {
                return response()->file(Storage::path($cachePath), [
                    'Content-Type'        => 'application/pdf',
                    'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                    'Cache-Control'       => 'private, max-age=3600',
                ]);
            }
        }

        $pdf = Pdf::loadView('reports.pdf', compact(
            'assessment', 'company',
            'scoreBand', 'scoreLabel', 'scoreColor', 'bm',
            'valueRatings', 'totalResponses', 'totalLeakage',
            'trainingCount', 'top3Gaps',
            'distribution',
            'totalRecovery', 'targetScore',
            'narrative', 'actions'
        ))->setPaper('a4', 'portrait');

        $pdfContent = $pdf->output();
        Storage::put($cachePath, $pdfContent);

        return response($pdfContent, 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control'       => 'private, max-age=3600',
        ]);
    }
}
