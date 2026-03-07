{{-- resources/views/students/hs/billing.blade.php --}}
@extends('students.hs.layouts.hs-app')
@section('title', 'Billing')

@push('styles')
<style>
/* ── HS Billing Styles ── */
.hb-shimmer { position: relative; overflow: hidden; }
.hb-shimmer::after {
    content: ''; position: absolute; top: 0; left: -100%; width: 50%; height: 100%;
    background: linear-gradient(105deg, transparent 40%, rgba(255,255,255,.35) 50%, transparent 60%);
    animation: hb-shim 3.5s ease-in-out infinite;
}
@keyframes hb-shim { 0% { left: -100%; } 50%, 100% { left: 160%; } }

.hb-row { transition: background .15s ease, transform .15s ease; cursor: default; }
.hb-row:hover { background: rgba(14,165,233,0.08) !important; transform: translateX(3px); }

.hb-select {
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2394a3b8' stroke-width='2'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M19 9l-7 7-7-7'/%3E%3C/svg%3E");
    background-repeat: no-repeat; background-position: right 10px center; background-size: 16px;
    padding-right: 36px !important; cursor: pointer;
}
.hb-select:focus { outline: none; border-color: #4f46e5; box-shadow: 0 0 0 3px rgba(79,70,229,.15); }

.hb-progress-track { height: 6px; background: #e0e7ff; border-radius: 99px; overflow: hidden; margin-top: 14px; }
.hb-progress-fill {
    height: 100%; border-radius: 99px;
    background: linear-gradient(90deg, #0ea5e9, #06b6d4);
    transition: width 1.4s cubic-bezier(.4,0,.2,1);
    position: relative; overflow: hidden;
}
.hb-progress-fill::after {
    content: ''; position: absolute; inset: 0;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,.3), transparent);
    animation: hb-shim 2.5s ease-in-out infinite;
}

.hb-btn-primary {
    background: linear-gradient(135deg, #0ea5e9, #06b6d4);
    transition: all .22s ease;
}
.hb-btn-primary:hover { transform: translateY(-2px); box-shadow: 0 12px 28px rgba(14,165,233,.4); }

@keyframes hb-fadein {
    from { opacity: 0; transform: translateY(12px); }
    to { opacity: 1; transform: translateY(0); }
}
.hb-fade { animation: hb-fadein .45s ease both; }
.hb-d1 { animation-delay: .06s; }
.hb-d2 { animation-delay: .12s; }
.hb-d3 { animation-delay: .18s; }

@media print {
    body * { visibility: hidden; }
    main, main * { visibility: visible; }
    main { position: absolute; left: 0; top: 0; width: 100%; background: white !important; }
    button, a[href] { display: none !important; }
    .no-print { display: none !important; }
}
</style>
@endpush

@section('content')
@php
    $user = auth()->user();
    $isJHS = str_contains(strtolower($user->level_group ?? ''), 'junior');
    $isSHS = str_contains(strtolower($user->level_group ?? ''), 'senior');
    $levelLabel = $isJHS ? 'Junior High' : ($isSHS ? 'Senior High' : 'High School');
    $accentColor = $isJHS ? '#06b6d4' : '#06b6d4';
    $paidPct = ($totalCharges ?? 0) > 0 ? min(100, round((($paid ?? 0) / $totalCharges) * 100)) : 0;
@endphp

<div class="space-y-6">

    {{-- ── Page Header ── --}}
    <div class="hb-fade flex flex-col sm:flex-row sm:items-end justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider mb-3"
                 style="background: #eef2ff; color: #4f46e5; border: 1px solid #c7d2fe;">
                <span class="w-1.5 h-1.5 rounded-full" style="background: {{ $accentColor }};"></span>
                {{ $levelLabel }} · Billing
            </div>
            <h1 class="font-display text-4xl sm:text-5xl leading-tight text-gray-800">Billing</h1>
            <p class="text-sm mt-1.5 text-gray-500">
                Manage your school fees and payment records.
                @if($isJHS) Annual billing — no semester breakdown.
                @elseif($isSHS) Semester-based billing applies.
                @endif
            </p>
        </div>
        <div class="flex items-center gap-3 no-print">
            @if(!isset($activePlan) && ($balance ?? 0) > 0)
            <a href="{{ route('hs.installments') }}"
               class="hb-btn-primary inline-flex items-center gap-2 px-5 py-2.5 text-sm font-bold text-white rounded-xl shadow-lg">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                Installment Plan
            </a>
            @endif
            <button onclick="window.print()"
                    class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-semibold rounded-xl transition-colors"
                    style="background: #ffffff; border: 1px solid #e5e7eb; color: #6b7280;">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                Print
            </button>
        </div>
    </div>

    {{-- ── Active Plan Banner ── --}}
    @if(isset($activePlan))
    <div class="hb-fade rounded-2xl p-4 sm:p-5 flex items-center gap-4"
         style="background: linear-gradient(135deg, #eef2ff, #e0e7ff); border: 1.5px solid #c7d2fe;">
        <div class="w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0"
             style="background: linear-gradient(135deg, #0ea5e9, #06b6d4);">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
        </div>
        <div class="flex-1">
            <p class="font-bold text-indigo-900 text-sm">Active Installment Plan — {{ $activePlan->total_installments }}-Part Payment</p>
            <p class="text-xs mt-0.5 text-indigo-600">
                ₱{{ number_format($activePlan->amount_per_installment, 2) }} per installment ·
                <a href="{{ route('hs.installments') }}" class="text-indigo-600 hover:text-indigo-800 font-semibold">Manage →</a>
            </p>
        </div>
    </div>
    @endif

    {{-- ── Filters ── --}}
    <div class="hb-fade hb-d1 flex flex-wrap items-center gap-3"
         style="background: #ffffff; border: 1px solid #e5e7eb; border-radius: 16px; padding: 16px 20px;">
        <form method="GET" action="{{ route('hs.billing') }}" class="flex flex-wrap gap-3 items-center w-full">
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-wide mb-1.5 text-gray-400">School Year</label>
                <select name="school_year" onchange="this.form.submit()"
                        class="hb-select text-sm font-semibold rounded-xl px-3.5 py-2"
                        style="background: #f9fafb; border: 1px solid #e5e7eb; color: #1f2937;">
                    @foreach($availableYears ?? ['2025-2026', '2024-2025'] as $yr)
                    <option value="{{ $yr }}" {{ ($selectedYear ?? '2025-2026') == $yr ? 'selected' : '' }}
                            style="background: #ffffff;">{{ $yr }}</option>
                    @endforeach
                </select>
            </div>
            @if($isSHS)
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-wide mb-1.5 text-gray-400">Semester</label>
                <select name="semester" onchange="this.form.submit()"
                        class="hb-select text-sm font-semibold rounded-xl px-3.5 py-2"
                        style="background: #f9fafb; border: 1px solid #e5e7eb; color: #1f2937;">
                    <option value="1" {{ ($selectedSemester ?? '1') == '1' ? 'selected' : '' }} style="background: #ffffff;">1st Semester</option>
                    <option value="2" {{ ($selectedSemester ?? '1') == '2' ? 'selected' : '' }} style="background: #ffffff;">2nd Semester</option>
                </select>
            </div>
            @else
            <div class="flex items-center gap-2 px-4 py-2 rounded-xl"
                 style="background: #cffafe; border: 1px solid #a5f3fc;">
                <span class="text-lg">📚</span>
                <div>
                    <p class="text-[10px] font-bold text-cyan-700">Annual Billing</p>
                </div>
            </div>
            @endif
        </form>
    </div>

    {{-- ── Balance Cards ── --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 hb-fade hb-d2">
        {{-- Balance due --}}
        <div class="hb-shimmer rounded-2xl p-6 sm:p-7 shadow-xl hs-card-lift"
             style="background: {{ ($balance ?? 0) > 0 ? 'linear-gradient(135deg, #fff1f2, #ffe4e6)' : 'linear-gradient(135deg, #f0fdf4, #dcfce7)' }}; border: 1px solid {{ ($balance ?? 0) > 0 ? '#fecdd3' : '#bbf7d0' }}; min-height: 170px; position: relative; overflow: hidden;">
            <div class="absolute -top-8 -right-8 w-36 h-36 rounded-full opacity-25"
                 style="background: radial-gradient(circle, rgba(255,255,255,0.7), transparent)"></div>
            <div class="relative">
                <p class="text-[10px] font-bold uppercase tracking-widest mb-3" style="color: {{ ($balance ?? 0) > 0 ? '#9f1239' : '#065f46' }};">
                    {{ ($balance ?? 0) > 0 ? 'Outstanding Balance' : '✓ Fully Paid' }}
                </p>
                <p class="font-mono-num text-4xl sm:text-5xl font-extrabold leading-none tabular-nums" style="color: {{ ($balance ?? 0) > 0 ? '#e11d48' : '#059669' }};">
                    ₱{{ number_format($balance ?? 0, 2) }}
                </p>
                <div class="hb-progress-track">
                    <div class="hb-progress-fill" style="width: {{ 100 - $paidPct }}%"></div>
                </div>
                <div class="flex items-center justify-between mt-2">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold"
                          style="background: rgba(255,255,255,0.6); color: {{ ($balance ?? 0) > 0 ? '#9f1239' : '#065f46' }};">
                        {{ ($balance ?? 0) > 0 ? '⚠️ Due' : '✅ Clear' }}
                    </span>
                    <span class="text-xs font-bold" style="color: {{ ($balance ?? 0) > 0 ? '#9f1239' : '#065f46' }};">{{ 100 - $paidPct }}% remaining</span>
                </div>
            </div>
        </div>

        {{-- Amount paid --}}
        <div class="hb-shimmer rounded-2xl p-6 sm:p-7 shadow-xl hs-card-lift"
             style="background: linear-gradient(135deg, #f0fdf4, #dcfce7); border: 1px solid #bbf7d0; min-height: 170px; position: relative; overflow: hidden;">
            <div class="absolute -top-8 -right-8 w-36 h-36 rounded-full opacity-20"
                 style="background: radial-gradient(circle, rgba(255,255,255,0.7), transparent)"></div>
            <div class="relative">
                <p class="text-[10px] font-bold uppercase tracking-widest mb-3 text-emerald-700">Amount Paid</p>
                <p class="font-mono-num text-4xl sm:text-5xl font-extrabold leading-none text-emerald-700 tabular-nums">
                    ₱{{ number_format($paid ?? 0, 2) }}
                </p>
                <div class="hb-progress-track">
                    <div class="hb-progress-fill" style="width: {{ $paidPct }}%; background: linear-gradient(90deg, #10b981, #34d399);"></div>
                </div>
                <div class="flex items-center justify-between mt-2">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-white/60 text-emerald-700">✅ This Period</span>
                    <span class="text-xs font-bold text-emerald-600">of ₱{{ number_format($totalCharges ?? 0, 2) }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Ledger Table ── --}}
    <div class="rounded-2xl overflow-hidden shadow-lg hb-fade hb-d3"
         style="border: 1px solid rgba(14,165,233,0.2);">
        <div class="px-6 py-4 flex items-center justify-between"
             style="background: #f8faff; border-bottom: 1px solid #e0e7ff;">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center"
                     style="background: #c7d2fe;">
                    <svg class="w-4 h-4 text-indigo-500" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 2a2 2 0 00-2 2v8a2 2 0 002 2h6a2 2 0 002-2V6.414A2 2 0 0016.414 5L14 2.586A2 2 0 0012.586 2H9z"/>
                        <path d="M3 8a2 2 0 012-2v10h8a2 2 0 01-2 2H5a2 2 0 01-2-2V8z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="font-bold text-gray-800 text-base">Student Ledger</h2>
                    <p class="text-xs mt-0.5 text-gray-400">
                        {{ $selectedYear ?? date('Y').'-'.(date('Y')+1) }}
                        @if($isSHS) — {{ ($selectedSemester ?? '1') == '1' ? '1st' : '2nd' }} Semester @endif
                    </p>
                </div>
            </div>
            <button onclick="window.print()" class="no-print flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium transition-all"
                    style="color: #6b7280;" class="hover:text-indigo-600 hover:bg-indigo-50">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                Print
            </button>
        </div>

        <div class="overflow-x-auto" style="background: #ffffff;">
            <table class="w-full min-w-[560px]">
                <thead>
                    <tr class="bg-gray-50" style="">
                        <th class="px-6 py-3.5 text-left text-[10px] font-bold uppercase tracking-widest text-gray-400">Particulars</th>
                        <th class="px-6 py-3.5 text-right text-[10px] font-bold uppercase tracking-widest text-gray-400">Charges</th>
                        <th class="px-6 py-3.5 text-right text-[10px] font-bold uppercase tracking-widest text-gray-400">Payments</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($ledgerItems ?? [] as $item)
                    <tr class="hb-row" class="{{ $loop->even ? 'bg-gray-50/50' : 'bg-white' }} border-b border-gray-50">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-1.5 h-1.5 rounded-full flex-shrink-0"
                                     style="background: {{ (!empty($item['payment']) && $item['payment'] > 0) ? '#059669' : '#d1d5db' }};"></div>
                                <span class="text-sm font-medium text-gray-700">{{ $item['description'] ?? '—' }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-right font-mono-num font-semibold text-sm">
                            @if(!empty($item['charge']) && $item['charge'] > 0)
                                <span class="text-rose-600">₱{{ number_format($item['charge'], 2) }}</span>
                            @else
                                <span class="text-gray-200">—</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right font-mono-num font-semibold text-sm">
                            @if(!empty($item['payment']) && $item['payment'] > 0)
                                <span class="inline-flex items-center gap-1 text-emerald-600">
                                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                    ₱{{ number_format($item['payment'], 2) }}
                                </span>
                            @else
                                <span class="text-gray-200">—</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="px-6 py-16 text-center">
                            <div class="text-4xl mb-3">📋</div>
                            <p class="text-sm font-semibold text-gray-400">No records for this period</p>
                            <p class="text-xs mt-1 text-gray-300">Try selecting a different school year{{ $isSHS ? ' or semester' : '' }}.</p>
                        </td>
                    </tr>
                    @endforelse

                    {{-- Total row --}}
                    <tr class="bg-gray-100 border-t-2 border-gray-200">
                        <td class="px-6 py-4 font-bold text-gray-700 text-sm uppercase tracking-wide">Total</td>
                        <td class="px-6 py-4 text-right font-mono-num font-bold text-sm text-rose-600">₱{{ number_format($totalCharges ?? 0, 2) }}</td>
                        <td class="px-6 py-4 text-right font-mono-num font-bold text-sm text-emerald-600">₱{{ number_format($totalPayments ?? $paid ?? 0, 2) }}</td>
                    </tr>

                    {{-- Balance row --}}
                    <tr style="background: linear-gradient(135deg, #4f46e5, #6366f1);">
                        <td class="px-6 py-5">
                            <div class="flex items-center gap-2">
                                <div class="w-2 h-2 rounded-full {{ ($balance ?? 0) > 0 ? 'hs-pulse' : '' }}"
                                     style="background: {{ ($balance ?? 0) > 0 ? '#fbbf24' : '#34d399' }};"></div>
                                <span class="text-white font-bold text-sm uppercase tracking-wider">Remaining Balance</span>
                            </div>
                        </td>
                        <td colspan="2" class="px-6 py-5 text-right">
                            <span class="font-mono-num font-extrabold text-2xl text-white tabular-nums">
                                ₱{{ number_format($balance ?? 0, 2) }}
                            </span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection