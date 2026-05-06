@extends('students.college.layouts.student-app')
@section('title', 'Dashboard')

@section('content')
@php
    $p      = min(100, max(0, $progress ?? 0));
    $r2     = 42;
    $c2     = 2 * M_PI * $r2;
    $off2   = $c2 * (1 - $p / 100);
    $ringColor = $p >= 100 ? '#34d399' : ($p >= 50 ? '#818cf8' : '#f59e0b');
@endphp

{{-- ── Hero ── --}}
<div class="relative overflow-hidden rounded-3xl fade-up"
     style="background: linear-gradient(135deg, var(--primary-light) 0%, var(--bg3) 100%);
            border: 1px solid var(--primary-border);">

    {{-- Decorative blobs --}}
    <div class="absolute -top-20 -right-20 w-72 h-72 rounded-full pointer-events-none"
         style="background: radial-gradient(circle, rgba(79,70,229,0.12) 0%, transparent 65%)"></div>
    <div class="absolute -bottom-12 -left-12 w-48 h-48 rounded-full pointer-events-none"
         style="background: radial-gradient(circle, rgba(99,102,241,0.08) 0%, transparent 65%)"></div>

    <div class="relative px-6 py-8 sm:px-10">

        {{-- Date badge --}}
        <div class="mb-5">
            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-semibold"
                 style="background: var(--bg2); color: var(--muted); border: 1px solid var(--border);">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 flex-shrink-0 col-pulse"></span>
                {{ now()->format('l, F j, Y') }}
            </div>
        </div>

        {{-- Greeting row --}}
        <div class="flex items-center justify-between gap-6 flex-wrap sm:flex-nowrap">

            {{-- Left: text --}}
            <div class="min-w-0 flex-1">
                <h1 class="font-bold text-3xl sm:text-4xl leading-tight" style="color: var(--text)">
                    Welcome back,<br>
                    <span style="background: linear-gradient(90deg, var(--primary), var(--primary-muted));
                                 -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">
                        {{ explode(' ', auth()->user()->name ?? 'Student')[0] }}!
                    </span>
                </h1>

                <p class="mt-2 text-sm font-medium" style="color: var(--muted)">
                    {{ auth()->user()->program ?? 'Student' }}
                    @if(auth()->user()->year_level)
                        &nbsp;·&nbsp; {{ auth()->user()->year_level }} Year
                    @endif
                </p>

                {{-- Balance alert --}}
                <div class="mt-5 inline-flex items-center gap-3 px-4 py-2.5 rounded-2xl"
                     style="{{ ($balance ?? 0) > 0
                         ? 'background: var(--danger-light); border: 1px solid var(--danger-border);'
                         : 'background: var(--success-light); border: 1px solid var(--success-border);' }}">
                    @if(($balance ?? 0) > 0)
                        <div class="w-7 h-7 rounded-lg flex items-center justify-center flex-shrink-0"
                             style="background: var(--danger-light);">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"
                                 style="color: var(--danger); min-width: 1rem;">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs font-bold" style="color: var(--danger)">Outstanding Balance</p>
                            <p class="text-xs mt-0.5 font-semibold" style="color: var(--danger)">
                                ₱{{ number_format($balance ?? 0, 2) }} — payment due
                            </p>
                        </div>
                    @else
                        <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"
                             style="color: var(--success); min-width: 1rem;">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span class="text-xs font-bold" style="color: var(--success)">
                            All fees settled — you're good to go!
                        </span>
                    @endif
                </div>
            </div>

            {{-- Right: Progress ring --}}
            <div class="flex flex-col items-center gap-2 flex-shrink-0">
                <div style="position: relative; width: 100px; height: 100px;">
                    <svg width="100" height="100" viewBox="0 0 100 100"
                         style="transform: rotate(-90deg); display: block;">
                        <circle cx="50" cy="50" r="{{ $r2 }}" fill="none"
                                stroke="rgba(79,70,229,0.12)" stroke-width="7"/>
                        <circle cx="50" cy="50" r="{{ $r2 }}" fill="none"
                                stroke="{{ $ringColor }}" stroke-width="7"
                                stroke-linecap="round"
                                stroke-dasharray="{{ round($c2, 2) }}"
                                stroke-dashoffset="{{ round($off2, 2) }}"
                                style="transition: stroke-dashoffset 1.2s cubic-bezier(.4,0,.2,1);
                                       filter: drop-shadow(0 0 4px {{ $ringColor }}90);"/>
                    </svg>
                    <div style="position: absolute; inset: 0; display: flex; flex-direction: column;
                                align-items: center; justify-content: center;">
                        <span class="font-mono-num font-black" style="font-size: 20px; line-height: 1; color: var(--text)">
                            {{ $p }}%
                        </span>
                        <span style="font-size: 8px; font-weight: 700; color: var(--muted);
                                     letter-spacing: .07em; margin-top: 3px; text-transform: uppercase;">
                            PAID
                        </span>
                    </div>
                </div>
                <div class="text-center">
                    <p class="text-xs font-semibold" style="color: var(--muted)">Payment Progress</p>
                    <p style="font-size: 10px; color: var(--muted-light)">Current Semester</p>
                </div>
            </div>

        </div>
    </div>
