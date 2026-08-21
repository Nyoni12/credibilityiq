@extends('layouts.public')

@section('title', 'Credibility Factory Afrique | Credibility Training & Consulting - Zimbabwe & Africa')

@section('meta')
<meta name="description" content="Africa's only firm specialising in credibility science. Executive clinics, corporate culture training, strategy facilitation, and the CredibilityIQ digital assessment platform. Serving Zimbabwe, Kenya and Tanzania.">
<meta name="keywords" content="credibility training Zimbabwe, credibility consulting Africa, executive leadership Zimbabwe, corporate culture change, business integrity training, credibility assessment, CredibilityIQ platform, Christopher Sithole-Kushata, leadership development Harare, organisational credibility, strategy facilitation Zimbabwe, culture transformation Africa">
<meta name="author" content="Credibility Factory Afrique">
<meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
<link rel="canonical" href="{{ config('app.url') }}">
<meta name="geo.region" content="ZW-HA">
<meta name="geo.placename" content="Harare, Zimbabwe">
<meta name="geo.position" content="-17.8252;31.0335">
<meta name="ICBM" content="-17.8252, 31.0335">
<meta property="og:type" content="website">
<meta property="og:url" content="{{ config('app.url') }}">
<meta property="og:site_name" content="Credibility Factory Afrique">
<meta property="og:title" content="Credibility Factory Afrique — Africa's Authority on Credibility">
<meta property="og:description" content="The only organisation in Africa that has mastered the art and science of credibility. Executive clinics, corporate training, culture transformation and the CredibilityIQ digital platform — Zimbabwe, Kenya &amp; Tanzania.">
<meta property="og:image" content="{{ asset('images/hero-bg.jpg') }}">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
<meta property="og:image:alt" content="Credibility Factory Afrique — Building Credible People, Teams and Institutions Across Africa">
<meta property="og:locale" content="en_ZW">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="Credibility Factory Afrique — Africa's Authority on Credibility">
<meta name="twitter:description" content="Africa's only credibility science firm. Executive clinics, corporate training, culture change &amp; the CredibilityIQ digital platform — Zimbabwe, Kenya &amp; Tanzania.">
<meta name="twitter:image" content="{{ asset('images/hero-bg.jpg') }}">
<meta name="twitter:image:alt" content="Credibility Factory Afrique">
@endsection

@section('preload')
<link rel="preload" as="image" href="{{ asset('images/hero-bg.jpg') }}" fetchpriority="high">
@endsection

