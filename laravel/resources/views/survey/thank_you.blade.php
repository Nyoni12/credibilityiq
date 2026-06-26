<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Thank You — {{ $company->name }}</title>
<script src="https://cdn.tailwindcss.com"></script>
<script>tailwind.config={theme:{extend:{colors:{brand:{500:'#1F2192',900:'#070831'},cfa:{500:'#00A651'}}}}}</script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
</head>
<body class="min-h-screen bg-gray-50 font-sans flex items-center justify-center p-4">
    <div class="max-w-md w-full text-center">
        <div class="w-20 h-20 mx-auto mb-6 rounded-full bg-cfa-500 flex items-center justify-center">
            <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
        </div>
        <h1 class="text-2xl font-extrabold text-gray-900 mb-3">Thank You!</h1>
        <p class="text-gray-500 text-base mb-2">Your feedback has been submitted successfully.</p>
        <p class="text-gray-400 text-sm mb-8">Your response is completely anonymous and will help <strong class="text-gray-600">{{ $company->name }}</strong> improve their credibility.</p>
        <p class="text-xs text-gray-400">Powered by CredibilityIQ · Credibility Factory Afrique</p>
    </div>
</body>
</html>
