<?php

namespace App\Http\Controllers;

use App\Models\Assessment;

class DashboardController extends Controller
{
    public function index()
    {
        $user    = auth()->user();
        $company = $user->company;

        if (!$company) {
            return redirect()->route('login')->with('error', 'No company linked to your account.');
        }

        $assessments       = $company->assessments()->with('valueRatings.companyValue')->get();
        $latestClosed      = $assessments->where('status', 'closed')->first();
        $totalAssessments  = $assessments->count();
        $openAssessments   = $assessments->where('status', 'open')->count();
        $valuesCount       = $company->values()->count();

        $trendData = $assessments->where('status', 'closed')
            ->take(6)->reverse()->values()
            ->map(fn ($a) => [
                'label' => $a->created_at->format('M Y'),
                'score' => $a->overall_score ?? 0,
            ]);

        return view('dashboard.index', compact(
            'company', 'latestClosed', 'assessments',
            'totalAssessments', 'openAssessments', 'valuesCount', 'trendData'
        ));
    }
}