</div>

{{-- ── KPI Cards ── --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 fade-up fade-up-d1">

    {{-- Balance --}}
    <div class="col-span-2 sm:col-span-1 card-lift rounded-2xl p-5 relative overflow-hidden"
         style="{{ ($balance ?? 0) > 0
             ? 'background: var(--danger-light); border: 1px solid var(--danger-border);'
             : 'background: var(--success-light); border: 1px solid var(--success-border);' }}">
        <p class="text-xs font-bold uppercase tracking-wider mb-2" style="color: var(--muted)">Balance Due</p>
        <p class="font-mono-num text-2xl font-bold leading-tight"
           style="color: {{ ($balance ?? 0) > 0 ? 'var(--danger)' : 'var(--success)' }}">
            ₱{{ number_format($balance ?? 0, 2) }}
        </p>
        <p class="text-xs mt-1 font-semibold"
           style="color: {{ ($balance ?? 0) > 0 ? 'var(--danger)' : 'var(--success)' }}">
            {{ ($balance ?? 0) > 0 ? 'Outstanding' : 'All clear ✓' }}
        </p>
    </div>

    {{-- Paid --}}
    <div class="card-lift rounded-2xl p-5 relative overflow-hidden"
         style="background: var(--bg2); border: 1px solid var(--border);">
        <div class="flex items-center gap-2 mb-2">
            <div class="w-7 h-7 rounded-lg flex items-center justify-center flex-shrink-0"
                 style="background: var(--success-light);">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"
                     style="color: var(--success); min-width: 1rem;">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
            </div>
            <p class="text-xs font-bold uppercase tracking-wider" style="color: var(--muted-light)">Paid</p>
        </div>
        <p class="font-mono-num text-xl font-bold" style="color: var(--success)">
            ₱{{ number_format($paid ?? 0, 2) }}
        </p>
    </div>

    {{-- Total --}}
    <div class="card-lift rounded-2xl p-5"
         style="background: var(--bg2); border: 1px solid var(--border);">
        <div class="flex items-center gap-2 mb-2">
            <div class="w-7 h-7 rounded-lg flex items-center justify-center flex-shrink-0"
                 style="background: var(--bg3);">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"
                     style="color: var(--muted); min-width: 1rem;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 11h.01M12 11h.01M15 11h.01M4 19h16a2 2 0 002-2V7a2 2 0 00-2-2H4a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
            </div>
            <p class="text-xs font-bold uppercase tracking-wider" style="color: var(--muted-light)">Total</p>
        </div>
        <p class="font-mono-num text-xl font-bold" style="color: var(--text)">
            ₱{{ number_format($total ?? 0, 2) }}
        </p>
    </div>

    {{-- Prev. Balance --}}
    <div class="card-lift rounded-2xl p-5 relative overflow-hidden"
         style="{{ ($previousBalance ?? 0) > 0
             ? 'background: var(--warning-light); border: 1px solid var(--warning-border);'
             : 'background: var(--bg2); border: 1px solid var(--border);' }}">
        <div class="flex items-center gap-2 mb-2">
            <div class="w-7 h-7 rounded-lg flex items-center justify-center flex-shrink-0"
                 style="background: {{ ($previousBalance ?? 0) > 0 ? 'var(--warning-light)' : 'var(--bg3)' }};">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"
                     style="color: {{ ($previousBalance ?? 0) > 0 ? 'var(--warning)' : 'var(--muted)' }}; min-width: 1rem;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <p class="text-xs font-bold uppercase tracking-wider" style="color: var(--muted-light)">Prev. Balance</p>
        </div>
        <p class="font-mono-num text-xl font-bold"
           style="color: {{ ($previousBalance ?? 0) > 0 ? 'var(--warning)' : 'var(--text)' }}">
            ₱{{ number_format($previousBalance ?? 0, 2) }}
        </p>
        <p class="text-xs mt-1 font-semibold"
           style="color: {{ ($previousBalance ?? 0) > 0 ? 'var(--warning)' : 'var(--muted)' }}">
            {{ ($previousBalance ?? 0) > 0 ? 'Carried over' : 'No carryover ✓' }}
        </p>
    </div>

