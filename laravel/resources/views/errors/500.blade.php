<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Server Error | CredibilityIQ</title>
<script src="https://cdn.tailwindcss.com"></script>
<script>tailwind.config={theme:{extend:{colors:{brand:{500:'#1F2192',600:'#191a7a',900:'#070831'}}}}}</script>
</head>
<body class="min-h-screen bg-gray-50 flex flex-col items-center justify-center p-6 font-sans">
    <div class="max-w-md w-full text-center">
        <p class="text-7xl font-extrabold text-gray-300 mb-4">500</p>
        <h1 class="text-2xl font-bold text-gray-900 mb-3">Something went wrong</h1>
        <p class="text-gray-500 mb-8">Our server encountered an unexpected error. Our team has been notified. Please try again in a few moments.</p>
        <div class="flex justify-center gap-3">
            <a href="/" class="px-5 py-2.5 bg-brand-500 text-white rounded-lg text-sm font-medium hover:bg-brand-600 transition-colors">Go home</a>
            <button onclick="location.reload()" class="px-5 py-2.5 border border-gray-300 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition-colors">Try again</button>
        </div>
        <p class="mt-8 text-xs text-gray-400">CredibilityIQ · Credibility Factory Afrique</p>
    </div>
</body>
</html>
