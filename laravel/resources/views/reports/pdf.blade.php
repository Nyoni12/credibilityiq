<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Credibility Scorecard: {{ $assessment->company->name }}</title>
<style>
* { margin:0; padding:0; box-sizing:border-box; }
body { font-family: DejaVu Sans, sans-serif; font-size:10.5px; color:#1f2937; background:#fff; line-height:1.5; }

/* ── Cover header ── */
.cover { background:#070831; color:#fff; padding:36px 40px 32px; margin:-40px -40px 28px; }
.cover-brand { font-size:11px; color:#9DA5EC; letter-spacing:.08em; text-transform:uppercase; margin-bottom:6px; }
.cover-company { font-size:22px; font-weight:900; color:#fff; margin-bottom:4px; }
.cover-title { font-size:13px; color:#9DA5EC; }
.cover-meta { margin-top:14px; font-size:9.5px; color:#717FE3; border-top:1px solid #1F2192; padding-top:10px; }

/* ── Executive summary band ── */
.exec-band { background:#f8f9ff; border:1px solid #C8CCF5; border-radius:10px; padding:18px 20px; margin-bottom:22px; }
.exec-grid { width:100%; border-collapse:collapse; }
.exec-grid td { width:25%; text-align:center; padding:0 8px; border-right:1px solid #e5e7eb; vertical-align:top; }
.exec-grid td:last-child { border-right:none; }
.exec-val { font-size:18px; font-weight:900; margin-bottom:2px; }
.exec-lbl { font-size:8.5px; color:#9ca3af; text-transform:uppercase; letter-spacing:.05em; }

/* ── Narrative ── */
.narrative { background:#fffbeb; border-left:4px solid #f59e0b; border-radius:0 8px 8px 0; padding:13px 16px; margin-bottom:22px; font-size:10px; color:#78350f; line-height:1.7; }
.narrative-title { font-weight:700; font-size:10px; color:#92400e; margin-bottom:5px; }

/* ── Section title ── */
.section-title { font-size:12px; font-weight:700; color:#1F2192; border-bottom:2px solid #1F2192; padding-bottom:5px; margin-bottom:14px; margin-top:22px; }

/* ── CLC badge ── */
.clc-badge { display:inline-block; padding:3px 10px; border-radius:20px; font-size:9px; font-weight:700; }

/* ── Value score bars ── */
.val-row { margin-bottom:13px; }
.val-name { font-weight:700; font-size:10px; margin-bottom:3px; }
.bar-wrap { background:#f3f4f6; border-radius:5px; height:9px; margin-bottom:3px; }
.bar-fill { height:9px; border-radius:5px; }
.val-meta { font-size:8.5px; color:#9ca3af; }

/* ── Tables ── */
table { width:100%; border-collapse:collapse; font-size:9.5px; }
th { background:#1F2192; color:#fff; text-align:left; padding:8px 10px; font-size:8.5px; font-weight:700; letter-spacing:.04em; }
th.right { text-align:right; }
td { padding:8px 10px; border-bottom:1px solid #f3f4f6; vertical-align:middle; }
td.right { text-align:right; }
tr:nth-child(even) td { background:#f9fafb; }
.tfoot td { background:#fffbeb; font-weight:700; border-top:2px solid #fbbf24; font-size:10px; }
.top3-row td { background:#fff5f5 !important; }
.top3-dot { display:inline-block; width:7px; height:7px; border-radius:50%; background:#ef4444; margin-right:4px; vertical-align:middle; }

/* ── Distribution mini-bar ── */
.dist-wrap { background:#f3f4f6; border-radius:3px; height:6px; width:70px; position:relative; display:inline-block; vertical-align:middle; }
.dist-range { height:6px; border-radius:3px; position:absolute; opacity:.35; }
.dist-avg { height:6px; width:2px; border-radius:1px; position:absolute; top:0; }

/* ── Action plan ── */
.action-row { padding:7px 0; border-bottom:1px solid #f3f4f6; font-size:9.5px; }
.action-row:last-child { border-bottom:none; }
.badge-critical { background:#fef2f2; color:#dc2626; border:1px solid #fecaca; border-radius:4px; padding:1px 6px; font-size:8px; font-weight:700; }
.badge-high     { background:#fff7ed; color:#ea580c; border:1px solid #fed7aa; border-radius:4px; padding:1px 6px; font-size:8px; font-weight:700; }
.badge-medium   { background:#fefce8; color:#ca8a04; border:1px solid #fde68a; border-radius:4px; padding:1px 6px; font-size:8px; font-weight:700; }
.badge-low      { background:#f0fdf4; color:#16a34a; border:1px solid #bbf7d0; border-radius:4px; padding:1px 6px; font-size:8px; font-weight:700; }

/* ── Training cards ── */
.training-table { width:100%; border-collapse:collapse; }
.training-table td { border:1px solid #fed7aa; border-radius:6px; padding:10px 12px; vertical-align:top; }

/* ── Footer ── */
.footer { margin-top:36px; padding-top:12px; border-top:1px solid #e5e7eb; font-size:8.5px; color:#9ca3af; text-align:center; }
.footer strong { color:#1F2192; }

/* ── Page break ── */
.page-break { page-break-before:always; padding-top:20px; }
</style>
</head>
<body>
<div style="padding:40px">

{{-- ══════════════════════════════════════════════════════════ --}}
{{-- ── COVER HEADER ── --}}
{{-- ══════════════════════════════════════════════════════════ --}}
<div class="cover">
    <div class="cover-brand">Credibility Factory Afrique &nbsp;·&nbsp; Official Scorecard Report</div>
    <div class="cover-company">{{ $assessment->company->name }}</div>
    <div class="cover-title">{{ $assessment->title }}</div>
    <div class="cover-meta">
        Generated {{ now()->format('d M Y, H:i') }}
        &nbsp;·&nbsp; Closed {{ $assessment->closed_at?->format('d M Y') }}
        &nbsp;·&nbsp; {{ $totalResponses }} Respondent{{ $totalResponses !== 1 ? 's' : '' }}
        &nbsp;·&nbsp; {{ $valueRatings->count() }} Values Assessed
        &nbsp;·&nbsp; CONFIDENTIAL
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════ --}}
{{-- ── EXECUTIVE SUMMARY ── --}}
{{-- ══════════════════════════════════════════════════════════ --}}
<div class="exec-band">
    <table class="exec-grid">
        <tr>
            <td>
                <div class="exec-val" style="color:{{ $bm['color'] }}">{{ number_format($assessment->overall_score ?? 0, 1) }}%</div>
                <div class="exec-lbl">Overall Score</div>
            </td>
            <td>
                <div class="exec-val" style="color:{{ $bm['color'] }};font-size:13px">{{ $bm['short'] }}</div>
                <div style="font-size:8px;color:#6b7280;margin-top:1px">{{ $bm['label'] }}</div>
                <div class="exec-lbl">CLC Band</div>
            </td>
            <td>
                <div class="exec-val" style="color:#dc2626">${{ number_format($totalLeakage) }}</div>
                <div class="exec-lbl">Total Leakage</div>
            </td>
            <td>
                <div class="exec-val" style="color:#16a34a">${{ number_format($totalRecovery) }}</div>
                <div style="font-size:8px;color:#6b7280;margin-top:1px">if all values reach {{ $targetScore }}/10</div>
                <div class="exec-lbl">Recovery Potential</div>
            </td>
        </tr>
    </table>
</div>

{{-- ══════════════════════════════════════════════════════════ --}}
{{-- ── NARRATIVE SUMMARY ── --}}
{{-- ══════════════════════════════════════════════════════════ --}}
<div class="narrative">
    <div class="narrative-title">📋 Report Summary</div>
    {!! $narrative !!}
</div>

{{-- ══════════════════════════════════════════════════════════ --}}
{{-- ── VALUE PERFORMANCE ── --}}
{{-- ══════════════════════════════════════════════════════════ --}}
<div class="section-title">Value Performance Breakdown</div>
@foreach($valueRatings as $vr)
@php
    $pct    = ($vr->avg_score - 1) / 9 * 100;
    $barPct = $vr->avg_score * 10;
    $clr    = \App\Services\ScoringService::gradeColor($pct);
    $dist   = $distribution[$vr->company_value_id] ?? null;
    $minS   = $dist?->min_score ?? 0;
    $maxS   = $dist?->max_score ?? 10;
    $stdDev = $dist?->std_dev ?? 0;
    $below  = $dist?->below_threshold ?? 0;
    $minPct = $minS * 10;
    $maxPct = $maxS * 10;
@endphp
<div class="val-row">
    <table style="width:100%;border-collapse:collapse">
        <tr>
            <td style="width:36%;padding:0 0 2px 0;vertical-align:middle">
                <span class="val-name">{{ $vr->companyValue->name }}</span>
            </td>
            <td style="width:38%;padding:0 8px 2px;vertical-align:middle">
                <div class="bar-wrap">
                    <div class="bar-fill" style="width:{{ $barPct }}%;background:{{ $clr }}"></div>
                </div>
            </td>
            <td style="width:26%;padding:0 0 2px;vertical-align:middle;text-align:right">
                <span style="font-weight:700;font-size:10px;color:{{ $clr }}">{{ number_format($vr->avg_score,1) }}/10</span>
                &nbsp;
                <span class="clc-badge" style="background:{{ $clr }}18;color:{{ $clr }}">
                    {{ \App\Services\ScoringService::gradeShort($pct) }}
                </span>
                @if($vr->training_recommended)
                &nbsp;<span style="font-size:8px;color:#ea580c;font-weight:700">⚠ Training</span>
                @endif
            </td>
        </tr>
        <tr>
            <td colspan="3" style="padding:0 0 2px 0">
                <span class="val-meta">
                    Min: {{ $minS }}
                    &nbsp;·&nbsp; Max: {{ $maxS }}
                    &nbsp;·&nbsp; Std Dev: {{ number_format($stdDev,1) }}{{ $stdDev > 2.5 ? ' ⚠ High variance' : '' }}
                    &nbsp;·&nbsp; Rated below 5: {{ $below }}
                    &nbsp;·&nbsp; Gap: {{ number_format((10 - $vr->avg_score)/9*100, 1) }}%
                    &nbsp;·&nbsp; Leakage: ${{ number_format($vr->financial_impact) }}
                </span>
            </td>
        </tr>
    </table>
</div>
@endforeach

{{-- ══════════════════════════════════════════════════════════ --}}
{{-- ── FINANCIAL LEAKAGE ── --}}
{{-- ══════════════════════════════════════════════════════════ --}}
@if($totalLeakage > 0)
<div class="section-title" style="margin-top:28px">Financial Leakage Analysis</div>
@php $leakageSorted = $valueRatings->sortByDesc('financial_impact'); $top3Ids = $top3Gaps->pluck('id')->toArray(); @endphp
<table>
    <thead>
        <tr>
            <th>Value</th>
            <th class="right">Avg Score</th>
            <th class="right">Financial Weight</th>
            <th class="right">Gap %</th>
            <th class="right">$ Leakage</th>
            <th class="right">Recovery Potential</th>
        </tr>
    </thead>
    <tbody>
        @foreach($leakageSorted as $vr)
        @php
            $isTop3   = in_array($vr->id, $top3Ids);
            $gap      = round((10 - $vr->avg_score) / 9 * 100, 1);
            $fw       = $vr->effective_weight ?? 0;
            $recovery = $vr->recovery_potential ?? 0;
            $clr      = \App\Services\ScoringService::gradeColor(($vr->avg_score - 1) / 9 * 100);
        @endphp
        <tr class="{{ $isTop3 ? 'top3-row' : '' }}">
            <td style="font-weight:600">
                @if($isTop3)<span class="top3-dot"></span>@endif
                {{ $vr->companyValue->name }}
            </td>
            <td class="right" style="font-weight:700;color:{{ $clr }}">{{ number_format($vr->avg_score,1) }}/10</td>
            <td class="right">${{ number_format($fw) }}</td>
            <td class="right;font-weight:600;color:{{ $gap >= 50 ? '#dc2626' : ($gap >= 30 ? '#ca8a04' : '#059669') }}">{{ $gap }}%</td>
            <td class="right" style="font-weight:700;color:#dc2626">${{ number_format($vr->financial_impact) }}</td>
            <td class="right" style="color:{{ $recovery > 0 ? '#16a34a' : '#9ca3af' }};font-weight:{{ $recovery > 0 ? '700' : '400' }}">
                {{ $recovery > 0 ? '+$' . number_format($recovery) : '—' }}
            </td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td colspan="4" class="tfoot">Total Financial Leakage at Risk</td>
            <td class="tfoot right" style="color:#dc2626;font-size:11px">${{ number_format($totalLeakage) }}</td>
            <td class="tfoot right" style="color:#16a34a;font-size:11px">+${{ number_format($totalRecovery) }}</td>
        </tr>
    </tfoot>
</table>
<p style="font-size:8px;color:#9ca3af;margin-top:6px">
    <span class="top3-dot"></span>Top 3 costliest gaps highlighted.
    Recovery potential = leakage recoverable if each value reaches {{ $targetScore }}/10.
</p>
@endif

{{-- ══════════════════════════════════════════════════════════ --}}
{{-- ── PRIORITY ACTION PLAN ── --}}
{{-- ══════════════════════════════════════════════════════════ --}}
<div class="section-title page-break">Priority Action Plan</div>
<p style="font-size:9px;color:#6b7280;margin-bottom:12px">
    Actions are sequenced by financial impact and urgency. Critical items require immediate scheduling.
</p>
@foreach($actions as $i => $action)
<div class="action-row">
    <table style="width:100%;border-collapse:collapse">
        <tr>
            <td style="width:22px;font-weight:900;font-size:13px;color:#1F2192;padding:0;vertical-align:middle">{{ $i + 1 }}</td>
            <td style="width:64px;padding:0 8px;vertical-align:middle">
                <span class="badge-{{ $action['priority'] }}">{{ strtoupper($action['priority']) }}</span>
            </td>
            <td style="padding:0;vertical-align:middle">{!! $action['label'] !!}</td>
        </tr>
    </table>
</div>
@endforeach

{{-- ══════════════════════════════════════════════════════════ --}}
{{-- ── TRAINING RECOMMENDATIONS ── --}}
{{-- ══════════════════════════════════════════════════════════ --}}
@if($trainingCount > 0)
<div class="section-title" style="margin-top:28px">Training Recommendations</div>
<p style="font-size:9px;color:#6b7280;margin-bottom:12px">
    The following values scored at or below 5.0/10 and meet the threshold for formal training intervention.
</p>
<table>
    <thead>
        <tr>
            <th>Value</th>
            <th class="right">Score</th>
            <th class="right">$ Leakage</th>
            <th class="right">Recovery if Fixed</th>
            <th class="right">Std Dev</th>
            <th>Notes</th>
        </tr>
    </thead>
    <tbody>
        @foreach($valueRatings->where('training_recommended', true)->sortByDesc('financial_impact') as $vr)
        @php
            $dist   = $distribution[$vr->company_value_id] ?? null;
            $stdDev = $dist?->std_dev ?? 0;
            $clr    = \App\Services\ScoringService::gradeColor(($vr->avg_score - 1) / 9 * 100);
        @endphp
        <tr>
            <td style="font-weight:700">{{ $vr->companyValue->name }}</td>
            <td class="right" style="font-weight:700;color:{{ $clr }}">{{ number_format($vr->avg_score,1) }}/10</td>
            <td class="right" style="color:#dc2626;font-weight:700">${{ number_format($vr->financial_impact) }}</td>
            <td class="right" style="color:#16a34a;font-weight:700">+${{ number_format($vr->recovery_potential ?? 0) }}</td>
            <td class="right" style="color:{{ $stdDev > 2.5 ? '#dc2626' : '#6b7280' }};font-weight:{{ $stdDev > 2.5 ? '700' : '400' }}">
                {{ number_format($stdDev,1) }}{{ $stdDev > 2.5 ? ' ⚠' : '' }}
            </td>
            <td style="font-size:8.5px;color:#6b7280">
                @if($stdDev > 2.5) High disagreement between staff. @endif
                @if(($dist?->below_threshold ?? 0) > 0) {{ $dist->below_threshold }} rated below 5. @endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif

{{-- ══════════════════════════════════════════════════════════ --}}
{{-- ── FOOTER ── --}}
{{-- ══════════════════════════════════════════════════════════ --}}
<div class="footer">
    <strong>Credibility Factory Afrique</strong> &nbsp;·&nbsp;
    Corporate Credibility Scorecard Platform &nbsp;·&nbsp;
    This report is confidential and intended solely for the authorised use of <strong>{{ $assessment->company->name }}</strong>. &nbsp;·&nbsp;
    Generated {{ now()->format('d M Y, H:i') }}
</div>

</div>
</body>
</html>
