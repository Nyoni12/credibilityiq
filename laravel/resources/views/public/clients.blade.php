@extends('layouts.public')

@section('title', 'Our Clients | Credibility Factory Afrique')

@section('meta')
<meta name="description" content="Credibility Factory Afrique serves leading institutions across Zimbabwe and East Africa — including government authorities, regulatory bodies, universities, and private sector organisations.">
<link rel="canonical" href="{{ config('app.url') }}/clients">
@endsection

@section('content')

<!-- PAGE HERO -->
<div class="page-hero" style="background-image:url('{{ asset('images/pages/clients-bg.jpg') }}')">
  <div class="container" style="position:relative;z-index:2;">
    <p class="eyebrow">Industry Pedigree</p>
    <h1 class="display">Trusted By Leading Institutions Across Zimbabwe &amp; East Africa</h1>
    <p class="page-hero-lead">Our frameworks have been deployed with government authorities, regulatory bodies, universities, and high-profile private sector organisations.</p>
  </div>
</div>

<!-- CLIENTS FULL -->
<section class="section clients-section" id="clients">
  <div class="container">
    <div class="clients-track-outer" id="clients-outer"><div class="clients-track" id="clients-track">
      <div class="client-card reveal">
        <div class="client-logo"><picture><source srcset="{{ asset('images/logos/NPA.webp') }}" type="image/webp"><img src="{{ asset('images/logos/NPA.png') }}" alt="National Prosecuting Authority of Zimbabwe" loading="lazy"></picture></div>
        <div class="client-name">National Prosecuting Authority of Zimbabwe</div>
        <div class="client-year">March 2025 &amp; July 2025</div>
        <div class="client-tag">Government / Legal</div>
      </div>
      <div class="client-card reveal reveal-delay-1">
        <div class="client-logo"><picture><source srcset="{{ asset('images/logos/Zimra.webp') }}" type="image/webp"><img src="{{ asset('images/logos/Zimra.png') }}" alt="Zimbabwe Revenue Authority" loading="lazy"></picture></div>
        <div class="client-name">Zimbabwe Revenue Authority (ZIMRA)</div>
        <div class="client-year">April 2025</div>
        <div class="client-tag">Government / Revenue</div>
      </div>
      <div class="client-card reveal reveal-delay-2">
        <div class="client-logo"><picture><source srcset="{{ asset('images/logos/uz.webp') }}" type="image/webp"><img src="{{ asset('images/logos/uz.png') }}" alt="University of Zimbabwe" loading="lazy"></picture></div>
        <div class="client-name">University of Zimbabwe Business School</div>
        <div class="client-year">2024 &amp; 2025</div>
        <div class="client-tag">Higher Education / MBA</div>
      </div>
      <div class="client-card reveal reveal-delay-3">
        <div class="client-logo"><picture><source srcset="{{ asset('images/logos/WAU.webp') }}" type="image/webp"><img src="{{ asset('images/logos/WAU.png') }}" alt="Women's University in Africa" loading="lazy"></picture></div>
        <div class="client-name">Women's University in Africa</div>
        <div class="client-year">2024 &amp; March 2025</div>
        <div class="client-tag">Higher Education / MBA</div>
      </div>
      <div class="client-card reveal">
        <div class="client-logo"><picture><source srcset="{{ asset('images/logos/MAF.webp') }}" type="image/webp"><img src="{{ asset('images/logos/MAF.png') }}" alt="Mobility for Africa" loading="lazy"></picture></div>
        <div class="client-name">Mobility For Africa (MFA)</div>
        <div class="client-year">2022 – 2024</div>
        <div class="client-tag">Social Enterprise</div>
      </div>
      <div class="client-card reveal reveal-delay-1">
        <div class="client-logo"><picture><source srcset="{{ asset('images/logos/HIT.webp') }}" type="image/webp"><img src="{{ asset('images/logos/HIT.png') }}" alt="Harare Institute of Technology" loading="lazy"></picture></div>
        <div class="client-name">Harare Institute of Technology</div>
        <div class="client-year">Ongoing</div>
        <div class="client-tag">Higher Education / Technology</div>
      </div>
      <div class="client-card reveal reveal-delay-2">
        <div class="client-logo"><picture><source srcset="{{ asset('images/logos/40 Under 40.webp') }}" type="image/webp"><img src="{{ asset('images/logos/40 Under 40.png') }}" alt="40 Under 40 Young Business Leaders in Zimbabwe" loading="lazy"></picture></div>
        <div class="client-name">40 Under 40, Young Business Leaders in Zimbabwe</div>
        <div class="client-year">June 2025</div>
        <div class="client-tag">Business Leadership</div>
      </div>
      <div class="client-card reveal reveal-delay-3">
        <div class="client-logo"><picture><source srcset="{{ asset('images/logos/UCCZ.webp') }}" type="image/webp"><img src="{{ asset('images/logos/UCCZ.png') }}" alt="United Church of Christ in Zimbabwe" loading="lazy"></picture></div>
        <div class="client-name">United Church of Christ in Zimbabwe (UCCZ)</div>
        <div class="client-year">May 2025</div>
        <div class="client-tag">Faith-Based Organisation</div>
      </div>
    </div></div>
    <div class="clients-note reveal">
      <div class="clients-note-mark">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 014 10 15.3 15.3 0 01-4 10 15.3 15.3 0 01-4-10 15.3 15.3 0 014-10z"/></svg>
      </div>
      <p><strong>Operating across Zimbabwe, Kenya and Tanzania.</strong> Our frameworks and programmes have been successfully deployed in public sector institutions, private enterprises, academic institutions, NGOs, and faith-based organisations, at every level from frontline staff to board and executive leadership.</p>
    </div>
  </div>
