<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Survey Closed</title>
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body class="min-h-screen bg-gray-50 font-sans flex items-center justify-center p-4">
    <div class="max-w-md w-full text-center">
        <div class="w-16 h-16 mx-auto mb-6 rounded-full bg-gray-200 flex items-center justify-center">
            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <h1 class="text-xl font-bold text-gray-800 mb-2">Survey Not Active</h1>
        <p class="text-gray-500 text-sm">There is no open survey for <strong>{{ $company->name }}</strong> at this time. Please check with your contact for an updated link.</p>
    </div>
</body>
</html>