</div>

{{-- ── Main Content Grid ── --}}
<div class="grid grid-cols-1 lg:grid-cols-5 gap-6 fade-up fade-up-d2">

    {{-- Payment Progress + Status (3 cols) --}}
    <div class="lg:col-span-3 space-y-5">

        {{-- Payment progress card --}}
        <div class="rounded-2xl p-6 card-lift"
             style="background: var(--bg2); border: 1px solid var(--border);">
            <div class="flex items-center justify-between mb-5">
                <div>
                    <h3 class="font-semibold text-base" style="color: var(--text)">Payment Progress</h3>
                    <p class="text-xs mt-0.5" style="color: var(--muted)">Current semester</p>
                </div>
                <span class="font-mono-num font-bold text-2xl" style="color: var(--primary)">{{ $p }}%</span>
            </div>

            {{-- Progress bar --}}
            <div class="h-3 rounded-full overflow-hidden" style="background: var(--bg3);">
                <div class="h-full rounded-full relative overflow-hidden"
                     style="width: {{ $p }}%;
                            background: linear-gradient(90deg, var(--primary), var(--primary-muted));
                            transition: width 1s ease;">
                    <div class="absolute inset-0"
                         style="background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
                                animation: shimmer 2.5s infinite;"></div>
                </div>
            </div>

            <div class="flex justify-between mt-2">
                @foreach([25, 50, 75, 100] as $m)
                    <span class="text-[10px] font-bold"
                          style="color: {{ $p >= $m ? 'var(--primary)' : 'var(--muted-light)' }}">
                        {{ $m }}%
                    </span>
                @endforeach
            </div>

            <div class="grid grid-cols-2 gap-3 mt-5">
                <div class="p-4 rounded-xl" style="background: var(--success-light); border: 1px solid var(--success-border);">
                    <p class="text-[10px] font-bold uppercase tracking-wider" style="color: var(--success)">Amount Paid</p>
                    <p class="font-mono-num font-bold text-lg mt-1" style="color: var(--success)">
                        ₱{{ number_format($paid ?? 0, 2) }}
                    </p>
                </div>
                <div class="p-4 rounded-xl" style="background: var(--bg3); border: 1px solid var(--border);">
                    <p class="text-[10px] font-bold uppercase tracking-wider" style="color: var(--muted)">Remaining</p>
                    <p class="font-mono-num font-bold text-lg mt-1" style="color: var(--text)">
                        ₱{{ number_format($balance ?? 0, 2) }}
                    </p>
                </div>
            </div>

            {{-- Previous balance carryover notice --}}
            @if(($previousBalance ?? 0) > 0)
            <div class="mt-3 px-4 py-3 rounded-xl flex items-center gap-3"
                 style="background: var(--warning-light); border: 1px solid var(--warning-border);">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                     stroke-width="2" style="color: var(--warning); min-width: 1rem;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-xs font-semibold" style="color: var(--warning)">
                    Includes <span class="font-bold">₱{{ number_format($previousBalance, 2) }}</span> carried over from previous semester
                </p>
            </div>
            @endif
        </div>

        {{-- Status banner --}}
        @if(($balance ?? 0) <= 0)
        <div class="rounded-2xl p-5 flex items-center gap-4"
             style="background: var(--success-light); border: 1px solid var(--success-border);">
            <div class="w-11 h-11 rounded-2xl flex items-center justify-center flex-shrink-0"
                 style="background: var(--success);">
                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20"
                     style="min-width: 1.25rem;">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div>
                <p class="font-bold" style="color: var(--success)">You're all paid up! 🎉</p>
                <p class="text-sm mt-0.5" style="color: var(--success)">No outstanding balance. Keep it up!</p>
            </div>
        </div>
        @else
        <div class="rounded-2xl p-5 flex items-center gap-4"
             style="background: var(--warning-light); border: 1px solid var(--warning-border);">
            <div class="w-11 h-11 rounded-2xl flex items-center justify-center flex-shrink-0"
                 style="background: var(--warning);">
                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20"
                     style="min-width: 1.25rem;">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="flex-1 min-w-0">
                <p class="font-bold" style="color: var(--warning)">Payment Due Soon</p>
                <p class="text-sm mt-0.5 truncate" style="color: var(--warning)">
                    Balance of ₱{{ number_format($balance ?? 0, 2) }}
                    @if(($previousBalance ?? 0) > 0)
                        — includes ₱{{ number_format($previousBalance, 2) }} carried over
                    @endif
                </p>
            </div>
            <a href="{{ route('student.billing') }}"
               class="flex-shrink-0 px-4 py-2 rounded-xl text-xs font-bold text-white transition-all"
               style="background: var(--warning);">
                View →
            </a>
        </div>
        @endif
    </div>

    {{-- Recent Payments (2 cols) --}}
    <div class="lg:col-span-2">
        <div class="rounded-2xl p-6 card-lift h-full"
             style="background: var(--bg2); border: 1px solid var(--border);">
            <div class="flex items-center justify-between mb-5">
                <div>
                    <h3 class="font-semibold text-base" style="color: var(--text)">Recent Payments</h3>
                    <p class="text-xs mt-0.5" style="color: var(--muted)">Latest transactions</p>
                </div>
                <a href="{{ route('student.statements') }}"
                   class="text-xs font-semibold flex items-center gap-1 transition-colors"
                   style="color: var(--primary)">
                    View all
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                         style="min-width: 0.75rem;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>

            <div class="space-y-2">
                @forelse($recentPayments ?? [] as $payment)
                <div class="flex items-center gap-3 p-3 rounded-xl transition-colors"
                     style="border: 1px solid transparent;"
                     onmouseover="this.style.background='var(--bg3)'; this.style.borderColor='var(--border)'"
                     onmouseout="this.style.background=''; this.style.borderColor='transparent'">
                    <div class="w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0"
                         style="background: var(--success-light);">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                             stroke-width="2.5" style="color: var(--success); min-width: 1rem;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold truncate" style="color: var(--text)">
                            {{ $payment['date'] ?? '—' }}
                        </p>
                        <p class="text-xs mt-0.5" style="color: var(--muted)">
                            {{ $payment['method'] ?? 'Payment' }}
                        </p>
                    </div>
                    <p class="font-mono-num font-bold text-sm flex-shrink-0" style="color: var(--success)">
                        +₱{{ number_format($payment['amount'] ?? 0, 2) }}
                    </p>
                </div>
                @empty
                <div class="text-center py-10 px-4">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center mx-auto mb-3"
                         style="background: var(--bg3);">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                             stroke-width="1.5" style="color: var(--muted); min-width: 1.25rem;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75"/>
                        </svg>
                    </div>
                    <p class="text-sm font-medium" style="color: var(--muted)">No payments yet</p>
                    <p class="text-xs mt-1" style="color: var(--muted-light)">Your history will appear here</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<style>
    @keyframes shimmer {
        0%   { transform: translateX(-100%); }
        100% { transform: translateX(200%); }
    }
</style>
@endsection