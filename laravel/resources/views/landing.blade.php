<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>CredibilityIQ — Corporate Credibility Scorecard Platform</title>
<meta name="description" content="Measure your company's credibility through stakeholder eyes. Turn perception into a clear score — and a roadmap to excellence.">
<script src="https://cdn.tailwindcss.com"></script>
<script>
tailwind.config = {
    theme: {
        extend: {
            colors: {
                brand: { 50:'#EEEFFE',100:'#C8CCF5',200:'#9DA5EC',300:'#717FE3',400:'#4659DA',500:'#1F2192',600:'#191B7A',700:'#131562',800:'#0D0E4A',900:'#070831' },
                accent: { 400:'#BE2CBA', 500:'#A329CC', 600:'#8821A8' },
                cfa: { 400:'#01AF50', 500:'#00A651', 600:'#008040' }
            },
            fontFamily: { sans: ['Inter','system-ui','sans-serif'] },
            animation: {
                'fade-up': 'fadeUp 0.6s ease both',
                'fade-in': 'fadeIn 0.5s ease both',
                'counter': 'counter 2s ease-out both',
                'float': 'float 6s ease-in-out infinite',
            },
            keyframes: {
                fadeUp: { '0%': { opacity:'0', transform:'translateY(24px)' }, '100%': { opacity:'1', transform:'translateY(0)' } },
                fadeIn: { '0%': { opacity:'0' }, '100%': { opacity:'1' } },
                float: { '0%,100%': { transform:'translateY(0)' }, '50%': { transform:'translateY(-10px)' } },
            }
        }
    }
}
</script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<style>
  [x-cloak] { display: none !important; }

  .reveal { opacity: 0; transform: translateY(30px); transition: opacity 0.65s ease, transform 0.65s ease; }
  .reveal.visible { opacity: 1; transform: translateY(0); }
  .delay-1 { transition-delay: 0.1s; }
  .delay-2 { transition-delay: 0.2s; }
  .delay-3 { transition-delay: 0.3s; }
  .delay-4 { transition-delay: 0.4s; }
  .delay-5 { transition-delay: 0.5s; }

  .hero-overlay {
    background: linear-gradient(to bottom,
      rgba(7,8,49,0.72) 0%,
      rgba(7,8,49,0.45) 40%,
      rgba(7,8,49,0.60) 70%,
      rgba(7,8,49,0.85) 100%
    );
  }

  .glass-card {
    background: rgba(255,255,255,0.08);
    backdrop-filter: blur(16px);
    border: 1px solid rgba(255,255,255,0.15);
  }

  .score-ring-track { fill: none; stroke: rgba(255,255,255,0.15); stroke-width: 8; }
  .score-ring-fill  { fill: none; stroke: #00A651; stroke-width: 8; stroke-linecap: round;
                      stroke-dasharray: 251.2; stroke-dashoffset: 251.2;
                      transform: rotate(-90deg); transform-origin: 50% 50%;
                      transition: stroke-dashoffset 2s ease; }
</style>
</head>
<body class="font-sans antialiased" x-data="{ mobileMenu: false, scrolled: false }"
      @scroll.window="scrolled = window.scrollY > 50">

