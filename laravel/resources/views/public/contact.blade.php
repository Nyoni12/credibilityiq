@extends('layouts.public')

@section('title', 'Contact Us | Credibility Factory Afrique')

@section('meta')
<meta name="description" content="Get in touch with Credibility Factory Afrique. Contact us to book a credibility clinic, culture change programme, strategy facilitation, or enquire about CredibilityIQ. We respond within 24 hours.">
<link rel="canonical" href="{{ config('app.url') }}/contact">
@endsection

@section('content')

<!-- PAGE HERO -->
<div class="page-hero" style="background-image:url('{{ asset('images/pages/contact-bg.jpg') }}')">
  <div class="container" style="position:relative;z-index:2;">
    <p class="eyebrow">Get in Touch</p>
    <h1 class="display">Let's Talk About Your Credibility Journey</h1>
    <p class="page-hero-lead">Our team is ready to discuss your organisation's needs and recommend the right programme for your context. Reach out — we respond within 24 hours.</p>
  </div>
</div>

<!-- CONTACT SECTION -->
<section class="contact-section section" id="contact">
  <div class="container">
    <div class="contact-grid">
      <div class="contact-left reveal-left">
        <p class="eyebrow" style="color:var(--brand-300);">Our Details</p>
        <h2 class="display" style="color:white;margin-top:12px;">We're Here to Help</h2>
        <p>Reach out via any of the channels below. Whether you're ready to book or just exploring, we'll respond within 24 hours with the right guidance for your context.</p>
        <div class="contact-items">
          <div class="contact-item">
            <div class="contact-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="rgba(163,41,204,0.85)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg></div>
            <div class="contact-detail"><strong>Address</strong><span>354 Chelternham Way, Melfort Park, Harare, Zimbabwe</span></div>
          </div>
          <div class="contact-item">
            <div class="contact-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="rgba(163,41,204,0.85)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07A19.5 19.5 0 013.07 9.8a19.79 19.79 0 01-3.07-8.68A2 2 0 012 .99h3a2 2 0 012 1.72c.127.96.361 1.903.7 2.81a2 2 0 01-.45 2.11L6.09 8.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0122 16.92z"/></svg></div>
            <div class="contact-detail"><strong>Phone</strong><a href="tel:+263242006784">Landline: +263-242-006-784</a><a href="tel:+263718584946">Cell: +263-718-584-946</a></div>
          </div>
          <div class="contact-item">
            <div class="contact-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="rgba(163,41,204,0.85)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg></div>
            <div class="contact-detail"><strong>Email</strong><a href="mailto:info@credibilityfactory.net">info@credibilityfactory.net</a><a href="mailto:chris@credibilityfactory.net">chris@credibilityfactory.net</a></div>
          </div>
          <div class="contact-item">
            <div class="contact-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="rgba(163,41,204,0.85)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 014 10 15.3 15.3 0 01-4 10 15.3 15.3 0 01-4-10 15.3 15.3 0 014-10z"/></svg></div>
            <div class="contact-detail"><strong>Website</strong><a href="https://www.credibilityfactory.net" target="_blank" rel="noopener">www.credibilityfactory.net</a></div>
          </div>
        </div>
      </div>
      <div class="reveal-right">
        <div class="contact-form">
          <h3>Send Us a Message</h3>
          <div class="form-group"><label>Full Name</label><input id="cf-name" type="text" placeholder="Your name"></div>
          <div class="form-group"><label>Organisation</label><input id="cf-org" type="text" placeholder="Your organisation"></div>
          <div class="form-group"><label>Email Address</label><input id="cf-email" type="email" placeholder="your@email.com"></div>
          <div class="form-group"><label>I'm Interested In</label><select id="cf-service"><option value="">Select a service…</option><option>Credibility Clinics for Executives</option><option>Corporate Strategy, Values Alignment</option><option>Culture Change Training</option><option>Business Integrity Workshop</option><option>Strategy Facilitation &amp; Team Building</option><option>Customer Service Surveys &amp; Training</option><option>CredibilityIQ Platform</option><option>General Enquiry</option></select></div>
          <div class="form-group"><label>Message</label><textarea id="cf-message" placeholder="Tell us about your organisation and what you're looking to achieve…"></textarea></div>
          <button class="form-submit" type="button" id="contact-submit">Send Message</button>
        </div>
      </div>
    </div>
  </div>
</section>

@endsection

@push('scripts')
<script>
document.getElementById('contact-submit').addEventListener('click', function() {
  var btn = this;
  var name    = document.getElementById('cf-name').value.trim();
  var org     = document.getElementById('cf-org').value.trim();
  var email   = document.getElementById('cf-email').value.trim();
  var service = document.getElementById('cf-service').value;
  var message = document.getElementById('cf-message').value.trim();

  if (!name || !email || !message) {
    btn.textContent = 'Please fill required fields';
    btn.style.background = '#dc2626';
    setTimeout(function() { btn.textContent = 'Send Message'; btn.style.background = ''; }, 3000);
    return;
  }

  btn.textContent = 'Sending…';
  btn.disabled = true;

  fetch('/contact', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
    body: JSON.stringify({ name: name, org: org, email: email, service: service, message: message })
  })
  .then(function(r) { return r.json(); })
  .then(function(data) {
    if (data.ok) {
      btn.textContent = 'Message Sent!';
      btn.style.background = '#059669';
      document.getElementById('cf-name').value    = '';
      document.getElementById('cf-org').value     = '';
      document.getElementById('cf-email').value   = '';
      document.getElementById('cf-service').value = '';
      document.getElementById('cf-message').value = '';
      setTimeout(function() { btn.textContent = 'Send Message'; btn.style.background = ''; btn.disabled = false; }, 4000);
    } else {
      throw new Error('not ok');
    }
  })
  .catch(function() {
    btn.textContent = 'Failed — please try again';
    btn.style.background = '#dc2626';
    btn.disabled = false;
    setTimeout(function() { btn.textContent = 'Send Message'; btn.style.background = ''; }, 4000);
  });
});
</script>
@endpush