@section('schema')
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@graph": [
    {
      "@type": "Organization",
      "@id": "{{ config('app.url') }}/#organization",
      "name": "Credibility Factory Afrique",
      "alternateName": ["CFA", "Credibility Factory"],
      "url": "{{ config('app.url') }}",
      "logo": {
        "@type": "ImageObject",
        "url": "{{ asset('images/logo.png') }}",
        "width": 300,
        "height": 90
      },
      "description": "Africa's only organisation that has developed and mastered the art and science of credibility — offering executive coaching, corporate training, culture transformation workshops, and the CredibilityIQ digital assessment platform.",
      "foundingDate": "2019",
      "founder": {
        "@type": "Person",
        "@id": "{{ config('app.url') }}/#founder",
        "name": "Christopher Sithole-Kushata",
        "jobTitle": "Founder & Chief Credibility Officer",
        "description": "Pioneering voice on Personal Credibility and Institutional Integrity in Zimbabwe and Southern Africa. Author of The Credibility Manifesto Handbook.",
        "worksFor": {"@id": "{{ config('app.url') }}/#organization"}
      },
      "address": {
        "@type": "PostalAddress",
        "streetAddress": "354 Chelternham Way, Melfort Park",
        "addressLocality": "Harare",
        "addressCountry": "ZW"
      },
      "contactPoint": [
        {
          "@type": "ContactPoint",
          "telephone": "+263-242-006-784",
          "contactType": "customer service",
          "areaServed": ["ZW", "KE", "TZ"],
          "availableLanguage": "English"
        },
        {
          "@type": "ContactPoint",
          "telephone": "+263-718-584-946",
          "contactType": "sales",
          "areaServed": ["ZW", "KE", "TZ"]
        },
        {
          "@type": "ContactPoint",
          "email": "info@credibilityfactory.net",
          "contactType": "customer service"
        }
      ],
      "areaServed": [
        {"@type": "Country", "name": "Zimbabwe"},
        {"@type": "Country", "name": "Kenya"},
        {"@type": "Country", "name": "Tanzania"}
      ],
      "knowsAbout": ["Credibility", "Corporate Training", "Executive Leadership", "Organisational Culture", "Business Integrity", "Strategy Facilitation", "Culture Change"],
      "hasOfferCatalog": {
        "@type": "OfferCatalog",
        "name": "Credibility Training & Consulting Services",
        "itemListElement": [
          {"@type": "Offer", "itemOffered": {"@type": "Service", "name": "Credibility Clinics for Executives", "description": "Personalised executive credibility assessment and coaching sessions."}},
          {"@type": "Offer", "itemOffered": {"@type": "Service", "name": "Corporate Strategy & Values Alignment", "description": "Facilitated strategy and values alignment workshops for leadership teams."}},
          {"@type": "Offer", "itemOffered": {"@type": "Service", "name": "Culture Change Training", "description": "Evidence-based organisational culture transformation programmes."}},
          {"@type": "Offer", "itemOffered": {"@type": "Service", "name": "Business Integrity Workshop", "description": "Workshops that embed integrity and ethical practice into business culture."}},
          {"@type": "Offer", "itemOffered": {"@type": "Service", "name": "Customer Service Surveys & Training", "description": "Credibility-driven customer service assessment and improvement programmes."}},
          {"@type": "Offer", "itemOffered": {"@type": "Service", "name": "CredibilityIQ Platform", "description": "Digital credibility assessment and scorecard platform built on the 12 CFA Building Blocks framework."}}
        ]
      }
    },
    {
      "@type": "WebSite",
      "@id": "{{ config('app.url') }}/#website",
      "url": "{{ config('app.url') }}",
      "name": "Credibility Factory Afrique",
      "description": "Africa's authority on credibility — training, consulting, and the CredibilityIQ digital assessment platform.",
      "publisher": {"@id": "{{ config('app.url') }}/#organization"}
    },
    {
      "@type": "WebPage",
      "@id": "{{ config('app.url') }}/#webpage",
      "url": "{{ config('app.url') }}",
      "name": "Credibility Factory Afrique | Credibility Training & Consulting — Zimbabwe & Africa",
      "isPartOf": {"@id": "{{ config('app.url') }}/#website"},
      "about": {"@id": "{{ config('app.url') }}/#organization"},
      "description": "Africa's only credibility science firm. Executive clinics, corporate training, culture transformation and CredibilityIQ digital platform — Zimbabwe, Kenya & Tanzania."
    },
    {
      "@type": "ProfessionalService",
      "@id": "{{ config('app.url') }}/#localbusiness",
      "name": "Credibility Factory Afrique",
      "image": "{{ asset('images/logo.png') }}",
      "url": "{{ config('app.url') }}",
      "telephone": "+263242006784",
      "email": "info@credibilityfactory.net",
      "address": {
        "@type": "PostalAddress",
        "streetAddress": "354 Chelternham Way, Melfort Park",
        "addressLocality": "Harare",
        "addressRegion": "Harare Province",
        "addressCountry": "ZW"
      },
      "geo": {
        "@type": "GeoCoordinates",
        "latitude": -17.8252,
        "longitude": 31.0335
      },
      "openingHours": "Mo-Fr 08:00-17:00",
      "priceRange": "$$",
      "areaServed": ["Zimbabwe", "Kenya", "Tanzania"],
      "serviceType": ["Corporate Training", "Executive Coaching", "Strategy Facilitation", "Credibility Assessment", "Culture Change"]
    },
    {
      "@type": "Book",
      "name": "The Credibility Manifesto Handbook: How to 12X Your Influence on Humanity",
      "author": {"@id": "{{ config('app.url') }}/#founder"},
      "publisher": {"@id": "{{ config('app.url') }}/#organization"},
      "description": "A comprehensive guide to building personal credibility and multiplying influence — by Christopher Sithole-Kushata, founder of Credibility Factory Afrique."
    },
    {
      "@type": "FAQPage",
      "mainEntity": [
        {
          "@type": "Question",
          "name": "What is Credibility Factory Afrique?",
          "acceptedAnswer": {"@type": "Answer", "text": "Credibility Factory Afrique is the only organisation in Zimbabwe and on the African continent that has developed a proprietary scientific framework for measuring, diagnosing, and growing credibility in people, teams, and institutions."}
        },
        {
          "@type": "Question",
          "name": "What countries does Credibility Factory Afrique serve?",
          "acceptedAnswer": {"@type": "Answer", "text": "We serve clients in Zimbabwe, Kenya, and Tanzania, with programmes for government departments, parastatals, private enterprises, NGOs, and academic institutions."}
        },
        {
          "@type": "Question",
          "name": "What is the CredibilityIQ platform?",
          "acceptedAnswer": {"@type": "Answer", "text": "CredibilityIQ is a digital assessment and scorecard platform built on the 12 CFA Credibility Building Blocks. It measures organisational credibility, identifies financial leakage, and generates priority action plans for improvement."}
        },
        {
          "@type": "Question",
          "name": "What are the 12 CFA Credibility Building Blocks?",
          "acceptedAnswer": {"@type": "Answer", "text": "The 12 Building Blocks are: Honesty Without Offense, Trustworthiness, Accountability, Responsibility, Hard Work, Fairness, Empathy, Humility, Respect, Reputation, Integrity, and Teamwork — the non-negotiable pillars of credible people and organisations."}
        },
        {
          "@type": "Question",
          "name": "How do I book a credibility training session?",
          "acceptedAnswer": {"@type": "Answer", "text": "Contact us at info@credibilityfactory.net, call +263-242-006-784, or complete the enquiry form on our website. We respond within 24 hours."}
        }
      ]
    }
  ]
}
</script>
@endsection