{{-- ─── NAVBAR ──────────────────────────────────────────────────────────── --}}
<nav :class="scrolled ? 'bg-brand-900/95 backdrop-blur-lg shadow-xl' : 'bg-transparent'"
     class="fixed top-0 inset-x-0 z-50 transition-all duration-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between h-16">
        <a href="/" class="flex items-center gap-2.5">
            <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-brand-400 to-accent-500 flex items-center justify-center text-white font-black text-xs">CIQ</div>
            <span class="text-white font-bold text-base">CredibilityIQ</span>
        </a>
        <div class="hidden md:flex items-center gap-6 text-sm font-medium text-brand-200">
            <a href="#how-it-works" class="hover:text-white transition-colors">How It Works</a>
            <a href="#features"     class="hover:text-white transition-colors">Features</a>
            <a href="#testimonials" class="hover:text-white transition-colors">Testimonials</a>
        </div>
        <div class="hidden md:flex items-center gap-3">
            <a href="{{ route('login') }}"  class="text-brand-200 hover:text-white text-sm font-medium px-4 py-2 rounded-lg hover:bg-white/10 transition-all">Sign In</a>
            <a href="{{ route('signup') }}" class="bg-cfa-500 hover:bg-cfa-600 text-white text-sm font-semibold px-5 py-2 rounded-lg transition-all hover:shadow-lg hover:shadow-cfa-500/30">Get Started Free</a>
        </div>
        <button @click="mobileMenu=!mobileMenu" class="md:hidden text-white p-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path x-show="!mobileMenu" stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                <path x-show="mobileMenu" x-cloak stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>
    {{-- Mobile menu --}}
    <div x-show="mobileMenu" x-cloak class="md:hidden bg-brand-900/98 border-t border-white/10 px-4 py-4 space-y-3">
        <a href="#how-it-works" @click="mobileMenu=false" class="block text-brand-200 hover:text-white py-2 text-sm">How It Works</a>
        <a href="#features"     @click="mobileMenu=false" class="block text-brand-200 hover:text-white py-2 text-sm">Features</a>
        <a href="{{ route('login') }}"  class="block text-brand-200 hover:text-white py-2 text-sm">Sign In</a>
        <a href="{{ route('signup') }}" class="block bg-cfa-500 text-white text-sm font-semibold px-4 py-2.5 rounded-lg text-center">Get Started Free</a>
    </div>
</nav>

{{-- ─── HERO ────────────────────────────────────────────────────────────── --}}
<section class="relative min-h-screen flex items-center justify-center overflow-hidden"
         style="background-image: url('{{ asset('images/hero-bg.jpg') }}'); background-size: cover; background-position: center top; background-repeat: no-repeat;">
    <div class="hero-overlay absolute inset-0"></div>

    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-24 pb-20 text-center">

        {{-- Badge --}}
        <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full text-xs font-semibold text-cfa-400 border border-cfa-500/30 bg-cfa-500/10 mb-8 animate-fade-in">
            <span class="w-1.5 h-1.5 rounded-full bg-cfa-400 animate-pulse"></span>
            Trusted by corporates across Africa
        </div>

        <h1 class="text-4xl sm:text-6xl lg:text-7xl font-black text-white leading-tight mb-6"
            style="text-shadow: 0 2px 20px rgba(0,0,0,0.8), 0 1px 4px rgba(0,0,0,0.9);">
            Your Company's<br>
            <span class="bg-gradient-to-r from-cfa-400 to-accent-400 bg-clip-text text-transparent">Credibility Score</span>
            <br>Starts Here
        </h1>

        <p class="text-lg sm:text-xl text-brand-100/90 max-w-2xl mx-auto mb-10 leading-relaxed"
           style="text-shadow: 0 1px 8px rgba(0,0,0,0.7);">
            Measure stakeholder trust, identify credibility gaps, and generate board-ready reports —
            all in one platform built for African corporates.
        </p>

        <div class="flex flex-col sm:flex-row gap-4 justify-center mb-16">
            <a href="{{ route('signup') }}"
               class="inline-flex items-center gap-2 bg-cfa-500 hover:bg-cfa-600 text-white font-bold px-8 py-4 rounded-xl text-base transition-all hover:shadow-2xl hover:shadow-cfa-500/40 hover:-translate-y-0.5">
                Start Free Assessment
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
            </a>
            <a href="#how-it-works"
               class="inline-flex items-center gap-2 glass-card text-white font-semibold px-8 py-4 rounded-xl text-base hover:bg-white/15 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                See How It Works
            </a>
        </div>

        {{-- Floating stats --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 max-w-3xl mx-auto">
            @foreach([
                ['87%','Avg Score Improvement','cfa'],
                ['360°','Stakeholder View','brand'],
                ['15min','To First Report','accent'],
                ['100%','Confidential','cfa'],
            ] as [$num, $label, $color])
            <div class="glass-card rounded-2xl p-4 text-center">
                <div class="text-2xl font-black {{ $color === 'cfa' ? 'text-cfa-400' : ($color === 'accent' ? 'text-accent-400' : 'text-brand-300') }}">{{ $num }}</div>
                <div class="text-white/70 text-xs mt-1">{{ $label }}</div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Scroll indicator --}}
    <div class="absolute bottom-6 left-1/2 -translate-x-1/2 z-10 animate-bounce">
        <svg class="w-5 h-5 text-white/50" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
    </div>
