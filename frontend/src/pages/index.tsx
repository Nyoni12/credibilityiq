import { useEffect, useRef, useState } from 'react';
import Link from 'next/link';
import { useAuth } from '@/context/AuthContext';
import { useRouter } from 'next/router';

// ── Scroll-reveal hook ─────────────────────────────────────────────
function useReveal() {
  useEffect(() => {
    const els = document.querySelectorAll('.reveal, .reveal-left, .reveal-right');
    const obs = new IntersectionObserver(
      (entries) => entries.forEach((e) => { if (e.isIntersecting) e.target.classList.add('visible'); }),
      { threshold: 0.12 }
    );
    els.forEach((el) => obs.observe(el));
    return () => obs.disconnect();
  }, []);
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
        <div className="w-14 h-14 rounded-2xl bg-gradient-to-br from-brand-500 to-indigo-400 flex items-center justify-center text-2xl mb-5 shadow-lg group-hover:scale-110 transition-transform duration-300">
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
      <div className="w-16 h-16 rounded-full bg-gradient-to-br from-brand-500 to-indigo-500 flex items-center justify-center text-white text-2xl font-black mb-5 shadow-xl animate-pulse-glow">
        {num}
      </div>
      <h3 className="text-lg font-bold text-gray-900 mb-2">{title}</h3>
      <p className="text-gray-500 text-sm leading-relaxed max-w-xs">{desc}</p>
    </div>
  );
}

// ── Pricing card ──────────────────────────────────────────────────
function PricingCard({ tier, price, features, featured = false, delay = '' }: {
  tier: string; price: string; features: string[]; featured?: boolean; delay?: string;
}) {
  return (
    <div className={`reveal ${delay} relative rounded-3xl p-8 flex flex-col transition-all duration-400 hover:-translate-y-2
      ${featured
        ? 'bg-gradient-to-b from-brand-500 to-indigo-600 text-white shadow-2xl shadow-brand-500/40 scale-105'
        : 'bg-white border border-gray-100 shadow-lg hover:shadow-2xl'
      }`}
    >
      {featured && (
        <div className="absolute -top-4 left-1/2 -translate-x-1/2 bg-yellow-400 text-yellow-900 text-xs font-black px-4 py-1.5 rounded-full uppercase tracking-widest">
          Most Popular
        </div>
      )}
      <p className={`text-sm font-semibold uppercase tracking-widest mb-2 ${featured ? 'text-brand-100' : 'text-brand-500'}`}>{tier}</p>
      <div className={`text-4xl font-black mb-1 ${featured ? 'text-white' : 'text-gray-900'}`}>{price}</div>
      <p className={`text-sm mb-8 ${featured ? 'text-brand-100' : 'text-gray-400'}`}>per month</p>
      <ul className="space-y-3 mb-8 flex-1">
        {features.map((f) => (
          <li key={f} className={`flex items-center gap-2 text-sm ${featured ? 'text-brand-50' : 'text-gray-600'}`}>
            <span className={`flex-shrink-0 w-5 h-5 rounded-full flex items-center justify-center text-xs font-bold
              ${featured ? 'bg-white/20 text-white' : 'bg-brand-100 text-brand-600'}`}>✓</span>
            {f}
          </li>
        ))}
      </ul>
      <Link href="/login">
        <button className={`w-full py-3 rounded-xl font-bold text-sm transition-all duration-300
          ${featured
            ? 'bg-white text-brand-600 hover:bg-brand-50 shimmer-btn'
            : 'bg-brand-500 text-white hover:bg-brand-600 shimmer-btn'
          }`}>
          Get Started
        </button>
      </Link>
    </div>
  );
}

