<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Credibility Scorecard — {{ $assessment->company->name }}</title>
<style>
* { margin: 0; padding: 0; box-sizing: border-box; }
body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1f2937; background: #fff; }
.page { padding: 40px; }

/* Header */
.header { background: #070831; color: white; padding: 30px 40px; margin: -40px -40px 30px; }
.header-logo { font-size: 22px; font-weight: 900; color: white; }
.header-sub { color: #9DA5EC; font-size: 10px; margin-top: 2px; }
.header-company { font-size: 18px; font-weight: 700; color: white; margin-top: 16px; }
.header-meta { color: #9DA5EC; font-size: 10px; margin-top: 4px; }

/* Score hero */
.score-section { border: 1px solid #e5e7eb; border-radius: 12px; padding: 24px; margin-bottom: 24px; background: #f9fafb; }
.score-big { font-size: 56px; font-weight: 900; color: {{ $scoreColor }}; line-height: 1; }
.score-label { font-size: 14px; font-weight: 700; color: {{ $scoreColor }}; margin-top: 4px; }
.stats-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; margin-top: 20px; }
.stat-box { background: white; border: 1px solid #e5e7eb; border-radius: 8px; padding: 14px; text-align: center; }
.stat-value { font-size: 22px; font-weight: 900; color: #1f2937; }
.stat-label { font-size: 9px; color: #9ca3af; margin-top: 2px; }

/* Section title */
.section-title { font-size: 13px; font-weight: 700; color: #1F2192; border-bottom: 2px solid #1F2192; padding-bottom: 6px; margin-bottom: 16px; margin-top: 24px; }

/* Value bars */
.value-row { margin-bottom: 14px; }
.value-name { font-weight: 600; font-size: 11px; margin-bottom: 4px; color: #1f2937; }
.bar-wrap { background: #f3f4f6; border-radius: 6px; height: 10px; margin-bottom: 3px; }
.bar-fill { height: 10px; border-radius: 6px; }
.value-meta { font-size: 9px; color: #9ca3af; display: flex; justify-content: space-between; }

/* Leakage table */
table { width: 100%; border-collapse: collapse; }
th { background: #f3f4f6; font-size: 9px; font-weight: 700; color: #6b7280; text-align: left; padding: 8px 12px; text-transform: uppercase; }
td { padding: 9px 12px; font-size: 10px; border-bottom: 1px solid #f9fafb; }
tr:last-child td { border-bottom: none; }
.tfoot td { background: #f9fafb; font-weight: 700; border-top: 2px solid #e5e7eb; }

/* Footer */
.footer { margin-top: 40px; padding-top: 16px; border-top: 1px solid #e5e7eb; font-size: 9px; color: #9ca3af; text-align: center; }
</style>
</head>
<body>
<div class="page">

    <div class="header">
        <div class="header-logo">CIQ CredibilityIQ</div>
        <div class="header-sub">Credibility Factory Afrique · Official Scorecard Report</div>
        <div class="header-company">{{ $assessment->company->name }}</div>
        <div class="header-meta">
            {{ $assessment->title }} · Generated {{ now()->format('d M Y') }}
            · Closed {{ $assessment->closed_at?->format('d M Y') }}
            · {{ $assessment->surveyResponses()->count() }} Respondents
        </div>
    </div>

    <!-- Overall Score -->
    <div class="score-section">
        <div style="display:flex; align-items:flex-start; gap:24px;">
            <div>
                <div class="score-big">{{ round($assessment->overall_score ?? 0) }}</div>
                <div style="font-size:11px; color:#6b7280; margin-top:2px;">/100 Overall</div>
                <div class="score-label">{{ $scoreLabel }}</div>
            </div>
            <div style="flex:1;">
                <p style="font-size:11px; color:#6b7280; margin-bottom:12px;">
                    This score reflects the weighted average credibility rating provided by
                    {{ $assessment->surveyResponses()->count() }} stakeholders across {{ count($valueRatings) }} company values.
                    @if($assessment->total_leakage > 0)
                    Estimated financial leakage of <strong style="color:#ef4444">${{ number_format($assessment->total_leakage) }}</strong>
                    has been identified.
                    @endif
                </p>
                <div class="stats-grid">
                    <div class="stat-box">
                        <div class="stat-value">{{ $assessment->surveyResponses()->count() }}</div>
                        <div class="stat-label">Respondents</div>
                    </div>
                    <div class="stat-box">
                        <div class="stat-value" style="color:#ef4444;">${{ number_format($assessment->total_leakage ?? 0) }}</div>
                        <div class="stat-label">Est. Leakage</div>
                    </div>
                    <div class="stat-box">
                        <div class="stat-value" style="color:{{ $scoreColor }}">{{ $scoreLabel }}</div>
                        <div class="stat-label">Grade</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Value Scores -->
    <div class="section-title">Value Scores</div>
    @foreach($valueRatings as $vr)
    @php
        $pct = $vr->avg_score * 10;
        $color = \App\Services\ScoringService::gradeColor($pct);
    @endphp
    <div class="value-row">
        <div class="value-name">{{ $vr->companyValue->name }} <span style="color:#9ca3af;font-weight:400;">({{ $vr->companyValue->weight_percentage }}% weight)</span></div>
        <div class="bar-wrap">
            <div class="bar-fill" style="width:{{ $pct }}%; background:{{ $color }};"></div>
        </div>
        <div class="value-meta">
            <span>Score: {{ number_format($vr->avg_score, 1) }}/10 ({{ number_format($pct, 1) }}%)</span>
            <span>{{ $vr->training_recommended ? '⚠ Training Recommended' : 'OK' }}</span>
        </div>
    </div>
    @endforeach

    <!-- Financial Leakage -->
    @if($assessment->total_leakage > 0)
    <div class="section-title">Financial Leakage Analysis</div>
    <table>
        <thead>
            <tr>
                <th>Value</th>
                <th>Avg Score</th>
                <th>Weight</th>
                <th>Est. Leakage</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($valueRatings->sortByDesc('financial_impact') as $vr)
            <tr>
                <td style="font-weight:600">{{ $vr->companyValue->name }}</td>
                <td style="color:{{ \App\Services\ScoringService::gradeColor($vr->avg_score * 10) }}; font-weight:700">{{ number_format($vr->avg_score,1) }}/10</td>
                <td>{{ $vr->companyValue->weight_percentage }}%</td>
                <td style="color:#ef4444; font-weight:700">${{ number_format($vr->financial_impact) }}</td>
                <td>{{ $vr->training_recommended ? '🎯 Training Required' : ($vr->avg_score < 7 ? '⚠ Needs Attention' : '✓ On Track') }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="tfoot">
                <td colspan="3" style="font-weight:700">Total Estimated Financial Leakage</td>
                <td style="color:#ef4444; font-size:13px; font-weight:900">${{ number_format($assessment->total_leakage) }}</td>
                <td></td>
            </tr>
        </tfoot>
    </table>
    @endif

    <div class="footer">
        CredibilityIQ · Credibility Factory Afrique · This report is confidential and intended solely for the use of {{ $assessment->company->name }}.
        Generated on {{ now()->format('d M Y, H:i') }}
    </div>

</div>
</body>
</html>
