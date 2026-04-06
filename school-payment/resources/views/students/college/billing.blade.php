{{-- billing.blade.php --}}
@extends('students.college.layouts.student-app')
@section('title', 'Billing')

@push('styles')
<style>
/* ── Billing Page Styles ── */
.bl-root { font-family: 'Sora', sans-serif; }

/* Balance card shimmer */
.bl-card-shimmer {
    position: relative; overflow: hidden;
}
.bl-card-shimmer::after {
    content: ''; position: absolute; top: 0; left: -100%; width: 50%; height: 100%;
    background: linear-gradient(105deg, transparent 40%, rgba(255,255,255,.12) 50%, transparent 60%);
    animation: bl-shimmer 3.5s ease-in-out infinite;
}
@keyframes bl-shimmer {
    0% { left: -100%; }
    50%, 100% { left: 160%; }
}

/* Plan banner pulse */
@keyframes bl-plan-pulse {
    0%, 100% { box-shadow: 0 0 0 0 rgba(99,102,241,.25); }
    50% { box-shadow: 0 0 0 8px rgba(99,102,241,0); }
}
.bl-plan-banner { animation: bl-plan-pulse 3s ease-in-out infinite; }

/* Filter card select */
.bl-select {
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2394a3b8' stroke-width='2'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M19 9l-7 7-7-7'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 10px center;
    background-size: 16px;
    padding-right: 36px !important;
    cursor: pointer;
    transition: all .2s ease;
}
.bl-select:focus { outline: none; border-color: #6366f1; box-shadow: 0 0 0 3px rgba(99,102,241,.15); }

/* Ledger table rows */
.bl-ledger-row { transition: background .15s ease, transform .15s ease; }
.bl-ledger-row:hover { background: #f0f4ff !important; transform: translateX(3px); }

/* Progress bar */
.bl-progress-track { height: 6px; background: rgba(255,255,255,.15); border-radius: 99px; overflow: hidden; margin-top: 14px; }
.bl-progress-fill {
    height: 100%; border-radius: 99px;
    background: linear-gradient(90deg, rgba(255,255,255,.5), rgba(255,255,255,.9));
    transition: width 1.4s cubic-bezier(.4,0,.2,1);
    position: relative; overflow: hidden;
}
.bl-progress-fill::after {
    content: ''; position: absolute; inset: 0;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,.4), transparent);
    animation: bl-shimmer 2.5s ease-in-out infinite;
}