// ── Main landing page ──────────────────────────────────────────────
export default function LandingPage() {
  const { user, loading } = useAuth();
  const router = useRouter();
  const [scrolled, setScrolled] = useState(false);
  const [menuOpen, setMenuOpen] = useState(false);
  useReveal();

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
          <div className="flex items-center gap-3">
            <div className="w-9 h-9 rounded-xl bg-gradient-to-br from-brand-500 to-indigo-500 flex items-center justify-center shadow-lg">
              <span className="text-white font-black text-sm">CQ</span>
            </div>
            <span className={`font-black text-xl tracking-tight transition-colors ${scrolled ? 'text-brand-700' : 'text-white'}`}>
              CredibilityIQ
            </span>
          </div>

          <div className="hidden md:flex items-center gap-8">
            {['Features', 'How It Works', 'Pricing'].map((item) => (
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
            <Link href="/login">
              <button className="px-5 py-2 text-sm font-bold rounded-xl bg-white text-brand-600 hover:bg-brand-50 shadow-lg hover:shadow-xl transition-all duration-300 shimmer-btn">
                Get Started
              </button>
            </Link>
          </div>
        </div>
      </nav>

      {/* ── Hero ────────────────────────────────────────────── */}
      <section className="relative min-h-screen gradient-bg flex items-center justify-center overflow-hidden">

        {/* Animated background blobs */}
        <div className="absolute inset-0 overflow-hidden pointer-events-none">
          <div className="absolute -top-40 -left-40 w-96 h-96 bg-indigo-500/30 rounded-full animate-morph animate-float blur-3xl" />
          <div className="absolute top-1/3 -right-32 w-80 h-80 bg-purple-500/25 rounded-full animate-morph animate-float-rev blur-3xl" />
          <div className="absolute -bottom-20 left-1/3 w-72 h-72 bg-blue-500/20 rounded-full animate-morph animate-float blur-3xl" style={{ animationDelay: '2s' }} />

          {/* Grid overlay */}
          <div className="absolute inset-0 opacity-10"
            style={{ backgroundImage: 'linear-gradient(rgba(255,255,255,.1) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,.1) 1px,transparent 1px)', backgroundSize: '60px 60px' }} />

          {/* Floating orbs */}
          {[...Array(6)].map((_, i) => (
            <div key={i} className="absolute rounded-full bg-white/5 border border-white/10 animate-float"
              style={{
                width: `${40 + i * 20}px`, height: `${40 + i * 20}px`,
                left: `${10 + i * 15}%`, top: `${15 + (i % 3) * 25}%`,
                animationDelay: `${i * 0.8}s`, animationDuration: `${5 + i}s`,
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
            CredibilityIQ quantifies how well your staff actually live your company values —
            and converts every performance gap into a precise financial loss figure.
          </p>

          {/* CTAs */}
          <div className="animate-fadeInUp flex flex-col sm:flex-row items-center justify-center gap-4 mb-16" style={{ animationDelay: '0.4s' }}>
            <Link href="/login">
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
                    <span className="text-gray-400 text-xs">credibilityiq.com/scorecard</span>
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
        style={{ background: 'linear-gradient(135deg, #0f0c29 0%, #302b63 50%, #24243e 100%)' }}>
        <div className="absolute inset-0 pointer-events-none">
          <div className="absolute top-0 right-0 w-96 h-96 bg-indigo-500/10 rounded-full blur-3xl" />
          <div className="absolute bottom-0 left-0 w-80 h-80 bg-purple-500/10 rounded-full blur-3xl" />
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
            <div className="hidden md:block absolute top-8 left-1/6 right-1/6 h-0.5 bg-gradient-to-r from-brand-500 via-indigo-400 to-purple-500 opacity-40" style={{ left: '18%', right: '18%' }} />

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
              { quote: 'We discovered we were losing over $400,000 a year to the gap between our stated values and actual staff behaviour. CredibilityIQ made that visible in a single afternoon.', name: 'Sarah M.', role: 'Chief People Officer, Nexus Financial' },
              { quote: 'The anonymous survey format got us responses we never would have collected in a town hall. The honesty of the data was eye-opening for our executive team.', name: 'James D.', role: 'L&D Director, PeakLogix Solutions' },
              { quote: 'Within a week of running the assessment, we had a targeted training plan aligned to our biggest financial risk areas. The ROI conversation is now so much easier.', name: 'Thandiwe K.', role: 'Head of HR, BrightMinds Academy' },
            ].map((t, i) => (
              <div key={i} className={`reveal delay-${(i + 1) * 200} bg-white rounded-2xl p-7 shadow-lg border border-gray-100 hover:shadow-xl hover:-translate-y-1 transition-all duration-300`}>
                <div className="text-brand-500 text-3xl mb-4 font-serif leading-none">"</div>
                <p className="text-gray-600 text-sm leading-relaxed mb-6 italic">{t.quote}</p>
                <div className="flex items-center gap-3">
                  <div className="w-10 h-10 rounded-full bg-gradient-to-br from-brand-500 to-indigo-400 flex items-center justify-center text-white font-bold text-sm">
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

      {/* ── Pricing ──────────────────────────────────────────── */}
      <section id="pricing" className="py-28 bg-white">
        <div className="max-w-6xl mx-auto px-6">
          <div className="text-center mb-16">
            <p className="reveal text-brand-500 font-semibold uppercase tracking-widest text-sm mb-3">Simple Pricing</p>
            <h2 className="reveal delay-100 text-4xl md:text-5xl font-black text-gray-900 mb-4">
              Choose the Plan That<br />
              <span className="gradient-text">Fits Your Company</span>
            </h2>
            <p className="reveal delay-200 text-gray-400 text-lg">All plans include anonymous assessments, financial reporting, and PDF exports.</p>
          </div>

          <div className="grid md:grid-cols-3 gap-8 items-start">
            <PricingCard delay="delay-100" tier="Starter" price="$299"
              features={['1 Company', 'Up to 6 values', '50 staff responses/mo', 'PDF scorecard export', 'Email support']} />
            <PricingCard delay="delay-200" tier="Professional" price="$699" featured
              features={['1 Company', 'Up to 12 values', 'Unlimited responses', 'Financial leakage report', 'Training flag engine', 'Priority support']} />
            <PricingCard delay="delay-300" tier="Enterprise" price="Custom"
              features={['Unlimited companies', 'Custom value frameworks', 'White-label branding', 'API access', 'Dedicated account manager', 'SLA guarantee']} />
          </div>
        </div>
      </section>

      {/* ── Final CTA ────────────────────────────────────────── */}
      <section className="py-28 relative overflow-hidden gradient-bg">
        <div className="absolute inset-0 pointer-events-none overflow-hidden">
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
            <Link href="/login">
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
              <div className="flex items-center gap-3 mb-4">
                <div className="w-9 h-9 rounded-xl bg-gradient-to-br from-brand-500 to-indigo-500 flex items-center justify-center">
                  <span className="text-white font-black text-sm">CQ</span>
                </div>
                <span className="text-white font-black text-xl">CredibilityIQ</span>
              </div>
              <p className="text-sm leading-relaxed max-w-xs">
                The corporate credibility scorecard platform that converts values gaps into financial insights and targeted training action plans.
              </p>
            </div>
            <div>
              <p className="text-white font-semibold mb-4 text-sm uppercase tracking-wider">Platform</p>
              <ul className="space-y-3 text-sm">
                {['Features', 'How It Works', 'Pricing', 'Security'].map((l) => (
                  <li key={l}><a href="#" className="hover:text-white transition-colors">{l}</a></li>
                ))}
              </ul>
            </div>
            <div>
              <p className="text-white font-semibold mb-4 text-sm uppercase tracking-wider">Account</p>
              <ul className="space-y-3 text-sm">
                <li><Link href="/login"><span className="hover:text-white transition-colors cursor-pointer">Sign In</span></Link></li>
                <li><Link href="/login"><span className="hover:text-white transition-colors cursor-pointer">Get Started</span></Link></li>
                <li><a href="mailto:admin@credibilityiq.com" className="hover:text-white transition-colors">Contact Us</a></li>
              </ul>
            </div>
          </div>

          <div className="border-t border-gray-800 pt-8 flex flex-col md:flex-row items-center justify-between gap-4 text-xs">
            <p>&copy; {new Date().getFullYear()} CredibilityIQ. All rights reserved.</p>
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
