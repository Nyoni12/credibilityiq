@extends('layouts.public')

@section('title', 'Our Frameworks | Credibility Factory Afrique')

@section('meta')
<meta name="description" content="Discover Credibility Factory Afrique's proprietary frameworks — the Credibility Life Cycle, Credibility Flywheel, Personal and Corporate Credibility Scorecards, 6 Corporate Credibility Imperatives, and more.">
<link rel="canonical" href="{{ config('app.url') }}/frameworks">
@endsection

@section('content')

<!-- PAGE HERO -->
<div class="page-hero" style="background-image:url('{{ asset('images/pages/frameworks-bg.jpg') }}')">
  <div class="container" style="position:relative;z-index:2;">
    <p class="eyebrow">Proprietary Frameworks</p>
    <h1 class="display">Battle-Tested Tools Built for African Organisations</h1>
    <p class="page-hero-lead">These are not off-the-shelf methodologies. Every framework was developed, tested, and refined by our team across hundreds of engagements in Zimbabwe and across the continent.</p>
  </div>
</div>

<!-- FRAMEWORKS FULL -->
<section class="section frameworks-section" id="frameworks">
  <div class="container">
    <div class="fw-grid">
      <div class="fw-card reveal">
        <div class="fw-mark"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="var(--accent)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 4 23 10 17 10"/><polyline points="1 20 1 14 7 14"/><path d="M3.51 9a9 9 0 0114.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0020.49 15"/></svg></div>
        <h3>The Credibility Life Cycle&reg;</h3>
        <p>A diagnostic framework that maps where individuals and organisations currently sit on the credibility spectrum, from Credibility Deficit to Celebrity Credibility. It reveals the specific interventions needed to advance along the cycle, and underpins every scorecard and training programme we deliver.</p>
        <span class="fw-label">Core diagnostic tool</span>
      </div>
      <div class="fw-card reveal reveal-delay-1">
        <div class="fw-mark"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="var(--accent)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M12 1v4M12 19v4M4.22 4.22l2.83 2.83M16.95 16.95l2.83 2.83M1 12h4M19 12h4M4.22 19.78l2.83-2.83M16.95 7.05l2.83-2.83"/></svg></div>
        <h3>The Credibility Flywheel&reg;</h3>
        <p>A strategy-execution framework designed to humanise the nexus between an organisation's Strategic Pillars and its Corporate Values. It identifies the gaps causing strategy-execution slippages and shows the precise steps to close them, monetising the Strategy-Values link for sustainable competitive advantage.</p>
        <span class="fw-label">Strategy alignment tool</span>
      </div>
      <div class="fw-card reveal reveal-delay-2">
        <div class="fw-mark"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="var(--accent)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg></div>
        <h3>Personal Credibility Scorecard</h3>
        <p>An evidence-based individual assessment instrument that gives executives and leaders a precise, quantified picture of their personal credibility score, broken down across the 12 Building Blocks. The data drives coaching conversations and leadership development plans grounded in reality, not perception.</p>
        <span class="fw-label">Leadership assessment</span>
      </div>
      <div class="fw-card reveal">
        <div class="fw-mark"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="var(--accent)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg></div>
        <h3>Corporate Credibility Scorecard</h3>
        <p>The organisational equivalent of the Personal Scorecard, a structured instrument that produces a credibility score for the entire institution, benchmarked against the 12 Building Blocks and the 6 Corporate Credibility Imperatives&reg;. Results feed directly into strategy and culture change programmes.</p>
        <span class="fw-label">Organisational assessment</span>
      </div>
      <div class="fw-card reveal reveal-delay-1">
        <div class="fw-mark"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="var(--accent)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg></div>
        <h3>6 Corporate Credibility Imperatives&reg;</h3>
        <p>Six non-negotiable organisational imperatives, derived from research across public and private sector organisations in Zimbabwe and Eastern Africa, that define what a credible corporation must demonstrate. These imperatives form the backbone of our executive training programmes and corporate assessments.</p>
        <span class="fw-label">Executive framework</span>
      </div>
      <div class="fw-card reveal reveal-delay-2">
        <div class="fw-mark"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="var(--accent)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg></div>
        <h3>Values Contribution to SSP</h3>
        <p>A measurement model that quantifies how corporate values directly contribute to an organisation's Scalability, Sustainability, and Profitability. This framework transforms abstract value statements into concrete, financially measurable levers, giving leadership teams an evidence-based case for values-led culture change.</p>
        <span class="fw-label">Financial values model</span>
      </div>
    </div>
  </div>
</section>

<!-- BUILDING BLOCKS -->
<section class="section blocks-section" id="building-blocks">
  <div class="blocks-wm">CREDIBILITY</div>
  <div class="container">
    <div class="blocks-header reveal">
      <p class="eyebrow">The Foundation</p>
      <h2 class="display">The 12 CFA Credibility Building Blocks</h2>
      <p style="color:rgba(255,255,255,0.48);max-width:540px;margin:16px auto 0;font-size:0.95rem;">Every training, workshop, and scorecard we deliver is anchored in these twelve non-negotiable pillars of credible people and credible organisations.</p>
    </div>
    <div class="blocks-grid">
      <div class="block-item reveal"><span class="block-num">01</span><span class="block-name">Honesty Without Offense</span></div>
      <div class="block-item reveal reveal-delay-1"><span class="block-num">02</span><span class="block-name">Trustworthiness</span></div>
      <div class="block-item reveal reveal-delay-2"><span class="block-num">03</span><span class="block-name">Accountability</span></div>
      <div class="block-item reveal reveal-delay-3"><span class="block-num">04</span><span class="block-name">Responsibility</span></div>
      <div class="block-item reveal"><span class="block-num">05</span><span class="block-name">Hard Work</span></div>
      <div class="block-item reveal reveal-delay-1"><span class="block-num">06</span><span class="block-name">Fairness</span></div>
      <div class="block-item reveal reveal-delay-2"><span class="block-num">07</span><span class="block-name">Empathy</span></div>
      <div class="block-item reveal reveal-delay-3"><span class="block-num">08</span><span class="block-name">Humility</span></div>
      <div class="block-item reveal"><span class="block-num">09</span><span class="block-name">Respect for Self &amp; Others</span></div>
      <div class="block-item reveal reveal-delay-1"><span class="block-num">10</span><span class="block-name">Reputation</span></div>
      <div class="block-item reveal reveal-delay-2"><span class="block-num">11</span><span class="block-name">Integrity</span></div>
      <div class="block-item reveal reveal-delay-3"><span class="block-num">12</span><span class="block-name">Teamwork</span></div>
    </div>
  </div>
</section>

<!-- BOTTOM CTA -->
<section class="cta-section" style="position:relative;overflow:hidden;">
  <div class="cta-orb cta-orb-1"></div>
  <div class="cta-orb cta-orb-2"></div>
  <div class="container" style="position:relative;z-index:1;">
    <p class="eyebrow" style="color:rgba(255,255,255,0.6);display:flex;justify-content:center;margin-bottom:16px;">Apply These Frameworks to Your Organisation</p>
    <h2 class="display">Ready to Diagnose Your Credibility Position?</h2>
    <p>Our team will walk you through the right framework for your context and help you begin your credibility journey with clarity and confidence.</p>
    <div class="cta-btns">
      <a href="{{ route('contact') }}" class="btn-white">Book a Consultation</a>
      <a href="{{ route('platform') }}" class="btn-ghost-white">Explore CredibilityIQ</a>
    </div>
  </div>
</section>

@endsection
