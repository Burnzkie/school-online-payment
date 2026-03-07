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
            @if(!isset($activePlan) && ($balance ?? 0) > 0)
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
                            @foreach($availableYears ?? ['2025-2026', '2024-2025', '2023-2024'] as $year)
                                <option value="{{ $year }}" {{ ($selectedYear ?? '2025-2026') === $year ? 'selected' : '' }}>{{ $year }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-1.5">Semester</label>
                        <select name="semester" class="bl-select w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 text-sm font-semibold"
                                onchange="this.form.submit()">
                            <option value="1"      {{ ($selectedSemester ?? '2') === '1'      ? 'selected' : '' }}>1st Semester</option>
                            <option value="2"      {{ ($selectedSemester ?? '2') === '2'      ? 'selected' : '' }}>2nd Semester</option>
                            <option value="summer" {{ ($selectedSemester ?? '2') === 'summer' ? 'selected' : '' }}>Summer</option>
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
                            {{ $selectedYear ?? '2025-2026' }} —
                            @switch($selectedSemester ?? '2')
                                @case('1') 1st Semester @break
                                @case('summer') Summer @break
                                @default 2nd Semester
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
                $paidPct = ($totalCharges ?? 0) > 0 ? round((($paid ?? 0) / ($totalCharges ?? 1)) * 100) : 0;
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
                        ₱{{ number_format($balance ?? 0, 2) }}
                    </p>
                    <div class="bl-progress-track">
                        <div class="bl-progress-fill" style="width: {{ $paidPct }}%"></div>
                    </div>
                    <div class="flex items-center justify-between mt-2">
                        <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-white/20 backdrop-blur-sm">
                            <span class="w-1.5 h-1.5 rounded-full {{ ($balance ?? 0) > 0 ? 'bg-amber-300 animate-pulse' : 'bg-emerald-300' }}"></span>
                            {{ ($balance ?? 0) > 0 ? 'Outstanding' : 'Fully Paid ✓' }}
                        </div>
                        <span class="text-xs font-bold opacity-60">{{ $paidPct }}% paid</span>
                    </div>
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
                    <p class="text-gray-400 text-xs">{{ $selectedYear ?? '2025-2026' }} — full record</p>
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
                        <td class="px-6 py-4 text-right font-mono-num font-bold text-emerald-600 text-sm">₱{{ number_format($totalPayments ?? $paid ?? 0, 2) }}</td>
                    </tr>

                    {{-- Remaining Balance Row --}}
                    <tr style="background: linear-gradient(135deg, #4f46e5, #6366f1);">
                        <td class="px-6 py-5">
                            <div class="flex items-center gap-2">
                                <div class="w-2 h-2 rounded-full {{ ($balance ?? 0) > 0 ? 'bg-amber-400 animate-pulse' : 'bg-emerald-400' }}"></div>
                                <span class="text-white font-black text-sm uppercase tracking-wider">Remaining Balance</span>
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