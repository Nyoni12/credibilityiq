@extends('layouts.public')

@section('title', 'CredibilityIQ Platform | Credibility Factory Afrique')

@section('meta')
<meta name="description" content="CredibilityIQ is our proprietary digital assessment platform that measures organisational credibility across all 12 Building Blocks, identifies financial leakage, and generates priority action plans.">
<link rel="canonical" href="{{ config('app.url') }}/platform">
@endsection

@section('content')

<!-- PAGE HERO -->
<div class="page-hero" style="background-image:url('{{ asset('images/pages/platform-bg.jpg') }}')">
  <div class="container" style="position:relative;z-index:2;">
    <p class="eyebrow">Digital Platform</p>
    <h1 class="display">CredibilityIQ: Measure. Diagnose. Grow.</h1>
    <p class="page-hero-lead">Our proprietary online platform translates the Credibility Life Cycle&reg; and Corporate Credibility Scorecard into a live, data-driven digital experience — giving organisations real-time insight into their credibility position.</p>
  </div>
</div>

<!-- PLATFORM FULL -->
<section class="section ciq-section" id="platform">
  <div class="container">
    <div class="ciq-inner">
      <div>
        <div class="ciq-eyebrow">Digital Platform</div>
        <h2 class="display">CredibilityIQ: The Digital Assessment Platform</h2>
        <p class="ciq-lead">CredibilityIQ is our proprietary online platform that translates the Credibility Life Cycle&reg; and Corporate Credibility Scorecard into a live, data-driven digital experience, giving organisations real-time insight into their credibility position across all 12 Building Blocks.</p>
        <div class="ciq-features">
          <div class="ciq-feat"><div class="ciq-feat-num">01</div><div><div class="ciq-feat-title">Live Credibility Scoring</div><div class="ciq-feat-desc">Assessments are scored in real time across all 12 Building Blocks with automatic CLC band classification and weighted financial leakage analysis.</div></div></div>
          <div class="ciq-feat"><div class="ciq-feat-num">02</div><div><div class="ciq-feat-title">Anonymous Survey Distribution</div><div class="ciq-feat-desc">Unique survey tokens are generated per assessment and distributed to respondents, fully anonymous, no login required, ensuring honest, unfiltered feedback.</div></div></div>
          <div class="ciq-feat"><div class="ciq-feat-num">03</div><div><div class="ciq-feat-title">Automated PDF Scorecard Reports</div><div class="ciq-feat-desc">One-click report generation produces a fully detailed A4 PDF including narrative, priority action plan, financial leakage estimates, and recovery potential.</div></div></div>
          <div class="ciq-feat"><div class="ciq-feat-num">04</div><div><div class="ciq-feat-title">Secure Multi-Company Management</div><div class="ciq-feat-desc">Superadmin oversight with individual company workspaces, user management, assessment slot controls, and a 24-hour SLA support ticket system.</div></div></div>
        </div>
        <a href="{{ route('login') }}" class="btn-primary" style="display:inline-flex;">Access the Platform</a>
      </div>
      <div>
        <p style="font-size:0.75rem;font-weight:700;letter-spacing:0.15em;text-transform:uppercase;color:rgba(255,255,255,0.38);margin-bottom:16px;">CLC Band Classification</p>
        <div class="ciq-bands">
          <div class="band-card" style="background:rgba(5,150,105,0.15);border-color:rgba(5,150,105,0.3);"><div class="band-label">Band CC</div><div class="band-name" style="color:#34D399;">Celebrity Credibility</div><div class="band-range">Score: 90–100%</div></div>
          <div class="band-card" style="background:rgba(202,138,4,0.15);border-color:rgba(202,138,4,0.3);"><div class="band-label">Band GW</div><div class="band-name" style="color:#FCD34D;">General Ward</div><div class="band-range">Score: 66–89%</div></div>
          <div class="band-card" style="background:rgba(234,88,12,0.15);border-color:rgba(234,88,12,0.3);"><div class="band-label">Band HDU</div><div class="band-name" style="color:#FB923C;">High Dependency Unit</div><div class="band-range">Score: 51–65%</div></div>
          <div class="band-card" style="background:rgba(220,38,38,0.15);border-color:rgba(220,38,38,0.3);"><div class="band-label">Band ICU</div><div class="band-name" style="color:#F87171;">Intensive Care Unit</div><div class="band-range">Score: 0–50%</div></div>
        </div>
        <div class="ciq-caps">
          <p style="font-size:0.73rem;font-weight:700;letter-spacing:0.12em;text-transform:uppercase;color:rgba(255,255,255,0.38);margin-bottom:12px;">Platform Capabilities</p>
          <div class="ciq-cap-row"><span>Financial leakage analysis</span><span>Included</span></div>
          <div class="ciq-cap-row"><span>Training recommendations per value</span><span>Included</span></div>
          <div class="ciq-cap-row"><span>Recovery potential calculation</span><span>Included</span></div>
          <div class="ciq-cap-row"><span>Auto-generated narrative report</span><span>Included</span></div>
          <div class="ciq-cap-row"><span>Priority action plan</span><span>Included</span></div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- BOTTOM CTA -->
<section class="cta-section" style="position:relative;overflow:hidden;">
  <div class="cta-orb cta-orb-1"></div>
  <div class="cta-orb cta-orb-2"></div>
  <div class="container" style="position:relative;z-index:1;">
    <p class="eyebrow" style="color:rgba(255,255,255,0.6);display:flex;justify-content:center;margin-bottom:16px;">Get Instant Credibility Insight</p>
    <h2 class="display">Start Measuring Your Organisation's Credibility Today</h2>
    <p>Access the CredibilityIQ platform to run your first assessment and get a data-driven scorecard with actionable recommendations.</p>
    <div class="cta-btns">
      <a href="{{ route('login') }}" class="btn-primary">Access the Platform</a>
      <a href="{{ route('contact') }}" class="btn-ghost-white">Request a Demo</a>
    </div>
  </div>
</section>

@endsection
