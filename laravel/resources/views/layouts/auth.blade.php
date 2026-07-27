<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>@yield('title', 'Sign In') | CredibilityIQ</title>
<link rel="icon" type="image/png" href="/images/logo.png">
<link rel="preload" href="/fonts/inter-var.woff2" as="font" type="font/woff2" crossorigin>
<link rel="stylesheet" href="/css/app.css">
<script defer src="/js/alpine.min.js"></script>
</head>
<body class="min-h-screen font-sans bg-brand-900 flex">

    {{-- Left: Branding panel --}}
    <div class="hidden lg:flex lg:w-1/2 relative overflow-hidden flex-col items-center justify-center p-12"
         style="background-image:url('/images/hero-bg.jpg');background-size:cover;background-position:center 60%;">
        {{-- Dark + brand tint overlay --}}
        <div class="absolute inset-0" style="background:linear-gradient(to bottom,rgba(3,8,24,.80) 0%,rgba(3,8,24,.65) 45%,rgba(3,8,24,.85) 100%),linear-gradient(to right,rgba(31,33,146,.40) 0%,transparent 70%)"></div>
        {{-- Subtle dot grid --}}
        <div class="absolute inset-0 opacity-[0.06]"
             style="background-image:radial-gradient(circle at 2px 2px,white 1px,transparent 0);background-size:32px 32px;"></div>
        <div class="relative z-10 text-center">
            <div style="display:inline-block;background:#ffffff;border-radius:16px;padding:14px 28px;box-shadow:0 8px 32px rgba(0,0,0,0.30);margin-bottom:2rem;">
                <img src="/images/logo.png" alt="Credibility Factory Afrique" style="height:56px;width:auto;display:block;">
            </div>
            <h1 class="text-4xl font-extrabold text-white mb-4 leading-tight">
                Measure What<br>Matters Most
            </h1>
            <p class="text-white/70 text-lg leading-relaxed max-w-sm mx-auto">
                Turn stakeholder perception into a clear credibility score and a roadmap to excellence.
            </p>
            <div class="mt-12 grid grid-cols-3 gap-6 text-center">
                <div><div class="text-3xl font-black text-white">360°</div><div class="text-white/50 text-sm mt-1">Stakeholder View</div></div>
                <div><div class="text-3xl font-black text-white">Live</div><div class="text-white/50 text-sm mt-1">Score Tracking</div></div>
                <div><div class="text-3xl font-black text-cfa-400">PDF</div><div class="text-white/50 text-sm mt-1">Board Reports</div></div>
            </div>
        </div>
    </div>

    {{-- Right: Form panel --}}
    <div class="flex-1 flex flex-col items-center justify-center p-8 bg-gray-50">
        <div class="w-full max-w-md">
            {{-- Mobile logo --}}
            <div class="lg:hidden text-center mb-8">
                <div style="display:inline-block;background:#ffffff;border-radius:14px;padding:12px 24px;box-shadow:0 4px 16px rgba(0,0,0,0.10);margin-bottom:8px;">
                    <img src="/images/logo.png" alt="Credibility Factory Afrique" style="height:48px;width:auto;display:block;">
                </div>
                <p class="text-sm text-gray-500 mt-2">Corporate Credibility Scorecard Platform</p>
            </div>

            @yield('form')

            @if(session('success'))
            <div class="mt-4 p-3 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm">
                {{ session('success') }}
            </div>
            @endif
        </div>
    </div>

</body>
</html>
