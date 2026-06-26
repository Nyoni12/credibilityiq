<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Credibility Survey — {{ $company->name }}</title>
<script src="https://cdn.tailwindcss.com"></script>
<script>
tailwind.config = {
    theme: {
        extend: {
            colors: {
                brand: { 500:'#1F2192', 600:'#191B7A', 900:'#070831' },
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
<style>
  [x-cloak]  { display: none !important; }
  .rating-btn { transition: all 0.12s ease; }
  .rating-btn:hover, .rating-btn.selected { transform: scale(1.1); }
</style>
</head>
<body class="min-h-screen bg-gray-50 font-sans" x-data="survey()">

    {{-- Header --}}
    <div class="bg-brand-900 text-white px-4 py-5 text-center">
        <div class="max-w-lg mx-auto">
            <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center text-white font-black text-sm mx-auto mb-3">CIQ</div>
            <h1 class="text-lg font-bold">{{ $company->name }}</h1>
            <p class="text-brand-300 text-sm">Stakeholder Credibility Survey</p>
            <p class="text-brand-400 text-xs mt-2">{{ $assessment->title }}</p>
        </div>
    </div>

    {{-- Progress bar --}}
    <div class="bg-white border-b border-gray-100 sticky top-0 z-10">
        <div class="max-w-lg mx-auto px-4 py-3">
            <div class="flex items-center justify-between text-xs text-gray-500 mb-2">
                <span x-text="`Question ${currentIndex + 1} of {{ count($values) }}`"></span>
                <span x-text="`${Math.round(progress)}% complete`"></span>
            </div>
            <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                <div class="h-full bg-brand-500 rounded-full transition-all duration-500"
                     :style="`width: ${progress}%`"></div>
            </div>
        </div>
    </div>

    {{-- Survey form --}}
    <form method="POST" action="{{ route('survey.submit', $token) }}" x-ref="form" @submit="return validateAll()">
        @csrf
        <div class="max-w-lg mx-auto px-4 py-8 space-y-4">

            {{-- Role question --}}
            <div x-show="currentIndex === -1">
                <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm">
                    <h2 class="text-lg font-bold text-gray-900 mb-2">Your Role</h2>
                    <p class="text-gray-500 text-sm mb-5">This survey is completely anonymous. Please share your relationship with {{ $company->name }}.</p>
                    <select name="respondent_role"
                            class="w-full px-4 py-3 rounded-xl border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 bg-white">
                        <option value="">Select your relationship…</option>
                        @foreach(['Customer / Client','Employee','Business Partner','Investor / Shareholder','Supplier','Community Member','Other'] as $r)
                        <option value="{{ $r }}">{{ $r }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mt-4 flex justify-end">
                    <button type="button" @click="next()"
                            class="bg-brand-500 hover:bg-brand-600 text-white font-semibold px-8 py-3 rounded-xl text-sm transition-all">
                        Start Survey →
                    </button>
                </div>
            </div>

            {{-- Value questions --}}
            @foreach($values as $i => $value)
            <div x-show="currentIndex === {{ $i }}" x-cloak>
                <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm">
                    <div class="flex items-center gap-2 mb-1">
                        <span class="w-6 h-6 rounded-full bg-brand-50 flex items-center justify-center text-brand-500 text-xs font-bold">{{ $i+1 }}</span>
                        <span class="text-xs text-gray-400 uppercase tracking-wider font-medium">Company Value</span>
                    </div>
                    <h2 class="text-xl font-bold text-gray-900 mt-3 mb-2">{{ $value->name }}</h2>
                    @if($value->description)
                    <p class="text-gray-500 text-sm mb-5 leading-relaxed">{{ $value->description }}</p>
                    @else
                    <p class="text-gray-500 text-sm mb-5">How strongly does {{ $company->name }} demonstrate this value?</p>
                    @endif

                    <p class="text-xs font-medium text-gray-500 mb-3 text-center">Rate from 1 (Very Poor) to 10 (Excellent)</p>

                    {{-- Rating buttons --}}
                    <div class="grid grid-cols-5 gap-2 mb-2">
                        @for($r = 1; $r <= 10; $r++)
                        <label class="cursor-pointer">
                            <input type="radio" name="scores[{{ $value->id }}]" value="{{ $r }}"
                                   class="sr-only" @change="setScore({{ $value->id }}, {{ $r }})">
                            <div :class="scores[{{ $value->id }}] === {{ $r }} ? 'bg-brand-500 text-white border-brand-500 scale-110 shadow-lg shadow-brand-500/30' : '{{ $r <= 3 ? 'border-red-200 text-red-500 hover:bg-red-50' : ($r <= 6 ? 'border-yellow-200 text-yellow-600 hover:bg-yellow-50' : 'border-green-200 text-green-600 hover:bg-green-50') }}'"
                                 class="rating-btn w-full aspect-square rounded-xl border-2 flex items-center justify-center font-bold text-base transition-all">
                                {{ $r }}
                            </div>
                        </label>
                        @endfor
                    </div>
                    <div class="flex justify-between text-xs text-gray-400 px-1">
                        <span>Very Poor</span>
                        <span>Excellent</span>
                    </div>

                    {{-- Selected score display --}}
                    <div x-show="scores[{{ $value->id }}]" x-cloak
                         class="mt-4 flex items-center gap-2 px-4 py-2.5 bg-brand-50 rounded-xl">
                        <svg class="w-4 h-4 text-brand-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        <span class="text-sm text-brand-700">You rated <strong x-text="scores[{{ $value->id }}]"></strong>/10</span>
                    </div>
                    <div x-show="!scores[{{ $value->id }}]"
                         class="mt-4 px-4 py-2.5 bg-gray-50 rounded-xl text-xs text-gray-400 text-center">
                        Please select a rating above
                    </div>
                </div>

                <div class="mt-4 flex items-center justify-between">
                    <button type="button" @click="prev()"
                            class="border border-gray-300 text-gray-600 hover:text-gray-800 font-medium px-6 py-2.5 rounded-xl text-sm hover:bg-gray-50 transition-all">
                        ← Back
                    </button>
                    @if($i < count($values) - 1)
                    <button type="button" @click="next({{ $value->id }})"
                            :disabled="!scores[{{ $value->id }}]"
                            :class="scores[{{ $value->id }}] ? 'bg-brand-500 hover:bg-brand-600 text-white' : 'bg-gray-200 text-gray-400 cursor-not-allowed'"
                            class="font-semibold px-8 py-2.5 rounded-xl text-sm transition-all">
                        Next →
                    </button>
                    @else
                    <button type="submit"
                            :disabled="!scores[{{ $value->id }}] || submitting"
                            :class="scores[{{ $value->id }}] && !submitting ? 'bg-cfa-500 hover:bg-cfa-600 text-white' : 'bg-gray-200 text-gray-400 cursor-not-allowed'"
                            class="font-bold px-8 py-2.5 rounded-xl text-sm transition-all">
                        <span x-show="!submitting">Submit Survey ✓</span>
                        <span x-show="submitting" x-cloak>Submitting…</span>
                    </button>
                    @endif
                </div>
            </div>
            @endforeach

        </div>
    </form>

    {{-- Footer --}}
    <div class="text-center text-xs text-gray-400 pb-8 px-4">
        <p>This survey is completely anonymous. No personal data is collected.</p>
        <p class="mt-1">Powered by <strong>CredibilityIQ</strong> · Credibility Factory Afrique</p>
    </div>

</body>
<script>
function survey() {
    return {
        currentIndex: -1,
        scores: {},
        submitting: false,

        get progress() {
            if (this.currentIndex < 0) return 0;
            const rated = Object.keys(this.scores).length;
            return (rated / {{ count($values) }}) * 100;
        },

        setScore(valueId, score) {
            this.scores[valueId] = score;
        },

        next(requireValueId = null) {
            if (requireValueId && !this.scores[requireValueId]) return;
            this.currentIndex++;
            window.scrollTo({ top: 0, behavior: 'smooth' });
        },

        prev() {
            this.currentIndex = Math.max(-1, this.currentIndex - 1);
            window.scrollTo({ top: 0, behavior: 'smooth' });
        },

        validateAll() {
            const valueIds = [{{ $values->pluck('id')->implode(',') }}];
            for (const id of valueIds) {
                if (!this.scores[id]) {
                    alert('Please rate all values before submitting.');
                    return false;
                }
            }
            this.submitting = true;
            return true;
        }
    }
}
</script>
</html>
