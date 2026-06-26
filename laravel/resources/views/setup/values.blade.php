@extends('layouts.app')
@section('title', 'Company Values')
@section('page-title', 'Company Values Setup')

@section('content')
<div class="space-y-6" x-data="{ showAdd: false, editId: null, totalWeight: {{ $values->sum('weight_percentage') }} }">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <p class="text-sm text-gray-500">Define what your company stands for. These values are rated in every assessment.</p>
            <div class="mt-2 flex items-center gap-2">
                <div class="flex-1 max-w-[200px] h-2 bg-gray-100 rounded-full overflow-hidden">
                    <div class="h-full rounded-full transition-all duration-500"
                         :class="totalWeight > 100 ? 'bg-red-500' : totalWeight === 100 ? 'bg-cfa-500' : 'bg-brand-400'"
                         :style="`width: ${Math.min(totalWeight, 100)}%`"></div>
                </div>
                <span class="text-xs" :class="totalWeight > 100 ? 'text-red-600 font-bold' : totalWeight === 100 ? 'text-cfa-600 font-bold' : 'text-gray-500'">
                    <span x-text="totalWeight"></span>% of 100%
                    <template x-if="totalWeight === 100"><span> ✓</span></template>
                    <template x-if="totalWeight > 100"><span> — exceeds 100%!</span></template>
                </span>
            </div>
        </div>
        <button @click="showAdd=true"
                class="inline-flex items-center gap-2 bg-brand-500 hover:bg-brand-600 text-white font-semibold px-5 py-2.5 rounded-xl text-sm transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            Add Value
        </button>
    </div>

    {{-- Tips --}}
    @if($values->isEmpty())
    <div class="bg-blue-50 border border-blue-200 rounded-2xl p-5">
        <h4 class="font-semibold text-blue-900 text-sm mb-2">Getting Started with Company Values</h4>
        <ul class="space-y-1 text-xs text-blue-700">
            <li>• Add 4–8 core values that define your company (e.g., Integrity, Innovation, Accountability).</li>
            <li>• Assign a weight percentage to each — they should add up to 100%.</li>
            <li>• Higher weight = more impact on the credibility score and financial leakage calculation.</li>
        </ul>
    </div>
    @endif

    {{-- Values list --}}
    <div class="space-y-3" id="values-list">
        @forelse($values as $i => $value)
        <div class="bg-white rounded-2xl border border-gray-200 p-4 hover:border-brand-200 transition-colors"
             x-data="{ editing: false }">
            <div x-show="!editing" class="flex items-center gap-4">
                <div class="w-8 h-8 rounded-lg bg-brand-50 flex items-center justify-center text-brand-500 font-bold text-sm shrink-0">
                    {{ $i + 1 }}
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-3 mb-0.5">
                        <h4 class="font-semibold text-gray-900">{{ $value->name }}</h4>
                        <span class="px-2 py-0.5 rounded-full bg-brand-50 text-brand-600 text-xs font-medium">{{ $value->weight_percentage }}%</span>
                    </div>
                    @if($value->description)
                    <p class="text-xs text-gray-500 truncate">{{ $value->description }}</p>
                    @endif
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    <button @click="editing=true" class="p-2 rounded-lg text-gray-400 hover:text-brand-500 hover:bg-brand-50 transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    </button>
                    <form method="POST" action="{{ route('values.destroy', $value) }}"
                          onsubmit="return confirm('Remove {{ $value->name }}?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="p-2 rounded-lg text-gray-400 hover:text-red-500 hover:bg-red-50 transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </form>
                </div>
            </div>

            {{-- Inline edit form --}}
            <div x-show="editing" x-cloak>
                <form method="POST" action="{{ route('values.update', $value) }}" class="space-y-3">
                    @csrf @method('PUT')
                    <div class="grid md:grid-cols-3 gap-3">
                        <div class="md:col-span-1">
                            <input type="text" name="name" value="{{ $value->name }}" required placeholder="Value name"
                                   class="w-full px-4 py-2.5 rounded-xl border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
                        </div>
                        <div>
                            <div class="relative">
                                <input type="number" name="weight_percentage" value="{{ $value->weight_percentage }}" min="0" max="100" step="0.5" required
                                       class="w-full px-4 py-2.5 pr-8 rounded-xl border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
                                <span class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">%</span>
                            </div>
                        </div>
                        <div class="md:col-span-1">
                            <input type="text" name="description" value="{{ $value->description }}" placeholder="Description (optional)"
                                   class="w-full px-4 py-2.5 rounded-xl border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="submit" class="bg-brand-500 hover:bg-brand-600 text-white font-medium px-5 py-2 rounded-xl text-sm transition-all">Save</button>
                        <button type="button" @click="editing=false" class="text-gray-500 hover:text-gray-700 font-medium px-4 py-2 rounded-xl text-sm border border-gray-200 hover:bg-gray-50 transition-all">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
        @empty
        <div class="bg-white rounded-2xl border border-dashed border-gray-300 p-10 text-center">
            <p class="text-gray-400 text-sm mb-4">No values configured yet.</p>
            <button @click="showAdd=true" class="text-brand-500 font-semibold text-sm hover:underline">Add your first value →</button>
        </div>
        @endforelse
    </div>

    {{-- Suggested defaults --}}
    @if($values->isEmpty())
    <div class="bg-white rounded-2xl border border-gray-200 p-5">
        <h4 class="font-semibold text-gray-800 text-sm mb-3">Suggested Default Values</h4>
        <div class="grid md:grid-cols-2 gap-2">
            @foreach([['Integrity','Living and upholding ethical standards in all dealings',20],['Innovation','Driving creative and forward-thinking solutions',15],['Accountability','Taking responsibility for actions and outcomes',20],['Customer Focus','Placing customer needs at the center of all decisions',20],['Team Collaboration','Working together to achieve shared goals',15],['Transparency','Open and honest communication with all stakeholders',10]] as [$n,$d,$w])
            <div class="flex items-center justify-between bg-gray-50 rounded-xl px-4 py-3">
                <div>
                    <p class="text-sm font-medium text-gray-800">{{ $n }}</p>
                    <p class="text-xs text-gray-400">{{ $w }}%</p>
                </div>
                <form method="POST" action="{{ route('values.store') }}">
                    @csrf
                    <input type="hidden" name="name" value="{{ $n }}">
                    <input type="hidden" name="description" value="{{ $d }}">
                    <input type="hidden" name="weight_percentage" value="{{ $w }}">
                    <button type="submit" class="text-brand-500 hover:text-brand-700 text-xs font-semibold">+ Add</button>
                </form>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Add value modal --}}
    <div x-show="showAdd" x-cloak class="fixed inset-0 z-50 flex items-center justify-center">
        <div @click="showAdd=false" class="absolute inset-0 bg-black/50"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl p-6 w-full max-w-md mx-4">
            <h3 class="font-bold text-gray-900 mb-4">Add Company Value</h3>
            <form method="POST" action="{{ route('values.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Value Name <span class="text-red-400">*</span></label>
                    <input type="text" name="name" required placeholder="e.g., Integrity, Innovation…"
                           class="w-full px-4 py-2.5 rounded-xl border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Weight Percentage <span class="text-red-400">*</span></label>
                    <div class="relative">
                        <input type="number" name="weight_percentage" required min="1" max="100" step="0.5"
                               value="{{ max(0, 100 - $values->sum('weight_percentage')) }}"
                               class="w-full px-4 py-2.5 pr-8 rounded-xl border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
                        <span class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">%</span>
                    </div>
                    <p class="text-xs text-gray-400 mt-1">Remaining: {{ max(0, 100 - $values->sum('weight_percentage')) }}%</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea name="description" rows="2" placeholder="What does this value mean for your company?"
                              class="w-full px-4 py-2.5 rounded-xl border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 resize-none"></textarea>
                </div>
                <div class="flex items-center gap-3 pt-1">
                    <button type="submit" class="flex-1 bg-brand-500 hover:bg-brand-600 text-white font-semibold py-3 rounded-xl text-sm transition-all">Add Value</button>
                    <button type="button" @click="showAdd=false"
                            class="flex-1 border border-gray-300 text-gray-700 font-medium py-3 rounded-xl text-sm hover:bg-gray-50 transition-all">Cancel</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
