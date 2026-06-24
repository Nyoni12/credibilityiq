import { useEffect, useRef, useState } from 'react';
import Link from 'next/link';
import { useAuth } from '@/context/AuthContext';
import { useRouter } from 'next/router';

// ── Scroll-reveal hook ─────────────────────────────────────────────
// `ready` must be true before we run — prevents the hook from firing
// while the component is still showing its loading spinner (at which
// point the reveal elements don't exist in the DOM yet).
function useReveal(ready: boolean) {
  useEffect(() => {
    if (!ready) return;

    const els = Array.from(
      document.querySelectorAll<Element>('.reveal, .reveal-left, .reveal-right')
    );

    // Synchronously reveal everything already in (or near) the viewport
    // BEFORE enabling the hiding CSS, so there is never a blank flash.
    const vh = window.innerHeight;
    els.forEach((el) => {
      const { top } = el.getBoundingClientRect();
      if (top < vh + 160) el.classList.add('visible');
    });

    // Only now do we let CSS hide below-fold elements
    document.body.classList.add('js-reveal');

    // Observer handles the rest as the user scrolls
    const obs = new IntersectionObserver(
      (entries) =>
        entries.forEach((e) => { if (e.isIntersecting) e.target.classList.add('visible'); }),
      { threshold: 0, rootMargin: '0px 0px 160px 0px' }
    );
    els.forEach((el) => { if (!el.classList.contains('visible')) obs.observe(el); });

    return () => {
      obs.disconnect();
      document.body.classList.remove('js-reveal');
    };
  }, [ready]);
}

// ── Animated counter ───────────────────────────────────────────────
function Counter({ target, suffix = '', duration = 2000 }: { target: number; suffix?: string; duration?: number }) {
  const [value, setValue] = useState(0);
  const ref = useRef<HTMLSpanElement>(null);
  const started = useRef(false);

  useEffect(() => {
    const el = ref.current;
    if (!el) return;
    const obs = new IntersectionObserver(([entry]) => {
      if (entry.isIntersecting && !started.current) {
        started.current = true;
        const start = performance.now();
        const tick = (now: number) => {
          const pct = Math.min((now - start) / duration, 1);
          const ease = 1 - Math.pow(1 - pct, 3);
          setValue(Math.floor(ease * target));
          if (pct < 1) requestAnimationFrame(tick);
        };
        requestAnimationFrame(tick);
      }
    }, { threshold: 0.5 });
    obs.observe(el);
    return () => obs.disconnect();
  }, [target, duration]);

  return <span ref={ref}>{value.toLocaleString()}{suffix}</span>;
}

// ── Feature card ───────────────────────────────────────────────────
function FeatureCard({ icon, title, desc, delay = '' }: { icon: string; title: string; desc: string; delay?: string }) {
  return (
    <div className={`reveal ${delay} group relative bg-white rounded-2xl p-7 shadow-lg border border-gray-100 hover:shadow-2xl hover:-translate-y-2 transition-all duration-400 overflow-hidden`}>
      <div className="absolute inset-0 bg-gradient-to-br from-brand-50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-400" />
      <div className="relative">
        <div className="w-14 h-14 rounded-2xl bg-gradient-to-br from-brand-500 to-accent-500 flex items-center justify-center text-2xl mb-5 shadow-lg group-hover:scale-110 transition-transform duration-300">
          {icon}
        </div>
        <h3 className="text-lg font-bold text-gray-900 mb-2">{title}</h3>
        <p className="text-gray-500 text-sm leading-relaxed">{desc}</p>
      </div>
    </div>
  );
}

// ── Step card ──────────────────────────────────────────────────────
function StepCard({ num, title, desc, delay = '' }: { num: string; title: string; desc: string; delay?: string }) {
  return (
    <div className={`reveal ${delay} flex flex-col items-center text-center relative`}>
      <div className="w-16 h-16 rounded-full bg-gradient-to-br from-brand-500 to-accent-500 flex items-center justify-center text-white text-2xl font-black mb-5 shadow-xl animate-pulse-glow">
        {num}
      </div>
      <h3 className="text-lg font-bold text-gray-900 mb-2">{title}</h3>
      <p className="text-gray-500 text-sm leading-relaxed max-w-xs">{desc}</p>
    </div>
  );
}