/* CTA button hover */
.bl-btn-primary {
    background: linear-gradient(135deg, #4f46e5, #7c3aed);
    transition: all .22s ease;
}
.bl-btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 12px 28px rgba(99,102,241,.38);
    background: linear-gradient(135deg, #4338ca, #6d28d9);
}

/* Fade in */
@keyframes bl-fadein {
    from { opacity: 0; transform: translateY(12px); }
    to { opacity: 1; transform: translateY(0); }
}
.bl-fade { animation: bl-fadein .45s ease both; }
.bl-d1 { animation-delay: .06s; }
.bl-d2 { animation-delay: .12s; }
.bl-d3 { animation-delay: .18s; }

/* Print */
@media print {
    body * { visibility: hidden; }
    main, main * { visibility: visible; }
    main { position: absolute; left: 0; top: 0; width: 100%; }
    button, a[href] { display: none !important; }
    .overflow-x-auto { overflow: visible !important; }
    .bl-no-print { display: none !important; }
}
</style>
@endpush

@section('content')
<div class="bl-root space-y-6">

    {{-- ── Page Header ── --}}
    <div class="bl-fade flex flex-col sm:flex-row sm:items-end justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider mb-3"
                 style="background: rgba(99,102,241,.1); color: #4f46e5;">
                <span class="w-1.5 h-1.5 rounded-full bg-indigo-500"></span>
                Student Account
            </div>
            <h1 class="font-display text-4xl sm:text-5xl leading-tight" class="font-display text-4xl sm:text-5xl leading-tight text-gray-800">Billing</h1>
            <p class="text-slate-500 text-sm mt-1.5">Manage your tuition charges and payment records.</p>
        </div>
        <div class="flex items-center gap-3 bl-no-print">
            @php $__prevBal = $previousBalance ?? 0; $__totalBal = ($balance ?? 0) + $__prevBal; @endphp
            @if($__totalBal > 0)
            <button onclick="document.getElementById('bl-pay-modal').classList.remove('hidden')"
                    class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-bold text-white rounded-xl shadow-lg transition-all"
                    style="background: linear-gradient(135deg,#059669,#10b981);">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                </svg>
                Pay Online
            </button>
            @endif
            @if(!isset($activePlan) && $__totalBal > 0)
            <a href="{{ route('student.installments') }}"
               class="bl-btn-primary inline-flex items-center gap-2 px-5 py-2.5 text-sm font-bold text-white rounded-xl shadow-lg">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                Set Up Installment Plan
            </a>
            @endif
            <button onclick="window.print()"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-sm font-semibold text-slate-600 hover:bg-slate-50 shadow-sm transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                Print
            </button>
        </div>
    </div>

    {{-- ── Active Plan Banner ── --}}
    @if(isset($activePlan))
    <div class="bl-plan-banner bl-fade rounded-2xl p-4 sm:p-5 flex items-center gap-4"
         style="background: linear-gradient(135deg,#eef2ff,#e0e7ff); border: 1.5px solid #c7d2fe;">
        <div class="w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0 shadow-md"
             style="background: linear-gradient(135deg, #4f46e5, #7c3aed);">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
        </div>
        <div class="flex-1 min-w-0">
            <div class="flex items-center gap-2 mb-0.5">
                <p class="font-bold text-indigo-900 text-sm">Active Installment Plan</p>
                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-indigo-200 text-indigo-800">
                    <span class="w-1.5 h-1.5 rounded-full bg-indigo-500 animate-pulse"></span> LIVE
                </span>
            </div>
            <p class="text-xs text-indigo-600">
                {{ $activePlan['installments'] ?? 1 }}-payment plan
                @if($activePlan['next_due'] ?? false)
                    · Next due <strong>{{ $activePlan['next_due'] }}</strong>
                @endif
            </p>
        </div>
        <a href="{{ route('student.installments') }}"
           class="bl-no-print flex-shrink-0 inline-flex items-center gap-1.5 text-xs font-bold text-indigo-700 hover:text-indigo-900 transition-colors px-3 py-2 rounded-lg hover:bg-indigo-100">
            View Plan
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
            </svg>
        </a>
    </div>
    @endif

    {{-- ── Top Row: Filter + Summary Cards ── --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 bl-fade bl-d1">

        {{-- Enrollment Filter --}}
        <div class="lg:col-span-4">
            <div class="bg-white rounded-2xl border border-slate-200 p-6 h-full shadow-sm">
                <div class="flex items-center gap-3 mb-5">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center shadow-md"
                         style="background: linear-gradient(135deg, #4f46e5, #6366f1);">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="font-bold text-slate-800 text-sm">Enrollment Period</h2>
                        <p class="text-xs text-slate-400">Select period to view</p>
                    </div>
                </div>

                <form method="GET" action="{{ route('student.billing') }}" class="space-y-3">
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-1.5">School Year</label>
                        <select name="school_year" class="bl-select w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 text-sm font-semibold"
                                onchange="this.form.submit()">
                            @forelse($availableYears ?? [] as $year)
                                <option value="{{ $year }}" {{ ($selectedYear ?? '') === $year ? 'selected' : '' }}>{{ $year }}</option>
                            @empty
                                <option value="" disabled>No data available</option>
                            @endforelse
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-1.5">Semester</label>
                        <select name="semester" class="bl-select w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 text-sm font-semibold"
                                onchange="this.form.submit()">
                            <option value="1"      {{ ($selectedSemester ?? '') === '1'      ? 'selected' : '' }}>1st Semester</option>
                            <option value="2"      {{ ($selectedSemester ?? '') === '2'      ? 'selected' : '' }}>2nd Semester</option>
                            <option value="summer" {{ ($selectedSemester ?? '') === 'summer' ? 'selected' : '' }}>Summer</option>
                        </select>
                    </div>
                </form>

                <div class="mt-5 pt-4 border-t border-slate-100 flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Currently Viewing</p>
                        <p class="font-bold text-slate-700 text-sm mt-0.5">
                            {{ $selectedYear ?? '—' }} —
                            @switch($selectedSemester ?? '')
                                @case('1') 1st Semester @break
                                @case('2') 2nd Semester @break
                                @case('summer') Summer @break
                                @default —
                            @endswitch
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Summary Cards --}}
        <div class="lg:col-span-8 grid grid-cols-1 sm:grid-cols-2 gap-4">

            {{-- Balance Card --}}
            @php
                $prevSemBal  = $previousBalance ?? 0;
                $curSemBal   = $balance ?? 0;
                $totalBal    = $curSemBal + $prevSemBal;
                $totalDue    = ($totalCharges ?? 0) + $prevSemBal;
                $paidPct     = $totalDue > 0 ? round((($paid ?? 0) / $totalDue) * 100) : 0;
            @endphp
            <div class="bl-card-shimmer relative overflow-hidden rounded-2xl p-6 sm:p-7 shadow-xl card-lift"
                 style="background: linear-gradient(135deg,#7f1d1d,#b91c1c,#991b1b); color: white; min-height: 170px;">
                <div class="absolute -top-8 -right-8 w-36 h-36 rounded-full opacity-20"
                     style="background: radial-gradient(circle, white, transparent)"></div>
                <div class="absolute -bottom-10 -left-10 w-40 h-40 rounded-full opacity-10"
                     style="background: radial-gradient(circle, white, transparent)"></div>
                <div class="relative">
                    <p class="text-[10px] font-bold uppercase tracking-widest opacity-60 mb-3">Current Balance</p>
                    <p class="font-mono-num text-4xl sm:text-5xl font-extrabold leading-none tabular-nums">
                        ₱{{ number_format($totalBal, 2) }}
                    </p>
                    @if($prevSemBal > 0)
                    <div class="mt-2 space-y-0.5">
                        <div class="flex justify-between text-[10px] font-semibold opacity-75">
                            <span>This Semester</span>
                            <span class="font-mono-num">₱{{ number_format($curSemBal, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-[10px] font-semibold opacity-75">
                            <span class="flex items-center gap-1">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-300 animate-pulse"></span>
                                Previous Semester
                            </span>
                            <span class="font-mono-num">₱{{ number_format($prevSemBal, 2) }}</span>
                        </div>
                    </div>
                    @endif
                    <div class="bl-progress-track">
                        <div class="bl-progress-fill" style="width: {{ $paidPct }}%"></div>
                    </div>
                    <div class="flex items-center justify-between mt-2">
                        <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-white/20 backdrop-blur-sm">
                            <span class="w-1.5 h-1.5 rounded-full {{ $totalBal > 0 ? 'bg-amber-300 animate-pulse' : 'bg-emerald-300' }}"></span>
                            {{ $totalBal > 0 ? 'Outstanding' : 'Fully Paid ✓' }}
                        </div>
                        <span class="text-xs font-bold opacity-60">{{ $paidPct }}% paid</span>
                    </div>
                    @if($totalBal > 0)
                    <button onclick="document.getElementById('bl-pay-modal').classList.remove('hidden')"
                            class="bl-no-print mt-4 w-full flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-sm font-bold transition-all"
                            style="background:rgba(255,255,255,.18);border:1.5px solid rgba(255,255,255,.35);color:white;backdrop-filter:blur(8px);"
                            onmouseover="this.style.background='rgba(255,255,255,.28)'"
                            onmouseout="this.style.background='rgba(255,255,255,.18)'">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                        </svg>
                        Pay Online Now
                    </button>
                    @endif
                </div>
            </div>

            {{-- Paid Card --}}
            <div class="bl-card-shimmer relative overflow-hidden rounded-2xl p-6 sm:p-7 shadow-xl card-lift"
                 style="background: linear-gradient(135deg,#052e16,#065f46,#047857); color: white; min-height: 170px;">
                <div class="absolute -top-8 -right-8 w-36 h-36 rounded-full opacity-20"
                     style="background: radial-gradient(circle, white, transparent)"></div>
                <div class="absolute -bottom-10 -left-10 w-40 h-40 rounded-full opacity-10"
                     style="background: radial-gradient(circle, white, transparent)"></div>
                <div class="relative">
                    <p class="text-[10px] font-bold uppercase tracking-widest opacity-60 mb-3">Amount Paid</p>
                    <p class="font-mono-num text-4xl sm:text-5xl font-extrabold leading-none tabular-nums">
                        ₱{{ number_format($paid ?? 0, 2) }}
                    </p>
                    <div class="bl-progress-track">
                        <div class="bl-progress-fill" style="width: {{ $paidPct }}%"></div>
                    </div>
                    <div class="flex items-center justify-between mt-2">
                        <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-white/20 backdrop-blur-sm">
                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            This Period
                        </div>
                        <span class="text-xs font-bold opacity-60">of ₱{{ number_format($totalCharges ?? 0, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Ledger Table ── --}}
    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm bl-fade bl-d2">
        <div class="px-6 py-4 flex items-center justify-between"
             style="background: #f8faff; border-bottom: 1px solid #e0e7ff;">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-indigo-100 flex items-center justify-center">
                    <svg class="w-4 h-4 text-indigo-500" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 2a2 2 0 00-2 2v8a2 2 0 002 2h6a2 2 0 002-2V6.414A2 2 0 0016.414 5L14 2.586A2 2 0 0012.586 2H9z"/>
                        <path d="M3 8a2 2 0 012-2v10h8a2 2 0 01-2 2H5a2 2 0 01-2-2V8z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="font-bold text-gray-800 text-base leading-tight">Student Ledger</h2>
                    <p class="text-gray-400 text-xs">{{ $selectedYear ?? '—' }} — full record</p>
                </div>
            </div>
            <button onclick="window.print()"
                    class="bl-no-print flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 transition-all text-xs font-medium">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                Print
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[560px]">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50">
                        <th class="px-6 py-3.5 text-left text-[10px] font-bold text-slate-400 uppercase tracking-widest">Particulars</th>
                        <th class="px-6 py-3.5 text-right text-[10px] font-bold text-slate-400 uppercase tracking-widest">Charges</th>
                        <th class="px-6 py-3.5 text-right text-[10px] font-bold text-slate-400 uppercase tracking-widest">Payments</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($ledgerItems ?? [] as $item)
                    <tr class="bl-ledger-row {{ $loop->even ? 'bg-slate-50/40' : 'bg-white' }}">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-1.5 h-1.5 rounded-full flex-shrink-0 {{ !empty($item['payment']) && $item['payment'] > 0 ? 'bg-emerald-400' : 'bg-slate-300' }}"></div>
                                <span class="text-slate-700 font-medium text-sm">{{ $item['description'] ?? '—' }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-right font-mono-num font-semibold text-slate-700 text-sm">
                            @if(!empty($item['charge']) && $item['charge'] > 0)
                                <span class="text-rose-600">₱{{ number_format($item['charge'], 2) }}</span>
                            @else
                                <span class="text-slate-200">—</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right font-mono-num font-semibold text-sm">
                            @if(!empty($item['payment']) && $item['payment'] > 0)
                                <span class="inline-flex items-center gap-1 text-emerald-600">
                                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                    ₱{{ number_format($item['payment'], 2) }}
                                </span>
                            @else
                                <span class="text-slate-200">—</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="px-6 py-16 text-center">
                            <div class="inline-flex flex-col items-center gap-3">
                                <div class="w-16 h-16 rounded-2xl bg-slate-100 flex items-center justify-center">
                                    <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                </div>
                                <p class="text-slate-400 text-sm font-semibold">No transactions for this period.</p>
                                <p class="text-slate-300 text-xs">Try selecting a different school year or semester.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse

                    {{-- Subtotal --}}
                    <tr class="bg-slate-100 border-t-2 border-slate-200">
                        <td class="px-6 py-4 font-bold text-slate-800 text-sm uppercase tracking-wide">Total</td>
                        <td class="px-6 py-4 text-right font-mono-num font-bold text-rose-600 text-sm">₱{{ number_format($totalCharges ?? 0, 2) }}</td>
                        <td class="px-6 py-4 text-right font-mono-num font-bold text-emerald-600 text-sm">₱{{ number_format($totalPayments ?? 0, 2) }}</td>
                    </tr>

                    {{-- Previous Semester Carryover --}}
                    @if(($previousBalance ?? 0) > 0)
                    <tr style="background:#fffbeb; border-top: 1px solid #fde68a;">
                        <td class="px-6 py-4" colspan="2">
                            <div class="flex items-center gap-2">
                                <div class="w-2 h-2 rounded-full bg-amber-400 animate-pulse flex-shrink-0"></div>
                                <span class="text-amber-800 font-bold text-sm">Previous Semester Balance</span>
                            </div>
                            <p class="text-amber-500 text-[10px] mt-0.5 pl-4">Unpaid balance carried over from prior semester</p>
                        </td>
                        <td class="px-6 py-4 text-right font-mono-num font-bold text-amber-700 text-sm">
                            + ₱{{ number_format($previousBalance, 2) }}
                        </td>
                    </tr>
                    @endif

                    {{-- Remaining Balance Row --}}
                    @php $__grandTotal = ($balance ?? 0) + ($previousBalance ?? 0); @endphp
                    <tr style="background: linear-gradient(135deg, #4f46e5, #6366f1);">
                        <td class="px-6 py-5" colspan="1">
                            <div class="flex items-center gap-2">
                                <div class="w-2 h-2 rounded-full {{ $__grandTotal > 0 ? 'bg-amber-400 animate-pulse' : 'bg-emerald-400' }}"></div>
                                <span class="text-white font-black text-sm uppercase tracking-wider">Remaining Balance</span>
                            </div>
                            @if(($previousBalance ?? 0) > 0)
                            <p class="text-indigo-200 text-[10px] mt-0.5 pl-4">
                                ₱{{ number_format($balance ?? 0, 2) }} current + ₱{{ number_format($previousBalance, 2) }} previous
                            </p>
                            @endif
                        </td>
                        <td colspan="2" class="px-6 py-5 text-right">
                            <span class="font-mono-num font-extrabold text-2xl text-white tabular-nums">
                                ₱{{ number_format($__grandTotal, 2) }}
                            </span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    {{-- ── Online Payment Pending Notice ── --}}
    @if(session('payment_submitted'))
    <div class="bl-fade bl-no-print flex items-start gap-4 p-5 rounded-2xl border"
         style="background:#f0fdf4; border-color:#bbf7d0;">
        <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0"
             style="background:#dcfce7;">
            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
            </svg>
        </div>
        <div>
            <p class="font-bold text-emerald-800 text-sm">Payment Submitted for Verification</p>
            <p class="text-emerald-700 text-xs mt-1">Your proof of payment has been received. The cashier will verify and confirm your payment within 1–2 business days. You will see the update reflected in your ledger once confirmed.</p>
        </div>
    </div>
    @endif

</div>

{{-- ══════════════════════════════════════════════════════
     PAY ONLINE MODAL
══════════════════════════════════════════════════════ --}}
<div id="bl-pay-modal"
     class="hidden fixed inset-0 z-50 flex items-center justify-center p-4"
     style="background:rgba(15,23,42,.55); backdrop-filter:blur(6px);"
     onclick="if(event.target===this) this.classList.add('hidden')">

    <div class="w-full max-w-lg bg-white rounded-3xl shadow-2xl overflow-hidden"
         style="max-height:90vh; overflow-y:auto;">

        {{-- Modal Header --}}
        <div class="px-7 py-5 flex items-center justify-between"
             style="background:linear-gradient(135deg,#4f46e5,#7c3aed);">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                    </svg>
                </div>
                <div>
                    <p class="font-bold text-white text-base">Pay Online</p>
                    <p class="text-white/70 text-xs">Submit proof of payment for verification</p>
                </div>
            </div>
            <button onclick="document.getElementById('bl-pay-modal').classList.add('hidden')"
                    class="w-8 h-8 rounded-xl bg-white/20 hover:bg-white/30 flex items-center justify-center transition-colors text-white">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Balance Due Summary --}}
        <div class="mx-6 mt-5 p-4 rounded-2xl flex items-center justify-between"
             style="background:#fef3c7; border:1.5px solid #fde68a;">
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-amber-600">Outstanding Balance</p>
                <p class="font-mono-num text-2xl font-extrabold text-amber-800 mt-0.5">₱{{ number_format(($balance ?? 0) + ($previousBalance ?? 0), 2) }}</p>
            </div>
            <div class="w-12 h-12 rounded-xl flex items-center justify-center"
                 style="background:#fef9c3;">
                <svg class="w-6 h-6 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>

        {{-- ── STEP 1: Choose Payment Method ── --}}
        <div id="bl-step1" class="px-6 py-5 space-y-3">
            <p class="text-xs font-bold uppercase tracking-widest text-gray-400 mb-1">Choose how you want to pay</p>

            {{-- GCash --}}
            <button type="button" onclick="blGoToMethod('GCash')"
                    class="w-full flex items-center gap-4 px-4 py-3.5 rounded-2xl border-2 border-gray-100 bg-gray-50 hover:border-blue-300 hover:bg-blue-50 transition-all group">
                <div class="w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0 bg-blue-100 group-hover:bg-blue-200 transition-colors">
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <rect x="5" y="2" width="14" height="20" rx="2"/><path d="M12 18h.01"/>
                    </svg>
                </div>
                <div class="flex-1 text-left">
                    <p class="font-bold text-gray-800 text-sm">GCash</p>
                    <p class="text-xs text-gray-400">Opens GCash — send to PAC Finance</p>
                </div>
                <svg class="w-4 h-4 text-gray-300 group-hover:text-blue-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                </svg>
            </button>

            {{-- Maya --}}
            <button type="button" onclick="blGoToMethod('PayMaya')"
                    class="w-full flex items-center gap-4 px-4 py-3.5 rounded-2xl border-2 border-gray-100 bg-gray-50 hover:border-purple-300 hover:bg-purple-50 transition-all group">
                <div class="w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0 bg-purple-100 group-hover:bg-purple-200 transition-colors">
                    <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <rect x="5" y="2" width="14" height="20" rx="2"/><path d="M12 18h.01"/>
                    </svg>
                </div>
                <div class="flex-1 text-left">
                    <p class="font-bold text-gray-800 text-sm">Maya</p>
                    <p class="text-xs text-gray-400">Opens Maya — send to PAC Finance</p>
                </div>
                <svg class="w-4 h-4 text-gray-300 group-hover:text-purple-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                </svg>
            </button>

            {{-- Bank Transfer --}}
            <button type="button" onclick="blGoToMethod('Bank Transfer')"
                    class="w-full flex items-center gap-4 px-4 py-3.5 rounded-2xl border-2 border-gray-100 bg-gray-50 hover:border-emerald-300 hover:bg-emerald-50 transition-all group">
                <div class="w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0 bg-emerald-100 group-hover:bg-emerald-200 transition-colors">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/>
                    </svg>
                </div>
                <div class="flex-1 text-left">
                    <p class="font-bold text-gray-800 text-sm">Bank Transfer</p>
                    <p class="text-xs text-gray-400">BDO / BPI / Landbank — see account details</p>
                </div>
                <svg class="w-4 h-4 text-gray-300 group-hover:text-emerald-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                </svg>
            </button>

            <p class="text-center text-xs text-gray-300 pt-1">You will be redirected to complete your payment, then come back to upload your receipt.</p>
        </div>

        {{-- ── STEP 2: Account Details + Upload Form ── --}}
        <div id="bl-step2" class="hidden">

            {{-- Back button --}}
            <div class="px-6 pt-4">
                <button type="button" onclick="blBackToStep1()"
                        class="inline-flex items-center gap-1.5 text-xs font-semibold text-gray-400 hover:text-indigo-500 transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Back
                </button>
            </div>

            {{-- Account details per method --}}
            <div id="bl-details-gcash" class="hidden px-6 pt-3">
                <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-2">GCash Account</p>
                <div class="rounded-2xl overflow-hidden border border-blue-100">
                    <div class="px-4 py-3 flex items-center justify-between" style="background:#eff6ff;">
                        <div>
                            <p class="text-[10px] font-bold uppercase tracking-wider text-blue-400">Number</p>
                            <p class="font-mono font-extrabold text-blue-800 text-lg tracking-widest mt-0.5">0917 123 4567</p>
                        </div>
                        <button type="button" onclick="blCopy('09171234567', this)"
                                class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold text-blue-600 bg-white border border-blue-200 hover:bg-blue-50 transition-all">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 01-2-2V4a2 2 0 012-2h9a2 2 0 012 2v1"/></svg>
                            Copy
                        </button>
                    </div>
                    <div class="px-4 py-2 bg-white border-t border-blue-50">
                        <p class="text-xs text-gray-500">Account Name: <strong class="text-gray-800">Philippine Advent College</strong></p>
                    </div>
                </div>
                <a href="https://gcash.com" target="_blank"
                   class="mt-3 w-full flex items-center justify-center gap-2 py-2.5 rounded-xl text-sm font-bold text-white transition-all"
                   style="background:linear-gradient(135deg,#1d4ed8,#3b82f6);">
                    Open GCash App
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                </a>
            </div>

            <div id="bl-details-paymaya" class="hidden px-6 pt-3">
                <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-2">Maya Account</p>
                <div class="rounded-2xl overflow-hidden border border-purple-100">
                    <div class="px-4 py-3 flex items-center justify-between" style="background:#f5f3ff;">
                        <div>
                            <p class="text-[10px] font-bold uppercase tracking-wider text-purple-400">Number</p>
                            <p class="font-mono font-extrabold text-purple-800 text-lg tracking-widest mt-0.5">0917 123 4567</p>
                        </div>
                        <button type="button" onclick="blCopy('09171234567', this)"
                                class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold text-purple-600 bg-white border border-purple-200 hover:bg-purple-50 transition-all">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 01-2-2V4a2 2 0 012-2h9a2 2 0 012 2v1"/></svg>
                            Copy
                        </button>
                    </div>
                    <div class="px-4 py-2 bg-white border-t border-purple-50">
                        <p class="text-xs text-gray-500">Account Name: <strong class="text-gray-800">Philippine Advent College</strong></p>
                    </div>
                </div>
                <a href="https://maya.ph" target="_blank"
                   class="mt-3 w-full flex items-center justify-center gap-2 py-2.5 rounded-xl text-sm font-bold text-white transition-all"
                   style="background:linear-gradient(135deg,#7c3aed,#a855f7);">
                    Open Maya App
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                </a>
            </div>

            <div id="bl-details-bank" class="hidden px-6 pt-3 space-y-2">
                <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-2">Bank Account Details</p>
                @foreach([['BDO','1234-5678-90'],['BPI','9876-5432-10'],['Landbank','0001-1234-56']] as [$bank,$acct])
                <div class="rounded-2xl overflow-hidden border border-emerald-100">
                    <div class="px-4 py-3 flex items-center justify-between" style="background:#f0fdf4;">
                        <div>
                            <p class="text-[10px] font-bold uppercase tracking-wider text-emerald-500">{{ $bank }}</p>
                            <p class="font-mono font-extrabold text-emerald-800 text-sm tracking-widest mt-0.5">{{ $acct }}</p>
                        </div>
                        <button type="button" onclick="blCopy('{{ $acct }}', this)"
                                class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold text-emerald-600 bg-white border border-emerald-200 hover:bg-emerald-50 transition-all">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 01-2-2V4a2 2 0 012-2h9a2 2 0 012 2v1"/></svg>
                            Copy
                        </button>
                    </div>
                    <div class="px-4 py-2 bg-white border-t border-emerald-50">
                        <p class="text-xs text-gray-500">Account Name: <strong class="text-gray-800">Philippine Advent College</strong></p>
                    </div>
                </div>
                @endforeach
            </div>

            {{-- Upload form --}}
            <form method="POST"
                  action="{{ route('student.billing.pay-online') }}"
                  enctype="multipart/form-data"
                  class="px-6 pb-6 mt-4 space-y-4">
                @csrf
                <input type="hidden" name="school_year"     value="{{ $selectedYear ?? '' }}">
                <input type="hidden" name="semester"        value="{{ $selectedSemester ?? '' }}">
                <input type="hidden" name="payment_method"  id="bl-method-input" value="">

                <div class="border-t border-gray-100 pt-4">
                    <p class="text-xs font-bold uppercase tracking-widest text-gray-400 mb-3">After Paying — Upload Your Receipt</p>

                    <div class="mb-4">
                        <label class="block text-xs font-bold uppercase tracking-widest text-gray-400 mb-2">Amount Paid (₱) *</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 font-bold text-gray-400 select-none">₱</span>
                            <input type="number" name="amount" step="0.01" min="1"
                                   max="{{ ($balance ?? 0) + ($previousBalance ?? 0) }}"
                                   value="{{ ($balance ?? 0) + ($previousBalance ?? 0) }}"
                                   placeholder="0.00" required
                                   class="w-full pl-8 pr-4 py-3 rounded-xl border border-gray-200 bg-gray-50 text-gray-800 font-mono-num font-bold text-lg focus:outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 transition-all">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-xs font-bold uppercase tracking-widest text-gray-400 mb-2">Screenshot / Receipt *</label>
                        <label id="bl-upload-label"
                               class="flex items-center gap-3 w-full px-4 py-3 rounded-xl border-2 border-dashed border-gray-200 bg-gray-50 cursor-pointer transition-all hover:border-indigo-300 hover:bg-indigo-50/40"
                               for="bl-proof-input">
                            <svg class="w-4 h-4 text-gray-300 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                            </svg>
                            <div>
                                <p class="text-sm font-semibold text-gray-400" id="bl-upload-text">Click to upload screenshot / receipt</p>
                                <p class="text-xs text-gray-300">JPG, PNG, PDF — max 5MB</p>
                            </div>
                            <input id="bl-proof-input" type="file" name="proof_of_payment"
                                   accept="image/*,.pdf" required class="hidden"
                                   onchange="blPreviewProof(this)">
                        </label>
                        <img id="bl-proof-preview" src="" alt="Preview" class="hidden mt-2 w-full rounded-xl object-contain max-h-36 border border-indigo-100">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-widest text-gray-400 mb-2">Notes <span class="font-normal normal-case text-gray-300">(optional)</span></label>
                        <textarea name="notes" rows="2" placeholder="Any notes for the cashier…"
                                  class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 text-gray-800 text-sm resize-none focus:outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 transition-all"></textarea>
                    </div>
                </div>

                <div class="flex gap-3">
                    <button type="submit"
                            class="flex-1 py-3.5 rounded-2xl font-bold text-white text-sm transition-all"
                            style="background:linear-gradient(135deg,#4f46e5,#7c3aed);">
                        Submit Receipt
                    </button>
                    <button type="button"
                            onclick="document.getElementById('bl-pay-modal').classList.add('hidden')"
                            class="px-5 py-3.5 rounded-2xl font-semibold text-sm text-gray-500 bg-gray-100 hover:bg-gray-200 transition-all">
                        Cancel
                    </button>
                </div>

                <p class="text-center text-xs text-gray-300">
                    ⏱ Cashier will verify your payment within 1–2 business days.
                </p>
            </form>
        </div>

@push('scripts')
<script>
function blGoToMethod(method) {
    // Set hidden input
    document.getElementById('bl-method-input').value = method;

    // Hide step 1, show step 2
    document.getElementById('bl-step1').classList.add('hidden');
    document.getElementById('bl-step2').classList.remove('hidden');

    // Show correct account details
    ['gcash','paymaya','bank'].forEach(k => document.getElementById('bl-details-' + k).classList.add('hidden'));
    const key = method === 'GCash' ? 'gcash' : (method === 'PayMaya' ? 'paymaya' : 'bank');
    document.getElementById('bl-details-' + key).classList.remove('hidden');
}

function blBackToStep1() {
    document.getElementById('bl-step2').classList.add('hidden');
    document.getElementById('bl-step1').classList.remove('hidden');
    document.getElementById('bl-method-input').value = '';
}

function blCopy(text, btn) {
    navigator.clipboard.writeText(text).then(() => {
        const orig = btn.innerHTML;
        btn.innerHTML = '✅ Copied!';
        btn.disabled = true;
        setTimeout(() => { btn.innerHTML = orig; btn.disabled = false; }, 2000);
    });
}

function blPreviewProof(input) {
    const file    = input.files[0];
    const preview = document.getElementById('bl-proof-preview');
    const label   = document.getElementById('bl-upload-text');
    if (!file) return;
    label.textContent = '✅ ' + file.name;
    if (file.type.startsWith('image/')) {
        const reader = new FileReader();
        reader.onload = e => { preview.src = e.target.result; preview.classList.remove('hidden'); };
        reader.readAsDataURL(file);
    } else {
        preview.classList.add('hidden');
    }
}

document.addEventListener('keydown', e => {
    if (e.key === 'Escape') document.getElementById('bl-pay-modal')?.classList.add('hidden');
});
</script>
@endpush
@endsection