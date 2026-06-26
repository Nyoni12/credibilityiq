<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>@yield('title', 'Sign In') — CredibilityIQ</title>
<script src="https://cdn.tailwindcss.com"></script>
<script>
tailwind.config = {
    theme: {
        extend: {
            colors: {
                brand: { 50:'#EEEFFE',100:'#C8CCF5',200:'#9DA5EC',300:'#717FE3',400:'#4659DA',500:'#1F2192',600:'#191B7A',700:'#131562',800:'#0D0E4A',900:'#070831' },
                accent: { 500:'#A329CC' },
                cfa: { 500:'#00A651' }
            },
            fontFamily: { sans: ['Inter','system-ui','sans-serif'] }
        }
    }
}
</script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="min-h-screen font-sans bg-brand-900 flex">

    {{-- Left: Branding panel --}}
    <div class="hidden lg:flex lg:w-1/2 relative overflow-hidden flex-col items-center justify-center p-12"
         style="background: linear-gradient(135deg, #070831 0%, #1F2192 60%, #A329CC 100%);">
        <div class="absolute inset-0 opacity-10"
             style="background-image: radial-gradient(circle at 2px 2px, white 1px, transparent 0); background-size: 32px 32px;"></div>
        <div class="relative z-10 text-center">
            <div class="w-20 h-20 mx-auto mb-8 rounded-2xl bg-white/10 backdrop-blur-sm border border-white/20 flex items-center justify-center">
                <span class="text-white font-black text-3xl">CIQ</span>
            </div>
            <h1 class="text-4xl font-extrabold text-white mb-4 leading-tight">
                Measure What<br>Matters Most
            </h1>
            <p class="text-brand-200 text-lg leading-relaxed max-w-sm mx-auto">
                Turn stakeholder perception into a clear credibility score — and a roadmap to excellence.
            </p>
            <div class="mt-12 grid grid-cols-3 gap-6 text-center">
                <div><div class="text-3xl font-black text-white">360°</div><div class="text-brand-300 text-sm mt-1">Stakeholder View</div></div>
                <div><div class="text-3xl font-black text-white">Live</div><div class="text-brand-300 text-sm mt-1">Score Tracking</div></div>
                <div><div class="text-3xl font-black text-cfa-400">PDF</div><div class="text-brand-300 text-sm mt-1">Board Reports</div></div>
            </div>
        </div>
    </div>

    {{-- Right: Form panel --}}
    <div class="flex-1 flex flex-col items-center justify-center p-8 bg-gray-50">
        <div class="w-full max-w-md">
            {{-- Mobile logo --}}
            <div class="lg:hidden text-center mb-8">
                <div class="w-14 h-14 mx-auto mb-3 rounded-xl bg-brand-500 flex items-center justify-center">
                    <span class="text-white font-black text-xl">CIQ</span>
                </div>
                <h2 class="text-xl font-bold text-brand-900">CredibilityIQ</h2>
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
