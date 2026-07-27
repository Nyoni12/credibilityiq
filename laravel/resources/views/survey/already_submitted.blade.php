<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Already Submitted | {{ $company->name }}</title>
<script src="https://cdn.tailwindcss.com"></script>
<script>tailwind.config={theme:{extend:{colors:{brand:{500:'#1F2192',900:'#070831'},cfa:{500:'#00A651'}}}}}</script>
</head>
<body class="min-h-screen bg-gray-50 font-sans flex items-center justify-center p-4">
    <div class="max-w-md w-full text-center">
        <div class="w-20 h-20 mx-auto mb-6 rounded-full bg-amber-100 flex items-center justify-center">
            <svg class="w-10 h-10 text-amber-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>
        </div>
        <h1 class="text-2xl font-extrabold text-gray-900 mb-3">Already Submitted</h1>
        <p class="text-gray-500 text-base mb-2">Your feedback has already been recorded for this assessment.</p>
        <p class="text-gray-400 text-sm mb-8">Only one response per device is allowed to ensure the integrity of the survey for <strong class="text-gray-600">{{ $company->name }}</strong>.</p>
        <p class="text-xs text-gray-400">Powered by CredibilityIQ · Credibility Factory Afrique</p>
    </div>
</body>
</html>
