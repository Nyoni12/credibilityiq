@extends('layouts.public')

@section('title', 'Our Services | Credibility Factory Afrique')

@section('meta')
<meta name="description" content="Explore our credibility training and consulting services — executive clinics, strategy and values alignment, culture change, business integrity workshops, and more. Serving Zimbabwe, Kenya and Tanzania.">
<link rel="canonical" href="{{ config('app.url') }}/services">
@endsection

@section('content')

<!-- PAGE HERO -->
<div class="page-hero" style="background-image:url('{{ asset('images/pages/services-bg.jpg') }}')">
  <div class="container" style="position:relative;z-index:2;">
    <p class="eyebrow">What We Offer</p>
    <h1 class="display">It's a Journey, Not a One-Day Workshop</h1>
    <p class="page-hero-lead">Our programmes range from executive clinics to full-scale culture transformation engagements. Every service is customised to your organisation's context and underpinned by our proprietary frameworks.</p>
  </div>
</div>

<!-- SERVICES FULL -->
<section class="section services-section" id="services">
  <div class="container">
    <div class="services-grid">
      <div class="svc-card reveal"><div class="svc-header"><div class="svc-badge">01</div><div><div class="svc-num">Service 01</div><div class="svc-title">Credibility Clinics for Executives</div></div></div><div class="svc-body"><p class="svc-desc">Designed specifically for senior leaders and C-suite executives, this intensive clinic sharpens personal performance, organisational influence, market share, and profitability, all anchored in the 6 Corporate Credibility Imperatives&reg;. Executives work through the Personal Credibility Scorecard and the Corporate Credibility Scorecard to understand precisely where they and their organisation sit on the Credibility Life Cycle.</p><ul class="svc-outcomes"><li>Executives leave with a quantified Personal Credibility Score and a clear gap analysis</li><li>Leaders gain the tools to execute strategy using internally generated credibility evidence</li><li>Improved organisational influence, stakeholder trust, and competitive positioning</li><li>Practical action plans to advance along the Credibility Life Cycle</li></ul><span class="svc-for">For: Senior Executives &amp; C-Suite Leaders</span></div></div>
      <div class="svc-card reveal reveal-delay-1"><div class="svc-header"><div class="svc-badge">02</div><div><div class="svc-num">Service 02</div><div class="svc-title">Corporate Strategy — Values Alignment Sessions</div></div></div><div class="svc-body"><p class="svc-desc">Designed to kick-start or reset strategy formulation, this programme addresses one of the most pervasive failures in African organisations: the disconnect between stated strategy and lived values. Using the Credibility Flywheel&reg; Framework, we map the gaps between your Strategy Pillars and your company's actual values, then provide a practical roadmap to close those execution slippages and position your organisation for unfair competitive advantage.</p><ul class="svc-outcomes"><li>A clear visual map of Strategy-Values gaps specific to your organisation</li><li>A practical, sequenced action plan to close strategy-execution slippages</li><li>Leadership alignment around a shared credibility-based strategy narrative</li><li>Monetised Strategy-Values link for sustainable market differentiation</li></ul><span class="svc-for">For: Executive Teams, Strategy Committees &amp; Boards</span></div></div>
      <div class="svc-card reveal"><div class="svc-header"><div class="svc-badge">03</div><div><div class="svc-num">Service 03</div><div class="svc-title">Culture Change Training Sessions</div></div></div><div class="svc-body"><p class="svc-desc">For organisations in motion, undergoing restructuring, rapid growth, merger activity, or a deliberate cultural reset, this programme introduces and deepens a desired culture shift, anchored on the Credibility Life-Cycle&reg; Framework. Both the Flywheel&reg; and the Life Cycle&reg; models are deployed to identify practical steps that close execution slippages and align strategic pillars with employee and stakeholder expectations.</p><ul class="svc-outcomes"><li>A diagnosed current-state culture map using the Credibility Life Cycle&reg;</li><li>A co-created target culture profile aligned to strategy and values</li><li>Measurable culture KPIs tied to Scalability, Sustainability, and Profitability</li><li>Embedded change champions equipped to sustain the culture shift</li></ul><span class="svc-for">For: All Staff Levels, HR-led, Board-endorsed</span></div></div>
      <div class="svc-card reveal reveal-delay-1"><div class="svc-header"><div class="svc-badge">04</div><div><div class="svc-num">Service 04</div><div class="svc-title">Business Integrity Workshops</div></div></div><div class="svc-body"><p class="svc-desc">A practical, engagement-driven workshop that equips staff and leadership with the knowledge, judgement, and tools to navigate the complex terrain of anti-corruption, anti-bribery, and ethical decision-making. Using real-world dilemmas drawn from the Zimbabwean and African business context, the workshop moves beyond compliance box-ticking toward a genuine commitment to organisational integrity.</p><ul class="svc-outcomes"><li>Comprehensive understanding of anti-corruption legislation and organisational liability</li><li>A clear, accessible anti-bribery policy and implementation roadmap</li><li>Staff equipped to identify and respond to bribery and fraud scenarios</li><li>Ethical decision-making frameworks embedded in day-to-day operations</li></ul><span class="svc-for">For: All Staff, Compliance Teams &amp; Executives</span></div></div>
      <div class="svc-card reveal"><div class="svc-header"><div class="svc-badge">05</div><div><div class="svc-num">Service 05</div><div class="svc-title">Strategy Facilitation &amp; Team Building</div></div></div><div class="svc-body"><p class="svc-desc">Ad-hoc and tailor-made strategy facilitation and team building programmes that promote cohesion, mutual respect, and a shared sense of purpose across functional areas. Whether you need to facilitate an annual strategy retreat, resolve cross-functional tension, or build the psychological safety needed for high-performance teams, these programmes are designed around your specific organisational context and culture.</p><ul class="svc-outcomes"><li>A clearly articulated strategic direction that all team members can own</li><li>Strengthened cross-functional relationships and communication channels</li><li>Mutual respect frameworks embedded in team operating norms</li><li>A post-workshop action plan with named owners and timelines</li></ul><span class="svc-for">For: Leadership Teams, Departments &amp; Cross-Functional Groups</span></div></div>
      <div class="svc-card reveal reveal-delay-1"><div class="svc-header"><div class="svc-badge">06</div><div><div class="svc-num">Service 06</div><div class="svc-title">Customer Service Experience Surveys &amp; Trainings</div></div></div><div class="svc-body"><p class="svc-desc">Beginning with a rigorous survey of your current customer service landscape, internal stakeholders, customer touchpoints, and service delivery gaps, we then design and deliver a bespoke training programme that addresses the identified gaps with precision. The goal is to create a customer service culture that disrupts the competition, deepens external partnerships, and directly contributes to scalability, sustainability, and profitability.</p><ul class="svc-outcomes"><li>A detailed Customer Service Experience Gap Report with actionable recommendations</li><li>A bespoke training programme tailored to your specific service context</li><li>Staff equipped with both the mindset and the tools to deliver exceptional service</li><li>Measurable improvement in customer retention, satisfaction, and referral rates</li></ul><span class="svc-for">For: Customer-Facing Teams, Service Managers &amp; Frontline Staff</span></div></div>
    </div>
  </div>
</section>

<!-- BOTTOM CTA -->
<section class="cta-section" style="position:relative;overflow:hidden;">
  <div class="cta-orb cta-orb-1"></div>
  <div class="cta-orb cta-orb-2"></div>
  <div class="container" style="position:relative;z-index:1;">
    <p class="eyebrow" style="color:rgba(255,255,255,0.6);display:flex;justify-content:center;margin-bottom:16px;">Ready to Get Started?</p>
    <h2 class="display">Find the Right Programme for Your Organisation</h2>
    <p>Tell us about your context and we will recommend the programme that fits your stage, sector, and goals. We respond within 24 hours.</p>
    <div class="cta-btns">
      <a href="{{ route('contact') }}" class="btn-white">Book a Consultation</a>
      <a href="{{ route('frameworks') }}" class="btn-ghost-white">Explore Our Frameworks</a>
    </div>
  </div>
</section>

@endsection