@section('content')

<!-- HERO -->
<section class="hero" id="home">
  <div class="hero-inner-new">
    <div class="hero-kicker"><span class="hero-kicker-rule"></span>Africa's Authority on Credibility</div>
    <div class="hero-headline-row">
      <h1 class="display hero-h1">The Strongest<br>Currency You<br>Can Own Is<br><em class="gt">Your Credibility.</em></h1>
      <div class="hero-side-panel">
        <p class="hero-lead">Credibility Factory Afrique is the only organisation in Zimbabwe and on the African continent that has developed and mastered the art and science of credibility. We build credible people, credible teams, and credible institutions.</p>
        <div class="hero-actions">
          <a href="{{ route('services') }}" class="btn-primary">Explore Our Services</a>
          <a href="{{ route('contact') }}" class="btn-outline">Book a Consultation</a>
        </div>
      </div>
    </div>
    <div class="hero-strip">
      <div class="hero-strip-item"><span class="hero-strip-num">35+</span><span class="hero-strip-label">Years collective experience</span></div>
      <div class="hero-strip-item"><span class="hero-strip-num">ZW · KE · TZ</span><span class="hero-strip-label">Countries we serve</span></div>
      <div class="hero-strip-item"><span class="hero-strip-num">12</span><span class="hero-strip-label">Credibility building blocks</span></div>
      <div class="hero-strip-item"><span class="hero-strip-num">6</span><span class="hero-strip-label">Proprietary programmes</span></div>
    </div>
  </div>
</section>

<!-- ABOUT TEASER -->
<section class="section" id="about-teaser">
  <div class="container">
    <div class="about-grid">
      <div class="reveal-left">
        <p class="eyebrow">Who We Are</p>
        <h2 class="display" style="margin-top:12px;margin-bottom:24px;">Credibility Cannot Be Bought. It Must Be Earned.</h2>
        <p class="lead">At Credibility Factory Afrique, we believe credibility is the highest form of personal and organisational currency. It cannot be faked repeatedly, cannot be bought off the shelf, and must be earned, nurtured, and relentlessly protected. We work with executives, government departments, parastatals, private enterprises, NGOs, and academic institutions across Zimbabwe and Eastern Africa.</p>
        <a href="{{ route('about') }}" class="btn-primary" style="margin-top:32px;display:inline-flex;">Learn More &rarr;</a>
      </div>
      <div class="reveal-right">
        <div class="about-card">
          <div class="about-card-eyebrow">Our Mission</div>
          <h3>Deploying Credibility Values Across Every Value Chain</h3>
          <p>Founded by Christopher Sithole-Kushata, the pioneering voice on Personal Credibility and Institutional Integrity in Zimbabwe and Southern Africa, Credibility Factory Afrique operates at the intersection of strategy, culture, integrity, and human behaviour.</p>
          <p>We are the only organisation on the continent that has built a proprietary scientific framework for measuring, diagnosing, and growing credibility, and turned it into a suite of training programmes, facilitation services, and digital tools.</p>
          <div class="about-tags">
            <span class="tag">Zimbabwe</span><span class="tag">Kenya</span><span class="tag">Tanzania</span><span class="tag">Southern Africa</span><span class="tag">East Africa</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- BUILDING BLOCKS -->
