@extends('layouts.public')

@section('title', 'Our Team | Credibility Factory Afrique')

@section('meta')
<meta name="description" content="Meet the Credibility Factory Afrique executive team — over 35 years of collective leadership experience across Zimbabwe, Southern Africa, and East Africa.">
<link rel="canonical" href="{{ config('app.url') }}/team">
@endsection

@section('content')

<!-- PAGE HERO -->
<div class="page-hero" style="background-image:url('{{ asset('images/pages/team-bg.jpg') }}')">
  <div class="container" style="position:relative;z-index:2;">
    <p class="eyebrow">Our Team</p>
    <h1 class="display">35+ Years of Collective Executive Experience</h1>
    <p class="page-hero-lead">Our executive team has guided and advised parastatals, government departments, private enterprises, and not-for-profit organisations on policy formulation and implementation across Zimbabwe, Southern Africa, and East Africa.</p>
  </div>
</div>

<!-- TEAM FULL -->
<section class="section team-section" id="team">
  <div class="container">
    <div class="team-grid">
      <div class="team-card reveal"><div class="team-avatar">CS</div><div><div class="team-name">Christopher Sithole-Kushata</div><div class="team-role">Founder &amp; Chief Credibility Officer</div><p class="team-bio">The pioneering voice on Personal Credibility and Institutional Integrity in Zimbabwe and Southern Africa. Christopher has over two decades of progressive delivery in Strategy and Leadership Development and Policy Formulation across the financial, NGO, manufacturing, and services sectors. He is the author of <em>"The Credibility Manifesto Handbook: How to 12X Your Influence on Humanity"</em> and Guest Lecturer for MBA classes at UZ Business School and WUA since 2023.</p><div class="team-creds"><span class="cred-tag">MBA, University of Zimbabwe</span><span class="cred-tag">BTHM (Hons), Marketing</span><span class="cred-tag">IAC Certificate in PR</span></div></div></div>
      <div class="team-card reveal reveal-delay-1"><div class="team-avatar">EM</div><div><div class="team-name">Dr. Ezekiel Mtetwa</div><div class="team-role">Director: Research &amp; Development</div><p class="team-bio">With over 20 years of progressive experience in the education and research sectors in Zimbabwe and Northern Europe, Dr. Mtetwa leads the Research and Development arm of Credibility Factory Afrique. He engages cutting-edge, disruptive technologies to deepen the knowledge base and strengthen the interface between communities, commerce, and humanity.</p><div class="team-creds"><span class="cred-tag">PhD, Uppsala University Sweden</span><span class="cred-tag">20+ Years Research Experience</span></div></div></div>
      <div class="team-card reveal"><div class="team-avatar">SK</div><div><div class="team-name">Simbarashe Kushata</div><div class="team-role">Marketing Director</div><p class="team-bio">A highly credentialed marketing professional who supports senior management in developing strategy, identifying the right tools and resources for painless implementation, and driving market visibility. Simbarashe has worked with international organisations including Spar, Epson EU, and Xerox, and combines formal marketing rigour with practical creative execution.</p><div class="team-creds"><span class="cred-tag">BSc Marketing Management, NUST</span><span class="cred-tag">CIM (UK)</span><span class="cred-tag">Diploma Graphic Design</span><span class="cred-tag">Google AdWords Certified</span></div></div></div>
    </div>
  </div>
</section>

<!-- BOTTOM CTA -->
<section class="cta-section">
  <div class="container">
    <p class="eyebrow cta-eyebrow">Work With Our Team</p>
    <h2 class="display">Ready to Engage Africa's<br>Credibility Experts?</h2>
    <p>Reach out to discuss your organisation's needs. Our team is ready to recommend the right programme for your context.</p>
    <div class="cta-btns">
      <a href="{{ route('contact') }}" class="btn-white">Get in Touch</a>
      <a href="{{ route('services') }}" class="btn-ghost-white">View Our Services</a>
    </div>
  </div>
</section>

@endsection