// ── Main landing page ──────────────────────────────────────────────
export default function LandingPage() {
  const { user, loading } = useAuth();
  const router = useRouter();
  const [scrolled, setScrolled] = useState(false);
  const [menuOpen, setMenuOpen] = useState(false);
  // Pass `ready` so the hook waits until auth resolves and real content is rendered
  useReveal(!loading && !user);

  useEffect(() => {
    if (!loading && user) router.replace(user.is_superadmin ? '/admin' : '/dashboard');
  }, [user, loading, router]);

  useEffect(() => {
    const handler = () => setScrolled(window.scrollY > 40);
    window.addEventListener('scroll', handler);
    return () => window.removeEventListener('scroll', handler);
  }, []);

  if (loading) return (
    <div className="min-h-screen flex items-center justify-center gradient-bg">
      <div className="w-14 h-14 border-4 border-white border-t-transparent rounded-full animate-spin" />
    </div>
  );
  if (user) return null;

  return (
    <div className="min-h-screen bg-white overflow-x-hidden">

      {/* ── Navbar ─────────────────────────────────────────── */}
      <nav className={`fixed top-0 left-0 right-0 z-50 transition-all duration-500 ${
        scrolled ? 'glass-light shadow-lg py-3' : 'py-5'
      }`}>
        <div className="max-w-7xl mx-auto px-6 flex items-center justify-between">
          <div className="flex items-center">
            <div className={`transition-all duration-500 rounded-xl overflow-hidden ${
              scrolled ? '' : 'bg-white/95 shadow-lg px-3 py-1.5'
            }`}>
              <img src="/logo.png" alt="Credibility Factory Afrique"
                className="h-8 w-auto block" />
            </div>
          </div>

          <div className="hidden md:flex items-center gap-8">
            {['Features', 'How It Works'].map((item) => (
              <a key={item}
                href={`#${item.toLowerCase().replace(/\s+/g, '-')}`}
                className={`text-sm font-medium transition-colors hover:text-brand-400 ${scrolled ? 'text-gray-600' : 'text-white/80'}`}
              >{item}</a>
            ))}
          </div>

          <div className="flex items-center gap-3">
            <Link href="/login">
              <button className={`px-4 py-2 text-sm font-semibold rounded-xl transition-all duration-300
                ${scrolled ? 'text-brand-600 hover:bg-brand-50' : 'text-white hover:bg-white/10'}`}>
                Sign In
              </button>
            </Link>
            <Link href="/signup">
              <button className="px-5 py-2 text-sm font-bold rounded-xl bg-white text-brand-600 hover:bg-brand-50 shadow-lg hover:shadow-xl transition-all duration-300 shimmer-btn">
                Get Started
              </button>
            </Link>
          </div>
        </div>
      </nav>

      {/* ── Hero ────────────────────────────────────────────── */}
      <section className="relative min-h-screen flex items-center justify-center overflow-hidden pt-20"
        style={{
          background: [
            'radial-gradient(ellipse at 78% 8%,  rgba(163,41,204,.38) 0%, transparent 48%)',
            'radial-gradient(ellipse at 10% 82%, rgba(0,166,81,.22)   0%, transparent 46%)',
            'radial-gradient(ellipse at 42% 50%, rgba(31,33,146,.55)  0%, transparent 62%)',
            'linear-gradient(152deg, #020317 0%, #07093c 22%, #181b8e 52%, #130740 80%, #020317 100%)',
          ].join(','),
        }}>

        <div className="absolute inset-0 overflow-hidden pointer-events-none" aria-hidden="true">

          {/* ── Africa continent watermark — core brand element ── */}
          <div className="absolute inset-0 flex items-center justify-end">
            <svg
              viewBox="0 0 100 122"
              fill="currentColor"
              className="h-[92vh] max-h-[840px] w-auto opacity-[0.09] text-white translate-x-[10%] flex-shrink-0"
              xmlns="http://www.w3.org/2000/svg"
            >
              {/*
                Simplified Africa continent outline.
                Key features preserved: Mediterranean coast, Horn of Africa (east),
                Cape of Good Hope (south), Gulf of Guinea indent (west), Senegal bulge.
              */}
              <path d="
                M 30,4
                C 35,1 38,0 41,0
                L 53,2
                C 63,5 69,9 71,11
                L 73,19
                C 79,30 85,38 88,43
                L 100,43
                C 97,49 94,52 93,54
                L 82,64
                C 80,72 79,77 79,80
                L 73,94
                L 65,110
                L 51,122
                L 42,113
                L 34,96
                C 37,87 40,83 40,79
                L 38,69
                L 36,59
                C 30,56 25,53 23,53
                L 17,53
                C 12,50  8,47  7,46
                L 0,38
                L 12,21
                Z
              " />
            </svg>
          </div>

          {/* Fine dot-grid texture */}
          <div className="absolute inset-0 opacity-[0.055]"
            style={{
              backgroundImage: [
                'radial-gradient(circle, rgba(255,255,255,.7) 1px, transparent 1px)',
              ].join(','),
              backgroundSize: '40px 40px',
            }} />

          {/* Subtle line grid */}
          <div className="absolute inset-0 opacity-[0.04]"
            style={{
              backgroundImage: [
                'linear-gradient(rgba(255,255,255,.3) 1px, transparent 1px)',
                'linear-gradient(90deg, rgba(255,255,255,.3) 1px, transparent 1px)',
              ].join(','),
              backgroundSize: '80px 80px',
            }} />

          {/* Floating orbs */}
          {[0, 1, 2].map((i) => (
            <div key={i} className="absolute rounded-full bg-white/5 border border-white/10 animate-float"
              style={{
                width: `${50 + i * 30}px`, height: `${50 + i * 30}px`,
                left: `${12 + i * 22}%`, top: `${18 + i * 22}%`,
                animationDelay: `${i * 1.3}s`, animationDuration: `${6 + i * 1.5}s`,
              }} />
          ))}
        </div>

        <div className="relative max-w-6xl mx-auto px-6 text-center">
          {/* Badge */}
          <div className="animate-fadeInDown inline-flex items-center gap-2 glass px-5 py-2 rounded-full text-white/90 text-sm font-medium mb-8">
            <span className="w-2 h-2 bg-green-400 rounded-full animate-pulse" />
            Trusted by leading corporations across Africa
          </div>

          {/* Headline */}
          <h1 className="animate-fadeInUp text-5xl md:text-7xl font-black text-white leading-tight mb-6" style={{ animationDelay: '0.1s' }}>
            Turn Values Into
            <br />
            <span className="gradient-text">Measurable Results</span>
          </h1>

          {/* Sub */}
          <p className="animate-fadeInUp text-lg md:text-xl text-white/70 max-w-2xl mx-auto mb-10 leading-relaxed" style={{ animationDelay: '0.25s' }}>
            Credibility Factory Afrique quantifies how well your staff actually live your company values —
            and converts every performance gap into a precise financial loss figure.
          </p>

          {/* CTAs */}
          <div className="animate-fadeInUp flex flex-col sm:flex-row items-center justify-center gap-4 mb-16" style={{ animationDelay: '0.4s' }}>
            <Link href="/signup">
              <button className="px-8 py-4 bg-white text-brand-600 font-black text-base rounded-2xl shadow-2xl hover:shadow-white/30 hover:-translate-y-1 transition-all duration-300 shimmer-btn">
                Start Free Assessment →
              </button>
            </Link>
            <a href="#how-it-works">
              <button className="px-8 py-4 glass text-white font-semibold text-base rounded-2xl hover:bg-white/15 transition-all duration-300 flex items-center gap-2">
                <span className="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center text-xs">▶</span>
                See How It Works
              </button>
            </a>
          </div>

          {/* Hero mockup */}
          <div className="animate-fadeIn relative max-w-4xl mx-auto" style={{ animationDelay: '0.6s' }}>
            <div className="glass rounded-3xl p-4 shadow-2xl">
              <div className="bg-gray-900/80 rounded-2xl p-6">
                <div className="flex items-center gap-2 mb-4">
                  <div className="w-3 h-3 rounded-full bg-red-400" />
                  <div className="w-3 h-3 rounded-full bg-yellow-400" />
                  <div className="w-3 h-3 rounded-full bg-green-400" />
                  <div className="flex-1 bg-gray-800 rounded-md h-6 ml-4 flex items-center px-3">
                    <span className="text-gray-400 text-xs">credibilityfactoryafrique.com/scorecard</span>
                  </div>
                </div>
                <div className="grid grid-cols-3 gap-4 mb-4">
                  {[
                    { label: 'Credibility Score', value: '74.2%', color: 'text-blue-400' },
                    { label: 'Financial Leakage', value: '$284,500', color: 'text-red-400' },
                    { label: 'Training Flags', value: '4', color: 'text-amber-400' },
                  ].map((s) => (
                    <div key={s.label} className="bg-gray-800 rounded-xl p-4 text-center">
                      <p className="text-gray-400 text-xs mb-1">{s.label}</p>
                      <p className={`text-xl font-black ${s.color}`}>{s.value}</p>
                    </div>
                  ))}
                </div>
                <div className="bg-gray-800 rounded-xl p-4">
                  <div className="flex justify-between text-xs text-gray-400 mb-3">
                    <span>Value Performance</span><span>Avg Score / 10</span>
                  </div>
                  {[
                    { name: 'Integrity', score: 8.1, color: 'bg-green-500' },
                    { name: 'Client Focus', score: 5.2, color: 'bg-red-500' },
                    { name: 'Innovation', score: 6.7, color: 'bg-blue-500' },
                    { name: 'Accountability', score: 4.9, color: 'bg-amber-500' },
                  ].map((v) => (
                    <div key={v.name} className="flex items-center gap-3 mb-2">
                      <span className="text-gray-400 text-xs w-28 text-right">{v.name}</span>
                      <div className="flex-1 bg-gray-700 rounded-full h-2 overflow-hidden">
                        <div className={`${v.color} h-2 rounded-full transition-all duration-1000`}
                          style={{ width: `${v.score * 10}%` }} />
                      </div>
                      <span className="text-white text-xs font-bold w-8">{v.score}</span>
                    </div>
                  ))}
                </div>
              </div>
            </div>
            {/* Glow effect under card */}
            <div className="absolute -bottom-6 left-1/2 -translate-x-1/2 w-3/4 h-16 bg-brand-500/30 blur-2xl rounded-full" />
          </div>
        </div>

        {/* Scroll indicator */}
        <div className="absolute bottom-8 left-1/2 -translate-x-1/2 flex flex-col items-center gap-2 animate-fadeIn" style={{ animationDelay: '1s' }}>
          <span className="text-white/40 text-xs uppercase tracking-widest">Scroll</span>
          <div className="w-px h-12 bg-gradient-to-b from-white/40 to-transparent animate-pulse" />
        </div>
      </section>

      {/* ── Stats ticker ────────────────────────────────────── */}
      <div className="bg-brand-500 py-4 overflow-hidden">
        <div className="flex animate-ticker whitespace-nowrap">
          {[...Array(2)].map((_, ri) => (
            <div key={ri} className="flex items-center gap-12 pr-12 flex-shrink-0">
              {['✦ 300+ Assessments Completed', '✦ 105+ Companies Onboarded', '✦ 26 Corporate Values Mapped', '✦ $2M+ Financial Leakage Identified', '✦ 100% Anonymous Staff Surveys', '✦ Instant PDF Reports'].map((t) => (
                <span key={t} className="text-white/90 font-semibold text-sm tracking-wide">{t}</span>
              ))}
            </div>
          ))}
        </div>
      </div>

      {/* ── Stats section ────────────────────────────────────── */}
      <section className="py-24 bg-gray-50">
        <div className="max-w-6xl mx-auto px-6">
          <div className="grid grid-cols-2 md:grid-cols-4 gap-8">
            {[
              { target: 300, suffix: '+', label: 'Assessments Run' },
              { target: 105, suffix: '+', label: 'Staff Surveyed' },
              { target: 26,  suffix: '',  label: 'Values Mapped' },
              { target: 98,  suffix: '%', label: 'Client Satisfaction' },
            ].map((s, i) => (
              <div key={s.label} className={`reveal delay-${(i + 1) * 100} text-center`}>
                <div className="text-5xl font-black text-brand-600 mb-2">
                  <Counter target={s.target} suffix={s.suffix} />
                </div>
                <p className="text-gray-500 text-sm font-medium">{s.label}</p>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* ── Features ─────────────────────────────────────────── */}
      <section id="features" className="py-28 bg-white">
        <div className="max-w-7xl mx-auto px-6">
          <div className="text-center mb-16">
            <p className="reveal text-brand-500 font-semibold uppercase tracking-widest text-sm mb-3">Platform Features</p>
            <h2 className="reveal delay-100 text-4xl md:text-5xl font-black text-gray-900 mb-4">
              Everything You Need to<br />
              <span className="gradient-text">Measure What Matters</span>
            </h2>
            <p className="reveal delay-200 text-gray-400 text-lg max-w-2xl mx-auto">
              A complete platform built for corporate training consultants and HR leaders who need real data, not guesswork.
            </p>
          </div>

          <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            <FeatureCard delay="delay-100" icon="📊"
              title="Anonymous Staff Assessments"
              desc="Unique survey links per company. Staff rate values 1–10 with zero login required. No PII collected. Results are brutally honest." />
            <FeatureCard delay="delay-200" icon="💸"
              title="Financial Leakage Calculator"
              desc="Every value gap is translated into a dollar loss using your company's financial weight model. Know exactly what misalignment costs." />
            <FeatureCard delay="delay-300" icon="🎯"
              title="Real-Time Scorecards"
              desc="Live dashboards update as responses come in. Overall credibility score, per-value breakdown, and top 3 costliest gaps at a glance." />
            <FeatureCard delay="delay-100" icon="🧠"
              title="Smart Training Flags"
              desc="Training programs are automatically recommended when any value score drops below your set threshold. Targeted, not generic." />
            <FeatureCard delay="delay-200" icon="📄"
              title="Branded PDF Reports"
              desc="One-click generation of a professional, client-ready PDF report with your logo, scoring, financials, and recommendations." />
            <FeatureCard delay="delay-300" icon="🏢"
              title="Multi-Tenant Architecture"
              desc="Manage unlimited corporate clients from a single Super Admin dashboard. Each company sees only their own data — always." />
          </div>
        </div>
      </section>

      {/* ── How It Works ─────────────────────────────────────── */}
      <section id="how-it-works" className="py-28 relative overflow-hidden"
        style={{
          background: [
            'radial-gradient(ellipse at 10% 20%,  rgba(163,41,204,.28) 0%, transparent 50%)',
            'radial-gradient(ellipse at 85% 75%, rgba(0,166,81,.16)   0%, transparent 46%)',
            'linear-gradient(152deg, #020317 0%, #07093c 25%, #181b8e 55%, #130740 82%, #020317 100%)',
          ].join(','),
        }}>
        <div className="absolute inset-0 pointer-events-none overflow-hidden">
          {/* Stroke-only Africa outline — lighter treatment vs hero fill */}
          <div className="absolute inset-0 flex items-center justify-start">
            <svg
              viewBox="0 0 100 122"
              fill="none"
              stroke="white"
              strokeWidth="1.5"
              className="h-[80vh] max-h-[700px] w-auto opacity-[0.06] -translate-x-[15%] flex-shrink-0"
              xmlns="http://www.w3.org/2000/svg"
              aria-hidden="true"
            >
              <path d="M 30,4 C 35,1 38,0 41,0 L 53,2 C 63,5 69,9 71,11 L 73,19 C 79,30 85,38 88,43 L 100,43 C 97,49 94,52 93,54 L 82,64 C 80,72 79,77 79,80 L 73,94 L 65,110 L 51,122 L 42,113 L 34,96 C 37,87 40,83 40,79 L 38,69 L 36,59 C 30,56 25,53 23,53 L 17,53 C 12,50 8,47 7,46 L 0,38 L 12,21 Z" />
            </svg>
          </div>
          <div className="absolute inset-0 opacity-[0.04]"
            style={{
              backgroundImage: 'radial-gradient(circle, rgba(255,255,255,.6) 1px, transparent 1px)',
              backgroundSize: '40px 40px',
            }} />
        </div>

        <div className="relative max-w-6xl mx-auto px-6">
          <div className="text-center mb-20">
            <p className="reveal text-brand-300 font-semibold uppercase tracking-widest text-sm mb-3">Simple Process</p>
            <h2 className="reveal delay-100 text-4xl md:text-5xl font-black text-white mb-4">
              From Setup to Scorecard<br />
              <span className="gradient-text">in Under 30 Minutes</span>
            </h2>
          </div>

          <div className="grid md:grid-cols-3 gap-12 relative">
            {/* Connector line */}
            <div className="hidden md:block absolute top-8 left-1/6 right-1/6 h-0.5 bg-gradient-to-r from-brand-500 via-accent-400 to-cfa-green-500 opacity-40" style={{ left: '18%', right: '18%' }} />

            <StepCard num="01" delay="delay-100"
              title="Configure Your Values"
              desc="Set up up to 12 company values, write descriptions, and assign a financial weight to each — takes 10 minutes." />
            <StepCard num="02" delay="delay-200"
              title="Send the Survey Link"
              desc="Share a unique, anonymous link with your staff. They rate each value from 1–10 on any device, no sign-up required." />
            <StepCard num="03" delay="delay-300"
              title="Download Your Scorecard"
              desc="Instantly view your credibility score, financial leakage per value, and training recommendations. Export as a branded PDF." />
          </div>
        </div>
      </section>

      {/* ── Social proof ─────────────────────────────────────── */}
      <section className="py-24 bg-gray-50">
        <div className="max-w-6xl mx-auto px-6">
          <div className="text-center mb-14">
            <h2 className="reveal text-3xl font-black text-gray-900 mb-3">What Clients Say</h2>
            <p className="reveal delay-100 text-gray-400">Trusted by HR leaders, trainers and consultants</p>
          </div>
          <div className="grid md:grid-cols-3 gap-6">
            {[
              { quote: 'We discovered we were losing over $400,000 a year to the gap between our stated values and actual staff behaviour. Credibility Factory Afrique made that visible in a single afternoon.', name: 'Sarah M.', role: 'Chief People Officer, Nexus Financial' },
              { quote: 'The anonymous survey format got us responses we never would have collected in a town hall. The honesty of the data was eye-opening for our executive team.', name: 'James D.', role: 'L&D Director, PeakLogix Solutions' },
              { quote: 'Within a week of running the assessment, we had a targeted training plan aligned to our biggest financial risk areas. The ROI conversation is now so much easier.', name: 'Thandiwe K.', role: 'Head of HR, BrightMinds Academy' },
            ].map((t, i) => (
              <div key={i} className={`reveal delay-${(i + 1) * 200} bg-white rounded-2xl p-7 shadow-lg border border-gray-100 hover:shadow-xl hover:-translate-y-1 transition-all duration-300`}>
                <div className="text-brand-500 text-3xl mb-4 font-serif leading-none">"</div>
                <p className="text-gray-600 text-sm leading-relaxed mb-6 italic">{t.quote}</p>
                <div className="flex items-center gap-3">
                  <div className="w-10 h-10 rounded-full bg-gradient-to-br from-brand-500 to-accent-500 flex items-center justify-center text-white font-bold text-sm">
                    {t.name[0]}
                  </div>
                  <div>
                    <p className="font-bold text-gray-900 text-sm">{t.name}</p>
                    <p className="text-gray-400 text-xs">{t.role}</p>
                  </div>
                </div>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* ── Final CTA ────────────────────────────────────────── */}
      <section className="py-28 relative overflow-hidden"
        style={{
          background: [
            'radial-gradient(ellipse at 85% 10%,  rgba(163,41,204,.30) 0%, transparent 48%)',
            'radial-gradient(ellipse at 15% 85%, rgba(0,166,81,.18)   0%, transparent 46%)',
            'radial-gradient(ellipse at 50% 50%, rgba(31,33,146,.42)  0%, transparent 60%)',
            'linear-gradient(152deg, #020317 0%, #07093c 22%, #181b8e 52%, #130740 80%, #020317 100%)',
          ].join(','),
        }}>
        <div className="absolute inset-0 pointer-events-none overflow-hidden">
          <div className="absolute inset-0 opacity-[0.04]"
            style={{
              backgroundImage: 'radial-gradient(circle, rgba(255,255,255,.6) 1px, transparent 1px)',
              backgroundSize: '40px 40px',
            }} />
          <div className="absolute -top-32 -left-32 w-96 h-96 bg-white/5 rounded-full animate-morph" />
          <div className="absolute -bottom-32 -right-32 w-80 h-80 bg-white/5 rounded-full animate-morph animate-float-rev" />
        </div>
        <div className="relative max-w-3xl mx-auto px-6 text-center">
          <h2 className="reveal text-4xl md:text-5xl font-black text-white mb-6">
            Ready to Discover What Your<br />
            <span className="gradient-text">Values Are Really Worth?</span>
          </h2>
          <p className="reveal delay-100 text-white/70 text-lg mb-10 max-w-xl mx-auto">
            Join hundreds of companies that have quantified their credibility gap and taken targeted action to close it.
          </p>
          <div className="reveal delay-200 flex flex-col sm:flex-row gap-4 justify-center">
            <Link href="/signup">
              <button className="px-10 py-4 bg-white text-brand-600 font-black text-base rounded-2xl shadow-2xl hover:-translate-y-1 hover:shadow-white/20 transition-all duration-300 shimmer-btn">
                Get Started Today →
              </button>
            </Link>
            <Link href="/login">
              <button className="px-10 py-4 glass text-white font-semibold text-base rounded-2xl hover:bg-white/15 transition-all duration-300">
                Sign In to Dashboard
              </button>
            </Link>
          </div>
        </div>
      </section>

      {/* ── Footer ───────────────────────────────────────────── */}
      <footer className="bg-gray-950 text-gray-400 pt-16 pb-8">
        <div className="max-w-6xl mx-auto px-6">
          <div className="grid md:grid-cols-4 gap-12 mb-14">
            <div className="md:col-span-2">
              <div className="mb-5">
                <img src="/logo.png" alt="Credibility Factory Afrique"
                  className="h-9 w-auto brightness-0 invert" />
              </div>
              <p className="text-sm leading-relaxed max-w-xs">
                The corporate credibility scorecard platform that converts values gaps into financial insights and targeted training action plans.
              </p>
            </div>
            <div>
              <p className="text-white font-semibold mb-4 text-sm uppercase tracking-wider">Platform</p>
              <ul className="space-y-3 text-sm">
                {['Features', 'How It Works', 'Security'].map((l) => (
                  <li key={l}><a href="#" className="hover:text-white transition-colors">{l}</a></li>
                ))}
              </ul>
            </div>
            <div>
              <p className="text-white font-semibold mb-4 text-sm uppercase tracking-wider">Account</p>
              <ul className="space-y-3 text-sm">
                <li><Link href="/login"><span className="hover:text-white transition-colors cursor-pointer">Sign In</span></Link></li>
                <li><Link href="/signup"><span className="hover:text-white transition-colors cursor-pointer">Get Started</span></Link></li>
                <li><a href="mailto:info@credibilityfactoryafrique.com" className="hover:text-white transition-colors">Contact Us</a></li>
              </ul>
            </div>
          </div>

          <div className="border-t border-gray-800 pt-8 flex flex-col md:flex-row items-center justify-between gap-4 text-xs">
            <p>&copy; {new Date().getFullYear()} Credibility Factory Afrique. All rights reserved.</p>
            <div className="flex gap-6">
              <a href="#" className="hover:text-white transition-colors">Privacy Policy</a>
              <a href="#" className="hover:text-white transition-colors">Terms of Service</a>
            </div>
          </div>
        </div>
      </footer>
    </div>
  );
}