</section>

<!-- BOTTOM CTA -->
<section class="cta-section" style="position:relative;overflow:hidden;">
  <div class="cta-orb cta-orb-1"></div>
  <div class="cta-orb cta-orb-2"></div>
  <div class="container" style="position:relative;z-index:1;">
    <p class="eyebrow" style="color:rgba(255,255,255,0.6);display:flex;justify-content:center;margin-bottom:16px;">Join Our Client Community</p>
    <h2 class="display">Ready to Become a More Credible Institution?</h2>
    <p>Join the leading organisations across Zimbabwe and East Africa that have already deployed our credibility frameworks to drive measurable change.</p>
    <div class="cta-btns">
      <a href="{{ route('contact') }}" class="btn-white">Get in Touch</a>
      <a href="{{ route('services') }}" class="btn-ghost-white">Explore Our Services</a>
    </div>
  </div>
</section>

@endsection

@push('scripts')
<script>
// Carousel — guards against double-init from layout
if (!window._carouselInit && document.getElementById('clients-outer')) {
  window._carouselInit = true;
  (function(){
    var outer = document.getElementById('clients-outer');
    var track = document.getElementById('clients-track');
    if (!outer || !track) return;
    track.innerHTML += track.innerHTML;
    var speed = 0.55, paused = false, dragging = false, startX = 0, startScroll = 0;
    function half() { return track.scrollWidth / 2; }
    function wrap() { if (outer.scrollLeft >= half()) outer.scrollLeft -= half(); if (outer.scrollLeft < 0) outer.scrollLeft += half(); }
    (function tick() { if (!paused && !dragging) { outer.scrollLeft += speed; wrap(); } requestAnimationFrame(tick); })();
    outer.addEventListener('mouseenter', function() { paused = true; });
    outer.addEventListener('mouseleave', function() { paused = false; dragging = false; });
    outer.addEventListener('mousedown', function(e) { dragging = true; startX = e.pageX; startScroll = outer.scrollLeft; outer.style.cursor = 'grabbing'; });
    window.addEventListener('mouseup', function() { dragging = false; outer.style.cursor = 'grab'; });
    outer.addEventListener('mousemove', function(e) {
      if (!dragging) return;
      e.preventDefault();
      outer.scrollLeft = startScroll - (e.pageX - startX);
      wrap();
    });
    var tx = 0, ts = 0;
    outer.addEventListener('touchstart', function(e) { tx = e.touches[0].pageX; ts = outer.scrollLeft; paused = true; }, { passive: true });
    outer.addEventListener('touchmove', function(e) { outer.scrollLeft = ts - (e.touches[0].pageX - tx); wrap(); }, { passive: true });
    outer.addEventListener('touchend', function() { paused = false; });
  })();
}
</script>
@endpush
