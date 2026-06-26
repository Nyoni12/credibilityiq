<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use App\Services\ScoringService;
use Illuminate\Http\Request;

class AssessmentController extends Controller
{
    public function __construct(private ScoringService $scoring) {}

    public function index()
    {
        $company     = auth()->user()->company;
        $assessments = $company->assessments()->withCount('surveyResponses')->get();

        return view('assessments.index', compact('company', 'assessments'));
    }

    public function store(Request $request)
    {
        $company = auth()->user()->company;

        $data = $request->validate([
            'title' => ['required', 'string', 'max:200'],
        ]);

        // Warn if values aren't configured
        if ($company->values()->count() === 0) {
            return back()->with('error', 'Please configure your company values before starting an assessment.');
        }

        $assessment = $company->assessments()->create([
            'title'  => $data['title'],
            'status' => 'open',
        ]);

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

        return redirect()->route('scorecard.show', $assessment)
            ->with('success', 'Assessment closed. Your scorecard is ready!');
    }

    public function destroy(Assessment $assessment)
    {
        $this->authorizeAssessment($assessment);
        $assessment->delete();
        return back()->with('success', 'Assessment deleted.');
    }

    private function authorizeAssessment(Assessment $assessment): void
    {
        $user = auth()->user();
        if (!$user->isSuperAdmin() && $assessment->company_id !== $user->company_id) {
            abort(403);
        }
    }
}
