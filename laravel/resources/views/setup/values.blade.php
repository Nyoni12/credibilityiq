@extends('layouts.app')
@section('title', 'Values Setup')

@section('content')

{{-- Safe JSON data in a script tag (avoids double-quote collision inside HTML attribute) --}}
<script id="__cfa_existing" type="application/json">
{!! json_encode(
    $existing->map(fn($v) => [
        'financial_weight' => (float) $v->financial_weight,
        'description'      => $v->description ?? '',
    ])->toArray()
) !!}
</script>

<div class="max-w-3xl mx-auto space-y-6" x-data="cfaValuesSetup()">

    {{-- Header --}}
    <div class="flex items-start justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Credibility Building Blocks</h1>
            <p class="text-sm text-gray-500 mt-1">
                Select the values that apply to your organisation. Assign a financial weight (annual $ at risk) to each.
            </p>
        </div>
        <button form="bulk-save-form" type="submit"
                :disabled="count === 0"
                class="px-5 py-2.5 bg-brand-500 text-white rounded-lg text-sm font-semibold hover:bg-brand-600 disabled:opacity-50 transition-colors whitespace-nowrap">
            Save (<span x-text="count"></span> selected)
        </button>
    </div>

    @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-700 text-sm px-4 py-3 rounded-lg">
        ✓ {{ session('success') }}
    </div>
    @endif

    {{-- Progress bar --}}
    <div class="flex items-center gap-3 bg-brand-50 rounded-xl px-4 py-3 border border-brand-100">
        <div class="flex-1 h-2 bg-brand-100 rounded-full overflow-hidden">
            <div class="h-2 bg-brand-500 rounded-full transition-all duration-300"
                 :style="`width:${(count/12)*100}%`"></div>
        </div>
        <span class="text-sm font-semibold text-brand-600 whitespace-nowrap">
            <span x-text="count"></span> / 12
        </span>
    </div>

    {{-- Bulk-save form --}}
    <form id="bulk-save-form" method="POST" action="{{ route('values.bulk-save') }}">
        @csrf

        {{-- Hidden inputs driven by Alpine state --}}
        @foreach($cfaValues as $idx => $val)
        <input type="hidden" name="values[{{ $idx }}][name]"             value="{{ $val['name'] }}">
        <input type="hidden" name="values[{{ $idx }}][selected]"         x-bind:value="isSelected('{{ addslashes($val['name']) }}') ? '1' : '0'">
        <input type="hidden" name="values[{{ $idx }}][financial_weight]" x-bind:value="getWeight('{{ addslashes($val['name']) }}')">
        <input type="hidden" name="values[{{ $idx }}][description]"      x-bind:value="getDesc('{{ addslashes($val['name']) }}')">
        @endforeach

        {{-- Value cards --}}
        <div class="space-y-3">
            @foreach($cfaValues as $idx => $val)
            @php $name = $val['name']; $hint = $val['hint']; $js = addslashes($name); @endphp

            <div :style="isSelected('{{ $js }}')
                             ? 'border-color:#1F2192;background:#fff;box-shadow:0 1px 3px rgba(0,0,0,.08)'
                             : 'border-color:#f3f4f6;background:#f9fafb'"
                 style="border-width:2px;border-style:solid;border-radius:12px;transition:all .15s">

                {{-- Header row — click to toggle --}}
                <div class="flex items-center gap-3 px-4 py-3 cursor-pointer select-none"
                     @click="toggle('{{ $js }}')">

                    {{-- Checkbox --}}
                    <div :style="isSelected('{{ $js }}')
                                     ? 'background:#1F2192;border-color:#1F2192'
                                     : 'background:#fff;border-color:#d1d5db'"
                         style="width:20px;height:20px;border-radius:5px;border-width:2px;border-style:solid;
                                display:flex;align-items:center;justify-content:center;flex-shrink:0;transition:all .15s">
                        <svg x-show="isSelected('{{ $js }}')" style="width:12px;height:12px;color:#fff" fill="none"
                             viewBox="0 0 24 24" stroke="white" stroke-width="3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>

                    {{-- Index --}}
                    <span class="text-xs font-bold text-gray-300 w-6 text-center flex-shrink-0">
                        {{ str_pad($idx + 1, 2, '0', STR_PAD_LEFT) }}
                    </span>

                    {{-- Name + hint --}}
                    <div class="flex-1 min-w-0">
                        <p :style="isSelected('{{ $js }}') ? 'color:#111827' : 'color:#6b7280'"
                           class="text-sm font-semibold transition-colors">{{ $name }}</p>
                        <p class="text-xs text-gray-400 mt-0.5">{{ $hint }}</p>
                    </div>

                    <span x-show="isSelected('{{ $js }}')"
                          style="background:#EEF0FD;color:#1F2192;font-size:11px;padding:2px 10px;border-radius:999px;font-weight:600;flex-shrink:0">
                        Selected
                    </span>
                </div>

                {{-- Expanded inputs --}}
                <div x-show="isSelected('{{ $js }}')" x-cloak
                     class="border-t border-gray-100 px-4 pb-4 pt-3 grid grid-cols-2 gap-3"
                     @click.stop>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">
                            Annual $ at Risk <span class="text-red-400">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm font-medium">$</span>
                            <input type="number" min="0" step="1000" placeholder="0"
                                   :value="getWeight('{{ $js }}')"
                                   @input="setWeight('{{ $js }}', $event.target.value)"
                                   class="w-full border border-gray-200 rounded-lg pl-7 pr-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">
                            Description <span class="text-gray-300">(optional)</span>
                        </label>
                        <input type="text" placeholder="How this value shows up…"
                               :value="getDesc('{{ $js }}')"
                               @input="setDesc('{{ $js }}', $event.target.value)"
                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="flex items-center justify-between text-xs text-gray-400 pb-6">
            <span><span x-text="count"></span> of 12 values selected</span>
            <span>Financial weights = total annual budget at risk per value</span>
        </div>
    </form>
</div>

<script>
function cfaValuesSetup() {
    // Parse initial state from the safe <script type="application/json"> tag
    let existing = {};
    try {
        existing = JSON.parse(document.getElementById('__cfa_existing').textContent || '{}');
    } catch(e) { console.warn('Could not parse existing values', e); }

    return {
        selected: existing,

        get count() { return Object.keys(this.selected).length; },

        toggle(name) {
            const next = Object.assign({}, this.selected);
            if (next[name] !== undefined) {
                delete next[name];
            } else {
                next[name] = { financial_weight: 0, description: '' };
            }
            this.selected = next;   // reassign so Alpine detects the change
        },

        isSelected(name) { return this.selected[name] !== undefined; },
        getWeight(name)   { return (this.selected[name] ?? {}).financial_weight ?? 0; },
        getDesc(name)     { return (this.selected[name] ?? {}).description ?? ''; },

        setWeight(name, val) {
            if (this.selected[name]) {
                this.selected = Object.assign({}, this.selected, {
                    [name]: Object.assign({}, this.selected[name], { financial_weight: parseFloat(val) || 0 })
                });
            }
        },
        setDesc(name, val) {
            if (this.selected[name]) {
                this.selected = Object.assign({}, this.selected, {
                    [name]: Object.assign({}, this.selected[name], { description: val })
                });
            }
        },
    };
}
</script>
@endsection
