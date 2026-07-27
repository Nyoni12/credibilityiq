<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Access Denied | CredibilityIQ</title>
<script src="https://cdn.tailwindcss.com"></script>
<script>tailwind.config={theme:{extend:{colors:{brand:{500:'#1F2192',600:'#191a7a',900:'#070831'}}}}}</script>
</head>
<body class="min-h-screen bg-gray-50 flex flex-col items-center justify-center p-6 font-sans">
    <div class="max-w-md w-full text-center">
        <div class="w-20 h-20 mx-auto mb-6 rounded-full bg-red-100 flex items-center justify-center">
            <svg class="w-10 h-10 text-red-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
        </div>
        <p class="text-7xl font-extrabold text-red-500 mb-4">403</p>
        <h1 class="text-2xl font-bold text-gray-900 mb-3">Access denied</h1>
        <p class="text-gray-500 mb-8">You don't have permission to access this resource. Please contact your administrator if you believe this is an error.</p>
        <div class="flex justify-center gap-3">
            <a href="/" class="px-5 py-2.5 bg-brand-500 text-white rounded-lg text-sm font-medium hover:bg-brand-600 transition-colors">Go home</a>
            <button onclick="history.back()" class="px-5 py-2.5 border border-gray-300 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition-colors">Go back</button>
        </div>
        <p class="mt-8 text-xs text-gray-400">CredibilityIQ · Credibility Factory Afrique</p>
    </div>
</body>
</html>
