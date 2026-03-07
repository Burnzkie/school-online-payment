@extends('students.college.layouts.student-app')
@section('title', 'Dashboard')

@section('content')

{{-- ── Greeting Hero ── --}}
@php
    $p = min(100, max(0, $progress ?? 0));
    $r2 = 42; $c2 = 2*M_PI*$r2;
    $off2 = $c2*(1-$p/100);
    $ringColor = $p >= 100 ? '#34d399' : ($p >= 50 ? '#818cf8' : '#f59e0b');
@endphp
<div class="relative overflow-hidden rounded-3xl fade-up"
     style="background: linear-gradient(135deg, #eef2ff 0%, #e0e7ff 50%, #f0f9ff 100%); border: 1px solid #c7d2fe;">

    {{-- Decorative orbs --}}
    <div class="absolute -top-20 -right-20 w-80 h-80 rounded-full pointer-events-none"
         style="background: radial-gradient(circle, rgba(59,85,230,0.25) 0%, transparent 65%)"></div>
    <div class="absolute -bottom-16 -left-16 w-56 h-56 rounded-full pointer-events-none"
         style="background: radial-gradient(circle, rgba(99,130,255,0.15) 0%, transparent 65%)"></div>
    {{-- Subtle dot-grid overlay --}}
    <div class="absolute inset-0 pointer-events-none"
         style="background-image: radial-gradient(rgba(79,70,229,0.08) 1px, transparent 1px); background-size: 28px 28px;"></div>
    {{-- Bottom separator line --}}
    <div class="absolute bottom-0 left-0 right-0 h-px"
         style="background: linear-gradient(90deg, transparent, rgba(79,70,229,0.15), transparent);"></div>

    <div class="relative px-6 py-8 sm:px-10 sm:py-10 lg:py-12">

        {{-- TOP ROW: date badge (full width, stacks above everything) --}}
        <div class="mb-5">
            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-semibold"
                 style="background: #fff; color: #6b7280; border: 1px solid #e0e7ff;">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 flex-shrink-0"
                      style="box-shadow: 0 0 6px rgba(52,211,153,0.8); animation: pulse-glow 2s infinite;"></span>
                {{ now()->format('l, F j, Y') }}
            </div>
        </div>

        {{-- MAIN ROW: greeting left + ring right — ring is a fixed-width island --}}
        <div style="display: grid; grid-template-columns: 1fr 140px; align-items: center; gap: 24px;">

            {{-- Left: Greeting content --}}
            <div style="min-width: 0;">
                <h1 class="font-display text-3xl sm:text-4xl lg:text-5xl text-gray-800 leading-tight tracking-tight">
                    Welcome back,<br>
                    <span style="background: linear-gradient(90deg, #4f46e5, #7c3aed); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">
                        {{ explode(' ', auth()->user()->name ?? 'Student')[0] }}!
                    </span>
                </h1>

                <p class="mt-3 text-sm font-medium" style="color: #6b7280;">
                    {{ auth()->user()->course ?? auth()->user()->program ?? 'Student' }}
                    @if(auth()->user()->year_level)
                        <span style="color: #d1d5db;">&nbsp;·&nbsp;</span>
                        <span style="color: #6b7280;">{{ auth()->user()->year_level }} Year</span>
                    @endif
                </p>

                {{-- Balance alert — constrained to left column only --}}
                @if(($balance ?? 0) > 0)
                <div class="mt-5"
                     style="display: inline-flex; align-items: center; gap: 12px;
                            padding: 10px 16px; border-radius: 16px;
                            background: #fff1f2; border: 1px solid #fecdd3;
                            max-width: 100%;">
                    <div style="width:28px; height:28px; border-radius:10px; flex-shrink:0;
                                background: #fee2e2;
                                display:flex; align-items:center; justify-content:center;">
                        <svg width="14" height="14" fill="currentColor" viewBox="0 0 20 20" style="color:#e11d48;">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div style="min-width:0;">
                        <p style="font-size:11px; font-weight:800; color:#9f1239; margin:0; line-height:1;">Outstanding Balance</p>
                        <p style="font-size:11px; margin:3px 0 0; color:#e11d48; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                            ₱{{ number_format($balance ?? 0, 2) }} — payment due
                        </p>
                    </div>
                </div>
                @else
                <div class="mt-5"
                     style="display:inline-flex; align-items:center; gap:10px;
                            padding:10px 16px; border-radius:16px;
                            background: #ecfdf5; border: 1px solid #a7f3d0;">
                    <svg width="14" height="14" fill="currentColor" viewBox="0 0 20 20" style="color:#059669; flex-shrink:0;">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <span style="font-size:12px; font-weight:700; color:#065f46;">All fees settled — you're good to go!</span>
                </div>
                @endif
            </div>

            {{-- Right: Progress Ring — locked 140px column, never moves --}}
            <div style="display:flex; flex-direction:column; align-items:center; gap:10px; width:140px; flex-shrink:0;">
                <div style="position:relative; width:112px; height:112px;">
                    {{-- Glow --}}
                    <div style="position:absolute; inset:8px; border-radius:50%;
                                background: radial-gradient(circle, {{ $ringColor }}22, transparent 70%);
                                filter: blur(10px);"></div>
                    <svg width="112" height="112" viewBox="0 0 100 100"
                         style="transform: rotate(-90deg); position:relative; z-index:1; display:block;">
                        <circle cx="50" cy="50" r="{{ $r2 }}" fill="none"
                                stroke="rgba(79,70,229,0.12)" stroke-width="7"/>
                        <circle cx="50" cy="50" r="{{ $r2 }}" fill="none"
                                stroke="{{ $ringColor }}"
                                stroke-width="7" stroke-linecap="round"
                                stroke-dasharray="{{ round($c2,2) }}"
                                stroke-dashoffset="{{ round($off2,2) }}"
                                style="transition: stroke-dashoffset 1.2s cubic-bezier(.4,0,.2,1);
                                       filter: drop-shadow(0 0 5px {{ $ringColor }}90);"/>
                    </svg>
                    {{-- Centre text --}}
                    <div style="position:absolute; inset:0; display:flex; flex-direction:column;
                                align-items:center; justify-content:center; z-index:2;">
                        <span class="font-mono-num" style="color:#1f2937; font-weight:900; font-size:22px; line-height:1;">{{ $p }}%</span>
                        <span style="font-size:9px; font-weight:700; color:#6b7280;
                                     letter-spacing:.07em; margin-top:3px; text-transform:uppercase;">PAID</span>
                    </div>
                </div>
                <div style="text-align:center;">
                    <p style="font-size:11px; font-weight:600; color:#6b7280; white-space:nowrap; margin:0;">Payment Progress</p>
                    <p style="font-size:10px; color:#9ca3af; white-space:nowrap; margin:3px 0 0;">Current Semester</p>
                </div>
            </div>

        </div>
    </div>
