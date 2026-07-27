<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Assessment;
use App\Services\ScoringService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class AssessmentController extends Controller
{
    public function __construct(private ScoringService $scoring) {}

    public function index()
    {
        $company     = auth()->user()->company;
        $assessments = $company->assessments()->withCount('surveyResponses')->get();
        $canCreate   = $company->canCreateAssessment();

        return view('assessments.index', compact('company', 'assessments', 'canCreate'));
    }

    public function store(Request $request)
    {
        $company = auth()->user()->company;

        if (!$company->canCreateAssessment()) {
            return back()->with('error', 'You have reached your assessment limit. Contact your administrator to unlock a new assessment.');
        }

        $data = $request->validate([
            'title' => ['required', 'string', 'max:200'],
        ]);

        if ($company->values()->count() === 0) {
            return back()->with('error', 'Please configure your company values before starting an assessment.');
        }

        $assessment = $company->assessments()->create([
            'title'  => $data['title'],
            'status' => 'open',
        ]);

        ActivityLog::record('assessment.created', $assessment, $assessment->title, [], $company->id);
        AdminController::bustCache();

        return redirect()->route('assessments.index')
            ->with('success', "Assessment \"{$assessment->title}\" created. Share the survey link with your stakeholders.");
    }

    public function close(Assessment $assessment)
    {
        $this->authorizeAssessment($assessment);

        if ($assessment->status === 'closed') {
            return back()->with('error', 'Assessment is already closed.');
        }

        if ($assessment->surveyResponses()->count() === 0) {
            return back()->with('error', 'Cannot close an assessment with no survey responses.');
        }

        $this->scoring->closeAssessment($assessment);
        $assessment->refresh();

        ActivityLog::record('assessment.closed', $assessment, $assessment->title, [
            'score' => $assessment->overall_score,
        ], $assessment->company_id);
        AdminController::bustCache();

        return redirect()->route('scorecard.show', $assessment)
            ->with('success', 'Assessment closed. Your scorecard is ready!');
    }

    public function recalculate(Assessment $assessment)
    {
        $this->authorizeAssessment($assessment);

        if ($assessment->status !== 'closed') {
            return back()->with('error', 'Only closed assessments can be recalculated.');
        }

        if ($assessment->surveyResponses()->count() === 0) {
            return back()->with('error', 'No survey responses to recalculate from.');
        }

        $this->scoring->closeAssessment($assessment);
        $assessment->refresh();

        ActivityLog::record('assessment.recalculated', $assessment, $assessment->title, [
            'score' => $assessment->overall_score,
        ], $assessment->company_id);
        AdminController::bustCache();

        return redirect()->route('scorecard.show', $assessment)
            ->with('success', 'Scorecard recalculated using current financial weights.');
    }

    public function destroy(Assessment $assessment)
    {
        $this->authorizeAssessment($assessment);

        ActivityLog::record('assessment.deleted', null, $assessment->title . ' (id:' . $assessment->id . ')', [], $assessment->company_id);
        $assessment->delete();

        return back()->with('success', 'Assessment deleted.');
    }

    public function export()
    {
        $company     = auth()->user()->company;
        $assessments = $company->assessments()
            ->withCount('surveyResponses')
            ->orderByDesc('created_at')
            ->get();

        $filename = 'assessments-' . now()->format('Y-m-d') . '.csv';

        ActivityLog::record('assessments.exported', null, 'CSV Export', [], $company->id);

        $callback = function () use ($assessments) {
            $f = fopen('php://output', 'w');
            fputcsv($f, ['Title', 'Status', 'Overall Score (%)', 'Total Leakage', 'Responses', 'Created', 'Closed']);
            foreach ($assessments as $a) {
                fputcsv($f, [
                    $a->title,
                    $a->status,
                    $a->overall_score !== null ? number_format($a->overall_score, 2) : '',
                    $a->total_leakage ?? '',
                    $a->survey_responses_count,
                    $a->created_at->format('Y-m-d'),
                    $a->closed_at?->format('Y-m-d') ?? '',
                ]);
            }
            fclose($f);
        };

        return Response::stream($callback, 200, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    private function authorizeAssessment(Assessment $assessment): void
    {
        $user = auth()->user();
        if (!$user->isSuperAdmin() && $assessment->company_id !== $user->company_id) {
            abort(403);
        }
    }
}
