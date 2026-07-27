@extends('layouts.public')

@section('title', 'About Us | Credibility Factory Afrique')

@section('meta')
<meta name="description" content="Learn about Credibility Factory Afrique — Africa's only organisation that has developed a proprietary scientific framework for measuring, diagnosing, and growing credibility in people, teams, and institutions.">
<link rel="canonical" href="{{ config('app.url') }}/about">
@endsection

@section('content')

<!-- PAGE HERO -->
<div class="page-hero" style="background-image:url('{{ asset('images/pages/about-bg.jpg') }}')">
  <div class="container" style="position:relative;z-index:2;">
    <p class="eyebrow">Who We Are</p>
    <h1 class="display">Credibility Cannot Be Bought. It Must Be Earned.</h1>
  </div>
</div>

<!-- ABOUT FULL -->
<section class="section" id="about">
  <div class="container">
    <div class="about-grid">
      <div class="reveal-left">
        <p class="eyebrow">Our Story</p>
        <h2 class="display" style="margin-top:12px;margin-bottom:24px;">Africa's Only Credibility Science Firm</h2>
        <p class="lead">At Credibility Factory Afrique, we believe credibility is the highest form of personal and organisational currency, one that cannot be faked repeatedly, cannot be bought off the shelf, and must be earned, nurtured, and relentlessly protected.</p>
        <div class="about-quote">
          <blockquote>"We prepare and inspire individuals and organisations of any age and size to offer products and services that are humane, accessible, inclusive, and resilient, by deploying credibility values across their processes and value chains."</blockquote>
        </div>
        <p style="font-size:0.88rem;color:var(--text-muted);margin-bottom:28px;">We work with executives, government departments, parastatals, private enterprises, NGOs, and academic institutions across Zimbabwe and Eastern Africa, equipping them with the frameworks, skills, and evidence base to build lasting credibility at every level of their operations.</p>
        <div class="about-pillars">
          <div class="pillar">Individuals</div><div class="pillar">Families</div><div class="pillar">Communities</div>
          <div class="pillar">Organisations</div><div class="pillar">Governments</div><div class="pillar">Businesses</div>
        </div>
      </div>
      <div class="reveal-right">
        <div class="about-card">
          <div class="about-card-wm">CFA</div>
          <div class="about-card-eyebrow">Our Mission</div>
          <h3>Deploying Credibility Values Across Every Value Chain</h3>
          <p>Founded by Christopher Sithole-Kushata, the pioneering voice on Personal Credibility and Institutional Integrity in Zimbabwe and Southern Africa, Credibility Factory Afrique operates at the intersection of strategy, culture, integrity, and human behaviour.</p>
          <p>We are the only organisation on the continent that has built a proprietary scientific framework for measuring, diagnosing, and growing credibility, and turned it into a suite of training programmes, facilitation services, and digital tools.</p>
          <div class="about-tags">
            <span class="tag">Zimbabwe</span><span class="tag">Kenya</span><span class="tag">Tanzania</span><span class="tag">Southern Africa</span><span class="tag">East Africa</span>
          </div>
        </div>
        <div class="badge-row">
          <div class="badge">
            <div class="badge-mark">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--accent)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 016.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 014 19.5v-15A2.5 2.5 0 016.5 2z"/></svg>
            </div>
            <div class="badge-text"><strong>"The Credibility Manifesto Handbook"</strong><span>How to 12X Your Influence on Humanity</span></div>
          </div>
          <div class="badge">
            <div class="badge-mark">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--accent)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>
            </div>
            <div class="badge-text"><strong>Guest Lecturer, UZ &amp; WUA</strong><span>MBA Programmes since 2023</span></div>
          </div>
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
    <p class="eyebrow" style="color:rgba(255,255,255,0.6);display:flex;justify-content:center;margin-bottom:16px;">Start Your Credibility Journey</p>
    <h2 class="display">Ready to Build a More Credible Organisation?</h2>
    <p>Our team is ready to discuss your needs and recommend the right programme for your context. Reach out — we respond within 24 hours.</p>
    <div class="cta-btns">
      <a href="{{ route('contact') }}" class="btn-white">Get in Touch</a>
      <a href="{{ route('services') }}" class="btn-ghost-white">Explore Our Services</a>
    </div>
  </div>
</section>

@endsection
