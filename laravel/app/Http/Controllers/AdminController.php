<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use App\Models\Company;
use App\Models\SurveyResponse;
use App\Models\User;
use App\Models\ValueRating;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function dashboard()
    {
        // Admin dashboard aggregates ALL tenant data — cache for 5 minutes
        // so a single superadmin page load doesn't fire 15+ cross-tenant queries.
        return Cache::remember('admin.dashboard', 300, function () {
            return $this->buildDashboard();
        });
    }

    private function buildDashboard()
    {
        // ── Core counts ──────────────────────────────────────────────
        $stats = [
            'companies'          => Company::count(),
            'active_companies'   => Company::where('is_active', true)->count(),
            'users'              => User::where('role', '!=', 'superadmin')->count(),
            'assessments'        => Assessment::count(),
            'open_assessments'   => Assessment::where('status', 'open')->count(),
            'closed_assessments' => Assessment::where('status', 'closed')->count(),
            'total_responses'    => SurveyResponse::count(),
        ];

        // ── Platform impact ──────────────────────────────────────────
        $totalLeakage = Assessment::where('status', 'closed')->sum('total_leakage');
        $avgScore     = Assessment::where('status', 'closed')->avg('overall_score') ?? 0;

        // ── Month-over-month growth ──────────────────────────────────
        $newThisMonth = Company::whereYear('created_at', now()->year)
                               ->whereMonth('created_at', now()->month)->count();
        $newLastMonth = Company::whereYear('created_at', now()->subMonth()->year)
                               ->whereMonth('created_at', now()->subMonth()->month)->count();
        $growthPct    = $newLastMonth > 0
                            ? round((($newThisMonth - $newLastMonth) / $newLastMonth) * 100)
                            : ($newThisMonth > 0 ? 100 : 0);

        // ── Engagement metrics ───────────────────────────────────────
        $repeatClients  = Company::has('assessments', '>=', 2)->count();
        $completionRate = $stats['assessments'] > 0
                            ? round($stats['closed_assessments'] / $stats['assessments'] * 100)
                            : 0;
        $trainingFlags  = ValueRating::where('training_recommended', true)->count();
        $avgAssessments = $stats['companies'] > 0
                            ? round($stats['assessments'] / $stats['companies'], 1)
                            : 0;

        // ── 12-month chart data ──────────────────────────────────────
        $monthlySignupsRaw = Company::selectRaw('YEAR(created_at) as y, MONTH(created_at) as m, COUNT(*) as cnt')
            ->where('created_at', '>=', now()->subMonths(11)->startOfMonth())
            ->groupBy('y', 'm')
            ->get()->keyBy(fn($r) => $r->y . '-' . $r->m);

        $monthlyClosedRaw = Assessment::selectRaw(
                'YEAR(closed_at) as y, MONTH(closed_at) as m,
                 COUNT(*) as cnt,
                 ROUND(AVG(overall_score),1) as avg_s,
                 SUM(total_leakage) as total_l'
            )
            ->where('status', 'closed')->whereNotNull('closed_at')
            ->where('closed_at', '>=', now()->subMonths(11)->startOfMonth())
            ->groupBy('y', 'm')
            ->get()->keyBy(fn($r) => $r->y . '-' . $r->m);

        $chartLabels = $chartSignups = $chartClosed = $chartLeakage = $chartAvgScore = [];

        for ($i = 11; $i >= 0; $i--) {
            $d   = now()->subMonths($i);
            $key = $d->year . '-' . $d->month;
            $chartLabels[]   = $d->format('M y');
            $chartSignups[]  = (int)   ($monthlySignupsRaw[$key]->cnt     ?? 0);
            $chartClosed[]   = (int)   ($monthlyClosedRaw[$key]->cnt      ?? 0);
            $chartLeakage[]  = (float) ($monthlyClosedRaw[$key]->total_l  ?? 0);
            $chartAvgScore[] = isset($monthlyClosedRaw[$key]) ? (float)$monthlyClosedRaw[$key]->avg_s : null;
        }

        $chartData = compact('chartLabels', 'chartSignups', 'chartClosed', 'chartLeakage', 'chartAvgScore');

        // ── Onboarding funnel ────────────────────────────────────────
        $funnel = [
            ['label' => 'Signed Up',         'count' => Company::count()],
            ['label' => 'Values Configured',  'count' => Company::has('values')->count()],
            ['label' => 'Assessment Started', 'count' => Company::has('assessments')->count()],
            ['label' => 'Assessment Closed',  'count' => Company::whereHas('assessments', fn($q) => $q->where('status', 'closed'))->count()],
        ];

        // ── CLC band distribution ────────────────────────────────────
        $closedScores = Assessment::where('status', 'closed')
            ->whereNotNull('overall_score')
            ->select('company_id', DB::raw('MAX(overall_score) as score'))
            ->groupBy('company_id')
            ->pluck('score');

        $bands = ['CC' => 0, 'GW' => 0, 'HDU' => 0, 'ICU' => 0];
        foreach ($closedScores as $score) {
            if ($score > 89)     $bands['CC']++;
            elseif ($score > 65) $bands['GW']++;
            elseif ($score > 50) $bands['HDU']++;
            else                 $bands['ICU']++;
        }
        $bandMeta = [
            'CC'  => ['label' => 'Celebrity Credibility', 'color' => '#059669', 'bg' => '#d1fae5'],
            'GW'  => ['label' => 'General Ward',          'color' => '#ca8a04', 'bg' => '#fef9c3'],
            'HDU' => ['label' => 'High Dependency Unit',  'color' => '#ea580c', 'bg' => '#ffedd5'],
            'ICU' => ['label' => 'Intensive Care Unit',   'color' => '#dc2626', 'bg' => '#fee2e2'],
        ];

        // ── Assessment score distribution (ALL closed assessments) ────────
        $allAssessmentScores = Assessment::where('status', 'closed')
            ->whereNotNull('overall_score')
            ->pluck('overall_score');

        $assessmentBands = ['CC' => 0, 'GW' => 0, 'HDU' => 0, 'ICU' => 0];
        foreach ($allAssessmentScores as $score) {
            if ($score > 89)     $assessmentBands['CC']++;
            elseif ($score > 65) $assessmentBands['GW']++;
            elseif ($score > 50) $assessmentBands['HDU']++;
            else                 $assessmentBands['ICU']++;
        }

        // ── Recently closed assessments ──────────────────────────────
        $recentClosed = Assessment::with('company')
            ->where('status', 'closed')->whereNotNull('closed_at')
            ->orderByDesc('closed_at')->take(8)->get();

        // ── Top leakage companies ────────────────────────────────────
        $topLeakage = Assessment::with('company')
            ->where('status', 'closed')->where('total_leakage', '>', 0)
            ->orderByDesc('total_leakage')->take(6)->get();

        // ── Most flagged values across platform ──────────────────────
        $mostFlaggedValues = DB::table('value_ratings')
            ->join('company_values', 'value_ratings.company_value_id', '=', 'company_values.id')
            ->where('value_ratings.training_recommended', true)
            ->select('company_values.name', DB::raw('COUNT(*) as flag_count'))
            ->groupBy('company_values.name')
            ->orderByDesc('flag_count')
            ->take(8)->get();

        // ── Stale open assessments (open > 14 days) ──────────────────
        $staleOpen = Assessment::with('company')
            ->where('status', 'open')->where('created_at', '<', now()->subDays(14))
            ->withCount('surveyResponses')->orderBy('created_at')
            ->take(6)->get();

        // ── Companies with no assessments ────────────────────────────
        $noAssessments = Company::doesntHave('assessments')
            ->withCount('users')->orderByDesc('created_at')
            ->take(6)->get();

        // ── Data quality: closed with < 5 responses ──────────────────
        $lowResponseClosed = Assessment::with('company')
            ->where('status', 'closed')
            ->withCount('surveyResponses')
            ->having('survey_responses_count', '<', 5)
            ->orderBy('survey_responses_count')
            ->take(6)->get();

        // ── Churn risk: closed before but no activity > 90 days ──────
        $churnRisk = Company::whereHas('assessments', fn($q) => $q->where('status', 'closed'))
            ->whereDoesntHave('assessments', fn($q) => $q->where('created_at', '>=', now()->subDays(90)))
            ->withCount('assessments')
            ->take(6)->get();

        // ── Industry distribution ────────────────────────────────────
        $industryDist = Company::selectRaw(
                "CASE WHEN industry IS NULL OR industry = '' THEN 'Unspecified' ELSE industry END as industry,
                 COUNT(*) as company_count"
            )
            ->groupBy('industry')
            ->orderByDesc('company_count')
            ->take(12)
            ->get();

        $industryScores = DB::table('companies')
            ->join('assessments', 'companies.id', '=', 'assessments.company_id')
            ->where('assessments.status', 'closed')
            ->whereNotNull('assessments.overall_score')
            ->selectRaw(
                "CASE WHEN companies.industry IS NULL OR companies.industry = '' THEN 'Unspecified' ELSE companies.industry END as industry,
                 ROUND(AVG(assessments.overall_score), 1) as avg_score,
                 COUNT(DISTINCT companies.id) as scored_count"
            )
            ->groupBy('companies.industry')
            ->orderByDesc('avg_score')
            ->get()
            ->keyBy('industry');

        // ── Recent companies ─────────────────────────────────────────
        $recentCompanies = Company::withCount('users')->latest()->take(5)->get();

        return view('admin.index', compact(
            'stats', 'totalLeakage', 'avgScore',
            'newThisMonth', 'newLastMonth', 'growthPct',
            'repeatClients', 'completionRate', 'trainingFlags', 'avgAssessments',
            'chartData', 'funnel', 'bands', 'bandMeta', 'assessmentBands',
            'industryDist', 'industryScores',
            'recentClosed', 'topLeakage', 'mostFlaggedValues',
            'staleOpen', 'noAssessments',
            'lowResponseClosed', 'churnRisk',
            'recentCompanies'
        ));
    }

    // Bust the admin dashboard cache whenever a new assessment closes or
    // a company is created — call this from AssessmentController and CompanyController.
    public static function bustCache(): void
    {
        Cache::forget('admin.dashboard');
    }
}