</section>

{{-- ─── LIVE SCORE DEMO ─────────────────────────────────────── --}}
<section class="bg-brand-900 py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-2 gap-12 items-center">
            <div class="reveal">
                <span class="text-cfa-400 text-sm font-semibold uppercase tracking-widest mb-4 block">Live Example</span>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-white mb-6 leading-tight">
                    From Vague Feelings<br>to a Clear Number
                </h2>
                <p class="text-brand-300 text-base leading-relaxed mb-8">
                    CredibilityIQ aggregates ratings from customers, partners, investors and employees into a
                    single credibility score — broken down by every value your company stands for.
                </p>
                <ul class="space-y-3">
                    @foreach(['Anonymous stakeholder surveys','Weighted value scoring','Financial leakage calculation','Board-ready PDF reports'] as $feat)
                    <li class="flex items-center gap-3 text-brand-200 text-sm">
                        <svg class="w-5 h-5 text-cfa-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        {{ $feat }}
                    </li>
                    @endforeach
                </ul>
            </div>

            {{-- Animated scorecard mockup --}}
            <div class="reveal delay-2" x-data="{ visible: false }" x-intersect.once="visible = true">
                <div class="bg-brand-800/60 border border-white/10 rounded-2xl p-6 backdrop-blur-sm">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <div class="text-white font-bold">Q2 2025 Assessment</div>
                            <div class="text-brand-400 text-xs">Acme Corp · 47 responses</div>
                        </div>
                        <span class="px-2.5 py-1 rounded-full bg-cfa-500/20 text-cfa-400 text-xs font-medium">Excellent</span>
                    </div>
                    {{-- Score ring --}}
                    <div class="flex items-center gap-6 mb-6">
                        <div class="relative w-24 h-24 shrink-0">
                            <svg viewBox="0 0 100 100" class="w-full h-full">
                                <circle cx="50" cy="50" r="40" class="score-ring-track"/>
                                <circle cx="50" cy="50" r="40" class="score-ring-fill"
                                        :style="visible ? 'stroke-dashoffset:' + (251.2*(1-0.83)) : 'stroke-dashoffset:251.2'"/>
                            </svg>
                            <div class="absolute inset-0 flex items-center justify-center">
                                <span class="text-white text-xl font-black">83</span>
                            </div>
                        </div>
                        <div class="flex-1 space-y-2">
                            @foreach([['Integrity','92','cfa'],['Innovation','78','brand'],['Accountability','86','cfa'],['Communication','71','accent']] as [$v,$s,$c])
                            <div>
                                <div class="flex items-center justify-between text-xs text-brand-300 mb-0.5">
                                    <span>{{ $v }}</span><span>{{ $s }}%</span>
                                </div>
                                <div class="h-1.5 bg-white/10 rounded-full overflow-hidden">
                                    <div class="{{ $c === 'cfa' ? 'bg-cfa-500' : ($c === 'brand' ? 'bg-brand-400' : 'bg-accent-400') }} h-full rounded-full transition-all duration-1000"
                                         :style="visible ? 'width:{{ $s }}%' : 'width:0'"></div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="border-t border-white/10 pt-4 grid grid-cols-2 gap-3">
                        <div class="bg-white/5 rounded-lg p-3">
                            <div class="text-brand-400 text-xs mb-1">Est. Leakage</div>
                            <div class="text-red-400 font-bold text-sm">$284,000</div>
                        </div>
                        <div class="bg-white/5 rounded-lg p-3">
                            <div class="text-brand-400 text-xs mb-1">YoY Improvement</div>
                            <div class="text-cfa-400 font-bold text-sm">+12 pts</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ─── HOW IT WORKS ────────────────────────────────────────── --}}
