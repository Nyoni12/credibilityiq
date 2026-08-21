@extends('layouts.public')

@section('title', 'Privacy Policy | Credibility Factory Afrique')

@section('meta')
<meta name="description" content="Privacy Policy for CredibilityIQ - how we collect, use, and protect your data in compliance with the Zimbabwe Cyber and Data Protection Act 2021.">
<link rel="canonical" href="{{ config('app.url') }}/privacy-policy">
@endsection

@section('content')

<style>
.legal-hero{padding:80px 0 60px;background:var(--brand);border-bottom:3px solid var(--accent)}
.legal-hero h1{color:#fff;font-size:clamp(1.8rem,4vw,2.6rem);margin:8px 0 0;line-height:1.2}
.legal-hero .eyebrow{color:var(--accent)}
.legal-date{font-size:0.8rem;color:rgba(255,255,255,0.45);margin-top:10px}
.legal-body{max-width:760px;padding:64px 0 96px}
.legal-body h2{font-family:Cambria,Georgia,serif;font-size:1.15rem;font-weight:700;color:var(--text);margin:48px 0 12px;padding-bottom:8px;border-bottom:1px solid var(--border)}
.legal-body h2:first-child{margin-top:0}
.legal-body h3{font-size:0.92rem;font-weight:600;color:var(--text);margin:20px 0 8px}
.legal-body p{font-size:0.92rem;color:var(--text-muted);line-height:1.8;margin-bottom:12px}
.legal-body ul{margin:8px 0 12px 0;padding-left:20px}
.legal-body ul li{font-size:0.92rem;color:var(--text-muted);line-height:1.7;margin-bottom:4px}
.legal-body a{color:var(--accent-600);text-decoration:underline}
.legal-body a:hover{color:var(--accent)}
.legal-body code{background:var(--brand-50);color:var(--brand);padding:1px 6px;border-radius:3px;font-size:0.82rem}
.legal-body strong{color:var(--text);font-weight:600}
</style>

<!-- PAGE HERO -->
<div class="legal-hero">
  <div class="container">
    <p class="eyebrow">Legal</p>
    <h1>Privacy Policy</h1>
    <p class="legal-date">Last updated: {{ now()->format('d F Y') }}</p>
  </div>
</div>

<!-- CONTENT -->
<div class="container">
  <div class="legal-body">

    <h2>1. Who We Are</h2>
    <p>Credibility Factory Afrique (CFA), trading as <strong>CredibilityIQ</strong>, is a credibility training and consulting company registered in Zimbabwe. We operate the CredibilityIQ platform at <a href="https://credibilityfactory.net">credibilityfactory.net</a>.</p>
    <p><strong>Data Controller:</strong> Credibility Factory Afrique, 354 Chelternham Way, Melfort Park, Harare, Zimbabwe.<br>
    <strong>Contact:</strong> <a href="mailto:info@credibilityfactory.net">info@credibilityfactory.net</a> | +263 242 006 784</p>

    <h2>2. Legal Basis</h2>
    <p>This policy is prepared in compliance with:</p>
    <ul>
      <li>Zimbabwe <strong>Cyber and Data Protection Act, Chapter 12:07 (2021)</strong></li>
      <li>Zimbabwe <strong>Access to Information and Protection of Privacy Act (AIPPA)</strong></li>
      <li>Kenya <strong>Data Protection Act, 2019</strong> (for Kenyan data subjects)</li>
      <li>South Africa <strong>POPIA, 2013</strong> (for South African data subjects)</li>
    </ul>

    <h2>3. What Data We Collect</h2>
    <h3>a. Organisation Administrators</h3>
    <ul>
      <li>Name and email address (account registration)</li>
      <li>Company name, industry, and annual revenue (for credibility scoring)</li>
      <li>Login timestamps and IP addresses (security audit logs)</li>
    </ul>
    <h3>b. Anonymous Survey Respondents</h3>
    <ul>
      <li>Credibility scores (1-10) per value dimension - <strong>no names or emails collected</strong></li>
      <li>Optional respondent role (e.g. "Manager") — self-described, no verification</li>
      <li>IP address — used solely to prevent duplicate submissions; not linked to identity</li>
    </ul>
    <h3>c. Technical Data</h3>
    <ul>
      <li>Server logs (IP, user agent, page accessed, timestamp)</li>
      <li>Session cookies (HttpOnly, expiry 120 minutes)</li>
    </ul>

    <h2>4. How We Use Your Data</h2>
    <ul>
      <li>Provide and operate the CredibilityIQ assessment platform</li>
      <li>Calculate CFA Credibility Lifecycle Continuum (CLC) scores</li>
      <li>Send password-reset emails (when requested)</li>
      <li>Detect and prevent fraud, abuse, and unauthorised access</li>
      <li>Comply with legal obligations</li>
    </ul>
    <p>We do <strong>not</strong> sell your data. We do not use survey data for advertising profiling.</p>

    <h2>5. Data Sharing</h2>
    <p>We share data only as necessary:</p>
    <ul>
      <li><strong>Hosting provider</strong> — your data is stored on servers in our cPanel hosting environment</li>
      <li><strong>Email service</strong> — reset-link emails are sent via our configured SMTP provider</li>
      <li><strong>Legal requirements</strong> — if required by a court order or lawful authority in Zimbabwe</li>
    </ul>

    <h2>6. Data Retention</h2>
    <ul>
      <li>Assessment and scorecard data: retained for the lifetime of the organisation account</li>
      <li>Audit logs: retained for 12 months, then auto-purged</li>
      <li>Survey IP records: retained for 90 days after the assessment closes</li>
      <li>Account data: deleted within 30 days of account termination upon written request</li>
    </ul>

    <h2>7. Your Rights</h2>
    <p>Under the Zimbabwe Cyber and Data Protection Act 2021, you have the right to:</p>
    <ul>
      <li><strong>Access</strong> — request a copy of your personal data</li>
      <li><strong>Rectification</strong> — correct inaccurate data</li>
      <li><strong>Erasure</strong> — request deletion of your data (subject to legal retention obligations)</li>
      <li><strong>Portability</strong> — receive your data in a machine-readable format (CSV export available in-platform)</li>
      <li><strong>Objection</strong> — object to processing for direct marketing</li>
    </ul>
    <p>Submit requests to: <a href="mailto:info@credibilityfactory.net">info@credibilityfactory.net</a></p>

    <h2>8. Security</h2>
    <p>We implement appropriate technical and organisational measures including:</p>
    <ul>
      <li>HTTPS encryption in transit (TLS 1.2+)</li>
      <li>Bcrypt password hashing</li>
      <li>CSRF token protection on all state-changing requests</li>
      <li>Rate limiting on login and survey submission endpoints</li>
      <li>Multi-tenant data isolation — each organisation can only access their own data</li>
      <li>Activity audit logs for administrative actions</li>
    </ul>

    <h2>9. Cookies</h2>
    <p>We use essential session cookies only. No third-party tracking cookies are set. The session cookie (<code>credibilityiq_session</code>) is HttpOnly, SameSite=Lax, and expires after 120 minutes of inactivity.</p>

    <h2>10. Changes to This Policy</h2>
    <p>We will notify registered administrators of material changes via email at least 14 days before they take effect. Continued use of the platform after notification constitutes acceptance.</p>

    <h2>11. Contact &amp; Complaints</h2>
    <p>For privacy concerns, contact our Data Protection Officer:</p>
    <p>
      <strong>Credibility Factory Afrique</strong><br>
      354 Chelternham Way, Melfort Park, Harare, Zimbabwe<br>
      <a href="mailto:info@credibilityfactory.net">info@credibilityfactory.net</a><br>
      +263 242 006 784
    </p>
    <p>Zimbabwean data subjects may lodge complaints with the <strong>Postal and Telecommunications Regulatory Authority of Zimbabwe (POTRAZ)</strong>, the designated supervisory authority under the Cyber and Data Protection Act.</p>

  </div>
</div>

@endsection
