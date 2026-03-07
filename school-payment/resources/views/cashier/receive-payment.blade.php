{{-- resources/views/cashier/receive-payment.blade.php --}}
@extends('cashier.layouts.cashier-app')
@section('title', 'Receive Payment')

@push('styles')
<style>
.rp-label { display: block; font-size: 12px; font-weight: 700; color: #6b7280; margin-bottom: 6px; letter-spacing: 0.04em; text-transform: uppercase; }
.rp-section { border-radius: 20px; padding: 24px; background: #ffffff; border: 1px solid #e5e7eb; }
</style>
@endpush

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    {{-- ── Page Header ── --}}
    <div class="c-fade">
        <a href="{{ route('cashier.students') }}"
           class="inline-flex items-center gap-1.5 text-xs font-semibold mb-4 text-gray-400 hover:text-indigo-500 transition-colors">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
            </svg>
            Back to Students
        </a>
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider mb-3 bg-indigo-50 text-indigo-500 border border-indigo-100">
            <span class="w-1.5 h-1.5 rounded-full bg-indigo-400"></span>
            Transaction Entry
        </div>
        <h1 class="font-display text-4xl sm:text-5xl text-gray-800 leading-tight">Receive Payment</h1>
        <p class="text-sm mt-1.5 text-gray-400">Record a student payment and issue an official receipt.</p>
    </div>

    <form method="POST" action="{{ route('cashier.receive-payment.store') }}" class="space-y-5">
        @csrf

        {{-- ── Student Selection ── --}}
        <div class="rp-section c-fade c-d1 shadow-sm">
            <h2 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                <span class="text-indigo-500">01</span> Student
            </h2>

            @if(isset($preselectedStudent))
                <input type="hidden" name="student_id" value="{{ $preselectedStudent->id }}">
                <div class="flex items-center gap-4 p-4 rounded-xl bg-indigo-50 border border-indigo-100">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center font-bold text-white flex-shrink-0"
                         style="background: linear-gradient(135deg, #4f46e5, #6366f1);">
                        {{ strtoupper(substr($preselectedStudent->name, 0, 1)) }}
                    </div>
                    <div class="flex-1">
                        <p class="font-bold text-gray-800">{{ $preselectedStudent->name }}</p>
                        <p class="text-xs mt-0.5 text-gray-400">
                            {{ $preselectedStudent->student_id ?? 'No ID' }} · {{ $preselectedStudent->level_group ?? '—' }} · {{ $preselectedStudent->year_level ?? '—' }}
                        </p>
                    </div>
                    <div class="text-right">
                        <p class="text-xs font-semibold text-gray-400">Balance Due</p>
                        <p class="font-mono-num font-bold text-lg {{ ($preselectedStudent->balance ?? 0) > 0 ? 'text-red-500' : 'text-indigo-500' }}">
                            ₱{{ number_format($preselectedStudent->balance ?? 0, 2) }}
                        </p>
                    </div>
                    <a href="{{ route('cashier.receive-payment') }}" class="text-xs text-gray-400 hover:text-gray-600 no-print">Change</a>
                </div>
            @else
                <div x-data="studentSearch()" class="space-y-3">
                    <div class="relative">
                        <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 pointer-events-none text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <input type="text"
                               x-model="query"
                               @input.debounce.300ms="search()"
                               placeholder="Type student name or ID to search..."
                               class="c-input pl-10"
                               autocomplete="off">
                        <input type="hidden" name="student_id" x-model="selectedId" required>
                    </div>

                    {{-- Search results --}}
                    <div x-show="results.length > 0" class="rounded-xl overflow-hidden border border-gray-200 shadow-sm bg-white">
                        <template x-for="s in results" :key="s.id">
                            <button type="button"
                                    @click="select(s)"
                                    class="w-full flex items-center gap-3 px-4 py-3 text-left transition-all border-b border-gray-50 last:border-0 hover:bg-indigo-50"
                                    :class="selectedId == s.id ? 'bg-indigo-50' : 'bg-white'">
                                <div class="w-8 h-8 rounded-lg flex items-center justify-center text-xs font-bold text-white flex-shrink-0"
                                     style="background: linear-gradient(135deg, #4f46e5, #6366f1);"
                                     x-text="s.name.charAt(0).toUpperCase()"></div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-gray-800 truncate" x-text="s.name"></p>
                                    <p class="text-xs mt-0.5 text-gray-400" x-text="(s.student_id || 'No ID') + ' · ' + (s.level_group || '—') + ' · ' + (s.year_level || '—')"></p>
                                </div>
                                <div class="text-right flex-shrink-0">
                                    <p class="font-mono-num font-bold text-sm" :class="s.balance > 0 ? 'text-red-500' : 'text-indigo-500'"
                                       x-text="'₱' + parseFloat(s.balance || 0).toLocaleString('en-PH', {minimumFractionDigits:2})"></p>
                                    <p class="text-[10px] mt-0.5 text-gray-400">balance</p>
                                </div>
                            </button>
                        </template>
                    </div>

                    {{-- Selected Student Card --}}
                    <div x-show="selected" class="flex items-center gap-4 p-4 rounded-xl bg-indigo-50 border border-indigo-100">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center font-bold text-white flex-shrink-0"
                             style="background: linear-gradient(135deg, #4f46e5, #6366f1);"
                             x-text="selected ? selected.name.charAt(0).toUpperCase() : ''"></div>
                        <div class="flex-1">
                            <p class="font-bold text-gray-800" x-text="selected?.name"></p>
                            <p class="text-xs mt-0.5 text-gray-400" x-text="(selected?.student_id || 'No ID') + ' · ' + (selected?.level_group || '—')"></p>
                        </div>
                        <div class="text-right">
                            <p class="text-xs text-gray-400">Balance Due</p>
                            <p class="font-mono-num font-bold text-lg"
                               :class="(selected?.balance||0) > 0 ? 'text-red-500' : 'text-indigo-500'"
                               x-text="'₱' + parseFloat(selected?.balance||0).toLocaleString('en-PH',{minimumFractionDigits:2})"></p>
                        </div>
                        <button type="button" @click="clear()" class="text-xs text-gray-400 hover:text-gray-600">Change</button>
                    </div>
                </div>
            @endif
        </div>

        {{-- ── Payment Details ── --}}
        <div class="rp-section c-fade c-d2 shadow-sm">
            <h2 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                <span class="text-indigo-500">02</span> Payment Details
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="rp-label">Amount (₱) *</label>
                    <input type="number" name="amount" step="0.01" min="1" required
                           value="{{ old('amount') }}" placeholder="0.00"
                           class="c-input font-mono-num text-lg">
                </div>
                <div>
                    <label class="rp-label">Payment Date *</label>
                    <input type="date" name="payment_date" required
                           value="{{ old('payment_date', date('Y-m-d')) }}"
                           class="c-input">
                </div>
                <div>
                    <label class="rp-label">Payment Method *</label>
                    <select name="payment_method" required class="c-input c-select">
                        <option value="">Select method...</option>
                        <option value="Cash"          {{ old('payment_method') === 'Cash'          ? 'selected' : '' }}>💵 Cash</option>
                        <option value="GCash"         {{ old('payment_method') === 'GCash'         ? 'selected' : '' }}>📱 GCash</option>
                        <option value="PayMaya"       {{ old('payment_method') === 'PayMaya'       ? 'selected' : '' }}>📱 PayMaya</option>
                        <option value="Bank Transfer" {{ old('payment_method') === 'Bank Transfer' ? 'selected' : '' }}>🏦 Bank Transfer</option>
                        <option value="Check"         {{ old('payment_method') === 'Check'         ? 'selected' : '' }}>📝 Check</option>
                    </select>
                </div>
                <div>
                    <label class="rp-label">OR Number</label>
                    <input type="text" name="or_number"
                           value="{{ old('or_number') }}" placeholder="Official Receipt No."
                           class="c-input font-mono-num">
                </div>
                <div>
                    <label class="rp-label">Reference Number</label>
                    <input type="text" name="reference_number"
                           value="{{ old('reference_number') }}"
                           placeholder="Transaction ref (for digital payments)"
                           class="c-input">
                </div>
                <div>
                    <label class="rp-label">School Year *</label>
                    <select name="school_year" required class="c-input c-select">
                        @for($y = date('Y'); $y >= date('Y')-2; $y--)
                        @php $opt = $y.'-'.($y+1); @endphp
                        <option value="{{ $opt }}" {{ old('school_year', date('Y').'-'.(date('Y')+1)) === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                        @endfor
                    </select>
                </div>
                <div>
                    <label class="rp-label">Semester *</label>
                    <select name="semester" required class="c-input c-select">
                        <option value="1"      {{ old('semester', '1') === '1'      ? 'selected' : '' }}>1st Semester / Annual</option>
                        <option value="2"      {{ old('semester') === '2'            ? 'selected' : '' }}>2nd Semester</option>
                        <option value="summer" {{ old('semester') === 'summer'       ? 'selected' : '' }}>Summer</option>
                    </select>
                </div>
            </div>
        </div>

        {{-- ── Notes ── --}}
        <div class="rp-section c-fade c-d3 shadow-sm">
            <h2 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                <span class="text-indigo-500">03</span> Notes
                <span class="text-xs font-normal text-gray-400">(optional)</span>
            </h2>
            <textarea name="notes" rows="3" placeholder="Additional notes about this payment..."
                      class="c-input resize-none">{{ old('notes') }}</textarea>
        </div>

        {{-- ── Submit ── --}}
        <div class="flex items-center gap-3 c-fade c-d4">
            <button type="submit"
                    class="flex-1 sm:flex-none px-8 py-3.5 rounded-2xl font-bold text-white text-base transition-all hover:scale-[1.02] shadow-sm"
                    style="background: linear-gradient(135deg, #4f46e5, #6366f1);"
                    onmouseover="this.style.boxShadow='0 15px 35px rgba(79,70,229,0.25)'"
                    onmouseout="this.style.boxShadow=''">
                ✅ Post Payment
            </button>
            <a href="{{ route('cashier.students') }}"
               class="px-6 py-3.5 rounded-2xl font-semibold text-sm text-gray-500 bg-white border border-gray-200 hover:bg-gray-50 transition-all">
                Cancel
            </a>
        </div>
    </form>
</div>

@push('scripts')
<script>
function studentSearch() {
    return {
        query: '',
        results: [],
        selected: null,
        selectedId: '{{ request('student_id') }}',
        async search() {
            if (this.query.length < 2) { this.results = []; return; }
            try {
                const res = await fetch(`/cashier/students/search?q=${encodeURIComponent(this.query)}`);
                this.results = await res.json();
            } catch(e) {}
        },
        select(s) {
            this.selected = s;
            this.selectedId = s.id;
            this.query = s.name;
            this.results = [];
        },
        clear() {
            this.selected = null;
            this.selectedId = '';
            this.query = '';
        }
    }
}
</script>
@endpush
@endsection