<section id="how-it-works" class="bg-white py-24">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16 reveal">
            <span class="text-brand-500 text-sm font-semibold uppercase tracking-widest mb-3 block">Simple Process</span>
            <h2 class="text-3xl sm:text-4xl font-extrabold text-gray-900">Your Scorecard in 4 Steps</h2>
        </div>
        <div class="grid md:grid-cols-4 gap-8">
            @foreach([
                ['1','Define Values','Map the values your company stands for — integrity, innovation, accountability, and more.','brand'],
                ['2','Share Survey','Send the anonymous survey link to customers, staff, partners, and investors.','accent'],
                ['3','Collect Ratings','Stakeholders score each value from 1–10. No login needed — mobile-friendly.','cfa'],
                ['4','Get Scorecard','Review your weighted credibility score, financial leakage, and improvement plan.','brand'],
            ] as $i => [$n, $title, $desc, $color])
            <div class="reveal delay-{{ $i + 1 }} text-center relative">
                @if($i < 3)
                <div class="hidden md:block absolute top-8 left-1/2 w-full h-0.5 bg-gradient-to-r
                     {{ $color === 'brand' ? 'from-brand-200 to-accent-200' : ($color === 'accent' ? 'from-accent-200 to-cfa-200' : 'from-cfa-200 to-brand-200') }}
                     -z-10"></div>
                @endif
                <div class="w-16 h-16 mx-auto mb-5 rounded-2xl flex items-center justify-center text-white text-xl font-black shadow-lg
                     {{ $color === 'brand' ? 'bg-gradient-to-br from-brand-500 to-brand-600' : ($color === 'accent' ? 'bg-gradient-to-br from-accent-500 to-accent-600' : 'bg-gradient-to-br from-cfa-500 to-cfa-600') }}">
                    {{ $n }}
                </div>
                <h3 class="text-base font-bold text-gray-900 mb-2">{{ $title }}</h3>
                <p class="text-gray-500 text-sm leading-relaxed">{{ $desc }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ─── FEATURES ────────────────────────────────────────────── --}}
<section id="features" class="bg-gray-50 py-24">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16 reveal">
            <span class="text-brand-500 text-sm font-semibold uppercase tracking-widest mb-3 block">Everything You Need</span>
            <h2 class="text-3xl sm:text-4xl font-extrabold text-gray-900">Built for African Boardrooms</h2>
            <p class="text-gray-500 mt-4 max-w-xl mx-auto">Every feature is designed around the real-world needs of corporate credibility management.</p>
        </div>
        <div class="grid md:grid-cols-3 gap-6">
            @foreach([
                ['🎯','360° Stakeholder Surveys','Collect anonymous feedback from every stakeholder group — customers, employees, partners, investors.'],
                ['⚖️','Weighted Value Scoring','Assign financial weight to each company value so the score reflects real business impact.'],
                ['💰','Financial Leakage Analysis','Quantify the revenue at risk from low credibility scores across each company value.'],
                ['📊','Visual Scorecards','Beautiful charts and score rings give executives an instant read on company credibility.'],
                ['📄','Board-Ready PDF Reports','One-click PDF download formatted for board presentations and investor decks.'],
                ['🏢','Multi-Company Management','SuperAdmins can manage multiple client companies from a single control panel.'],
                ['🔒','100% Anonymous Surveys','Stakeholders need no account — just a link. Complete confidentiality guaranteed.'],
                ['📈','Trend Tracking','Run quarterly assessments and track your credibility score over time.'],
                ['🚀','Instant Setup','Start your first assessment in under 15 minutes. No training required.'],
            ] as $i => [$icon, $title, $desc])
            <div class="reveal delay-{{ ($i % 3) + 1 }} bg-white rounded-xl p-6 border border-gray-200 hover:border-brand-200 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 group">
                <div class="w-11 h-11 rounded-xl bg-brand-50 border border-brand-100 flex items-center justify-center text-xl mb-4 group-hover:bg-brand-100 transition-colors">
                    {{ $icon }}
                </div>
                <h3 class="font-bold text-gray-900 mb-2">{{ $title }}</h3>
                <p class="text-gray-500 text-sm leading-relaxed">{{ $desc }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ─── TESTIMONIALS ────────────────────────────────────────── --}}