<section class="section blocks-section" id="building-blocks">
  <div class="container">
    <div class="blocks-manifesto">
      <div class="reveal-left">
        <p class="eyebrow">The Foundation</p>
        <h2 class="display" style="margin-top:14px;color:white;line-height:1.08;">The 12 CFA<br>Credibility<br>Building Blocks</h2>
        <p style="color:rgba(255,255,255,0.42);margin-top:20px;font-size:0.88rem;line-height:1.8;">Every training, workshop, and scorecard we deliver is anchored in these twelve non-negotiable pillars of credible people and credible organisations.</p>
        <a href="{{ route('frameworks') }}" class="btn-outline" style="margin-top:32px;display:inline-flex;">Our Frameworks &rarr;</a>
      </div>
      <div>
        <div class="block-row reveal"><span class="block-row-num">01</span><span class="block-row-name">Honesty Without Offense</span></div>
        <div class="block-row reveal"><span class="block-row-num">02</span><span class="block-row-name">Trustworthiness</span></div>
        <div class="block-row reveal"><span class="block-row-num">03</span><span class="block-row-name">Accountability</span></div>
        <div class="block-row reveal"><span class="block-row-num">04</span><span class="block-row-name">Responsibility</span></div>
        <div class="block-row reveal"><span class="block-row-num">05</span><span class="block-row-name">Hard Work</span></div>
        <div class="block-row reveal"><span class="block-row-num">06</span><span class="block-row-name">Fairness</span></div>
        <div class="block-row reveal"><span class="block-row-num">07</span><span class="block-row-name">Empathy</span></div>
        <div class="block-row reveal"><span class="block-row-num">08</span><span class="block-row-name">Humility</span></div>
        <div class="block-row reveal"><span class="block-row-num">09</span><span class="block-row-name">Respect for Self &amp; Others</span></div>
        <div class="block-row reveal"><span class="block-row-num">10</span><span class="block-row-name">Reputation</span></div>
        <div class="block-row reveal"><span class="block-row-num">11</span><span class="block-row-name">Integrity</span></div>
        <div class="block-row reveal"><span class="block-row-num">12</span><span class="block-row-name">Teamwork</span></div>
      </div>
    </div>
  </div>
</section>

<!-- SERVICES -->
<section class="section services-section" id="services-teaser">
  <div class="container">
    <div class="services-intro">
      <p class="eyebrow reveal">What We Offer</p>
      <h2 class="display reveal" style="margin-top:12px;">It's a Journey, Not<br>a One-Day Workshop</h2>
      <p class="lead reveal" style="margin-top:16px;max-width:560px;">Our programmes range from executive clinics to full-scale culture transformation engagements, every one customised to your organisation's context.</p>
    </div>
    <div class="svc-list">
      <div class="svc-list-item reveal">
        <span class="svc-list-num">01</span>
        <div>
          <div class="svc-list-title">Credibility Clinics for Executives</div>
          <p class="svc-list-desc">Intensive clinics for C-suite and senior leaders, working through the Personal and Corporate Credibility Scorecards anchored in the 6 Corporate Credibility Imperatives®. Executives leave with a quantified score, a gap analysis, and a clear action plan.</p>
          <span class="svc-list-for">For: Senior Executives &amp; C-Suite Leaders</span>
        </div>
        <a href="{{ route('services') }}" class="svc-list-link">Details &rarr;</a>
      </div>
      <div class="svc-list-item reveal reveal-delay-1">
        <span class="svc-list-num">02</span>
        <div>
          <div class="svc-list-title">Corporate Strategy: Values Alignment</div>
          <p class="svc-list-desc">Using the Credibility Flywheel® Framework to map and close the gap between stated strategy and lived values, positioning your organisation for unfair competitive advantage through measurable credibility evidence.</p>
          <span class="svc-list-for">For: Executive Teams, Strategy Committees &amp; Boards</span>
        </div>
        <a href="{{ route('services') }}" class="svc-list-link">Details &rarr;</a>
      </div>
      <div class="svc-list-item reveal reveal-delay-2">
        <span class="svc-list-num">03</span>
        <div>
          <div class="svc-list-title">Culture Change Training Sessions</div>
          <p class="svc-list-desc">For organisations undergoing restructuring, growth, or merger activity, embedding a deliberate culture shift anchored on the Credibility Life-Cycle® and Flywheel® models, with measurable KPIs tied to scalability and profitability.</p>
          <span class="svc-list-for">For: All Staff Levels, HR-led, Board-endorsed</span>
        </div>
        <a href="{{ route('services') }}" class="svc-list-link">Details &rarr;</a>
      </div>
    </div>
    <div style="margin-top:48px;">
      <a href="{{ route('services') }}" class="btn-primary">View All Services &rarr;</a>
    </div>
  </div>
</section>

<!-- CTA -->
<section class="cta-section">
  <div class="container" style="position:relative;z-index:1;">
    <p class="eyebrow" style="color:rgba(255,255,255,0.5);display:flex;justify-content:center;margin-bottom:16px;">Start Your Credibility Journey</p>
    <h2 class="display">Ready to Build the Most<br>Valuable Currency You Own?</h2>
    <p>Whether you're an executive seeking a personal credibility clinic, an organisation undergoing culture change, or a team that needs its strategy and values aligned, we have a programme built for you.</p>
    <div class="cta-btns">
      <a href="{{ route('contact') }}" class="btn-white">Book a Consultation</a>
      <a href="{{ route('services') }}" class="btn-ghost-white">Explore Our Services</a>
    </div>
  </div>
</section>

@endsection