</div>

<style>
@keyframes pulse-glow {
    0%, 100% { opacity: 1; box-shadow: 0 0 6px rgba(52,211,153,0.8); }
    50% { opacity: 0.6; box-shadow: 0 0 12px rgba(52,211,153,0.4); }
}
</style>

{{-- ── KPI Cards ── --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 fade-up fade-up-d1">

    {{-- Balance --}}
    <div class="col-span-2 sm:col-span-1 lg:col-span-1 card-lift rounded-2xl p-5 relative overflow-hidden"
         style="background: {{ ($balance ?? 0) > 0 ? '#fff1f2' : '#ecfdf5' }}; border: 1px solid {{ ($balance ?? 0) > 0 ? '#fecdd3' : '#a7f3d0' }};">
        <div class="absolute -top-4 -right-4 w-24 h-24 rounded-full opacity-20"
             style="background: radial-gradient(circle, white, transparent)"></div>
        <p class="text-xs font-bold uppercase tracking-wider text-gray-500 mb-2">Balance Due</p>
        <p class="font-mono-num text-2xl sm:text-3xl font-bold leading-tight" style="color: {{ ($balance ?? 0) > 0 ? '#e11d48' : '#059669' }};">
            ₱{{ number_format($balance ?? 0, 2) }}
        </p>
        <p class="text-xs mt-1" style="color: {{ ($balance ?? 0) > 0 ? '#9f1239' : '#065f46' }};">{{ ($balance ?? 0) > 0 ? 'Outstanding' : 'All clear ✓' }}</p>
    </div>

    {{-- Paid --}}
    <div class="card-lift rounded-2xl p-5 bg-white border border-slate-200 relative overflow-hidden">
        <div class="flex items-center gap-2 mb-2">
            <div class="w-7 h-7 rounded-lg flex items-center justify-center" style="background:#dcfce7;">
                <svg class="w-4 h-4 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
            </div>
            <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Paid</p>
        </div>
        <p class="font-mono-num text-xl font-bold text-emerald-600">₱{{ number_format($paid ?? 0, 2) }}</p>
    </div>

    {{-- Total --}}
    <div class="card-lift rounded-2xl p-5 bg-white border border-slate-200">
        <div class="flex items-center gap-2 mb-2">
            <div class="w-7 h-7 rounded-lg flex items-center justify-center bg-slate-100">
                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 11h.01M12 11h.01M15 11h.01M4 19h16a2 2 0 002-2V7a2 2 0 00-2-2H4a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
            </div>
            <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Total</p>
        </div>
        <p class="font-mono-num text-xl font-bold text-slate-800">₱{{ number_format($total ?? 0, 2) }}</p>
    </div>

    {{-- Next Due --}}
    <div class="card-lift rounded-2xl p-5 bg-white border border-slate-200">
        <div class="flex items-center gap-2 mb-2">
            <div class="w-7 h-7 rounded-lg flex items-center justify-center bg-amber-50">
                <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
            <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Next Due</p>
        </div>
        <p class="text-sm font-bold text-slate-800 leading-snug">
            {{ ($balance ?? 0) > 0 ? ($nextDueDate ?? 'See Billing') : '—' }}
        </p>
    </div>
</div>

{{-- ── Main Content Grid ── --}}
<div class="grid grid-cols-1 lg:grid-cols-5 gap-6 fade-up fade-up-d2">

    {{-- Progress + Quick Actions (3 cols) --}}
    <div class="lg:col-span-3 space-y-5">

        {{-- Payment progress card --}}
        <div class="bg-white rounded-2xl border border-slate-200 p-6 card-lift">
            <div class="flex items-center justify-between mb-5">
                <div>
                    <h3 class="font-semibold text-slate-800 text-base">Payment Progress</h3>
                    <p class="text-xs text-slate-400 mt-0.5">Current semester</p>
                </div>
                <span class="font-mono-num font-bold text-2xl" class="text-indigo-600">{{ $p }}%</span>
            </div>
            <div class="h-3 bg-slate-100 rounded-full overflow-hidden shadow-inner relative">
                <div class="h-full rounded-full relative overflow-hidden transition-all duration-1000"
                     style="width:{{ $p }}%; background: linear-gradient(90deg, #3b55e6, #7c3aed, #ec4899);">
                    <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/20 to-transparent"
                         style="animation: shimmer 2.5s infinite;"></div>
                </div>
            </div>
            <div class="flex justify-between mt-2.5">
                @foreach([25,50,75,100] as $m)
                <span class="text-[10px] font-bold {{ $p >= $m ? 'text-indigo-500' : 'text-slate-300' }}">{{ $m }}%</span>
                @endforeach
            </div>
            <div class="grid grid-cols-2 gap-3 mt-5">
                <div class="p-3.5 rounded-xl" style="background:#f0fdf4;">
                    <p class="text-[10px] font-bold text-emerald-600 uppercase tracking-wider">Amount Paid</p>
                    <p class="font-mono-num font-bold text-lg text-emerald-700 mt-1">₱{{ number_format($paid ?? 0, 2) }}</p>
                </div>
                <div class="p-3.5 rounded-xl bg-slate-50">
                    <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Remaining</p>
                    <p class="font-mono-num font-bold text-lg text-slate-700 mt-1">₱{{ number_format($balance ?? 0, 2) }}</p>
                </div>
            </div>
        </div>


        {{-- Status banner --}}
        @if(($balance ?? 0) <= 0)
        <div class="rounded-2xl p-5 flex items-center gap-4"
             style="background: linear-gradient(135deg,#ecfdf5,#d1fae5); border: 1px solid #a7f3d0;">
            <div class="w-12 h-12 rounded-2xl bg-emerald-500 flex items-center justify-center flex-shrink-0 shadow-lg">
                <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div>
                <p class="font-bold text-emerald-900">You're all paid up! 🎉</p>
                <p class="text-sm text-emerald-700 mt-0.5">No outstanding balance. Keep it up!</p>
            </div>
        </div>
        @else
        <div class="rounded-2xl p-5 flex items-center gap-4"
             style="background: linear-gradient(135deg,#fff7ed,#ffedd5); border: 1px solid #fed7aa;">
            <div class="w-12 h-12 rounded-2xl bg-amber-500 flex items-center justify-center flex-shrink-0 shadow-lg">
                <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="flex-1">
                <p class="font-bold text-amber-900">Payment Due Soon</p>
                <p class="text-sm text-amber-700 mt-0.5">Balance of ₱{{ number_format($balance ?? 0, 2) }} — due {{ $nextDueDate ?? 'see billing' }}</p>
            </div>
            <a href="{{ route('student.billing') }}"
               class="flex-shrink-0 px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold rounded-xl transition-colors shadow">
               View →
            </a>
        </div>
        @endif
    </div>

    {{-- Recent Payments (2 cols) --}}
    <div class="lg:col-span-2">
        <div class="bg-white rounded-2xl border border-slate-200 p-6 card-lift h-full">
            <div class="flex items-center justify-between mb-5">
                <div>
                    <h3 class="font-semibold text-slate-800 text-base">Recent Payments</h3>
                    <p class="text-xs text-slate-400 mt-0.5">Latest transactions</p>
                </div>
                <a href="{{ route('student.statements') }}"
                   class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 flex items-center gap-1 transition-colors">
                    View all
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>

            <div class="space-y-3">
                @forelse($recentPayments ?? [] as $payment)
                <div class="flex items-center gap-3 p-3.5 rounded-xl hover:bg-slate-50 transition-colors">
                    <div class="w-9 h-9 rounded-xl bg-emerald-100 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-slate-800 truncate">{{ $payment['date'] ?? '—' }}</p>
                        <p class="text-xs text-slate-400 mt-0.5">{{ $payment['method'] ?? 'Payment' }}</p>
                    </div>
                    <p class="font-mono-num font-bold text-emerald-600 text-sm flex-shrink-0">+₱{{ number_format($payment['amount'] ?? 0, 2) }}</p>
                </div>
                @empty
                <div class="text-center py-10 px-4">
                    <div class="w-14 h-14 rounded-2xl bg-slate-100 flex items-center justify-center mx-auto mb-3">
                        <svg class="w-7 h-7 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/>
                        </svg>
                    </div>
                    <p class="text-sm font-medium text-slate-500">No payments yet</p>
                    <p class="text-xs text-slate-400 mt-1">Your history will appear here</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<style>
@keyframes shimmer {
    0% { transform: translateX(-100%); }
    100% { transform: translateX(200%); }
}
</style>
@endsection