<section id="testimonials" class="bg-brand-900 py-24">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16 reveal">
            <span class="text-cfa-400 text-sm font-semibold uppercase tracking-widest mb-3 block">Testimonials</span>
            <h2 class="text-3xl sm:text-4xl font-extrabold text-white">Trusted by Leaders</h2>
        </div>
        <div class="grid md:grid-cols-3 gap-6">
            @foreach([
                ['The financial leakage report was eye-opening. We identified $400k in at-risk revenue from a single credibility gap.','Nomvula Dlamini','CEO, Horizons Capital','ND','cfa'],
                ['Running our first 360° survey took 20 minutes. The board received our credibility scorecard the same week.','Tendai Moyo','COO, ZimTech Group','TM','brand'],
                ['CredibilityIQ gave us the language to talk about trust in measurable terms. Our investors love the quarterly reports.','Adaeze Obi','Group MD, Pinnacle Holdings','AO','accent'],
            ] as $i => [$quote, $name, $role, $initials, $color])
            <div class="reveal delay-{{ $i + 1 }} bg-white/5 border border-white/10 rounded-2xl p-6 backdrop-blur-sm hover:bg-white/10 transition-all">
                <div class="flex gap-1 mb-4">
                    @for($s = 0; $s < 5; $s++)
                    <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                    @endfor
                </div>
                <p class="text-brand-200 text-sm leading-relaxed mb-5 italic">"{{ $quote }}"</p>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center text-white text-sm font-bold
                         {{ $color === 'cfa' ? 'bg-cfa-500' : ($color === 'accent' ? 'bg-accent-500' : 'bg-brand-400') }}">
                        {{ $initials }}
                    </div>
                    <div>
                        <div class="text-white font-semibold text-sm">{{ $name }}</div>
                        <div class="text-brand-400 text-xs">{{ $role }}</div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ─── CTA ─────────────────────────────────────────────────── --}}
<section class="bg-gradient-to-br from-cfa-600 via-cfa-500 to-cfa-400 py-20">
    <div class="max-w-3xl mx-auto px-4 text-center reveal">
        <h2 class="text-3xl sm:text-4xl font-extrabold text-white mb-4">
            Ready to Measure Your Credibility?
        </h2>
        <p class="text-white/80 text-lg mb-8">
            Join corporate leaders using CredibilityIQ to build trust, attract investment, and grow.
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('signup') }}"
               class="bg-white text-cfa-700 font-bold px-8 py-4 rounded-xl text-base hover:shadow-2xl hover:-translate-y-0.5 transition-all">
                Create Free Account
            </a>
            <a href="{{ route('login') }}"
               class="border-2 border-white/50 text-white font-semibold px-8 py-4 rounded-xl text-base hover:bg-white/10 transition-all">
                Sign In
            </a>
        </div>
    </div>
</section>

{{-- ─── FOOTER ──────────────────────────────────────────────── --}}
<footer class="bg-brand-900 border-t border-white/10 py-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-2.5">
                <div class="w-7 h-7 rounded-lg bg-gradient-to-br from-brand-400 to-accent-500 flex items-center justify-center text-white font-black text-xs">CIQ</div>
                <span class="text-white font-bold text-sm">CredibilityIQ</span>
            </div>
            <p class="text-brand-400 text-xs text-center">
                © {{ date('Y') }} Credibility Factory Afrique. All rights reserved.
            </p>
            <div class="flex items-center gap-5 text-xs text-brand-400">
                <a href="#" class="hover:text-white transition-colors">Privacy Policy</a>
                <a href="#" class="hover:text-white transition-colors">Terms</a>
                <a href="{{ route('login') }}" class="hover:text-white transition-colors">Sign In</a>
            </div>
        </div>
    </div>
</footer>

<script>
// Intersection Observer for reveal animations
const observer = new IntersectionObserver((entries) => {
    entries.forEach(e => { if (e.isIntersecting) e.target.classList.add('visible'); });
}, { threshold: 0.1 });
document.querySelectorAll('.reveal').forEach(el => observer.observe(el));
</script>
</body>
</html>
