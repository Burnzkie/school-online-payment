{{-- resources/views/students/hs/installments.blade.php --}}
@extends('students.hs.layouts.hs-app')
@section('title', 'Installment Plans')

@push('styles')
<style>
.hi-plan-card {
    border-radius: 20px; cursor: pointer;
    transition: all 0.25s ease;
    border: 2px solid transparent;
    position: relative; overflow: hidden;
}
.hi-plan-card:hover { transform: translateY(-5px); }
.hi-plan-card.selected {
    border-color: #4f46e5 !important;
    box-shadow: 0 0 0 4px rgba(79,70,229,0.15), 0 20px 40px rgba(79,70,229,0.15);
}
.hi-plan-card input[type="radio"] { position: absolute; opacity: 0; }
.hi-plan-card .hi-check {
    position: absolute; top: 14px; right: 14px;
    width: 22px; height: 22px; border-radius: 50%;
    border: 2px solid #d1d5db;
    display: flex; align-items: center; justify-content: center;
    transition: all 0.2s ease;
}
.hi-plan-card.selected .hi-check {
    background: linear-gradient(135deg, #4f46e5, #6366f1);
    border-color: transparent;
}

/* Schedule timeline */
.hi-timeline { position: relative; }
.hi-timeline::before {
    content: '';
    position: absolute; left: 16px; top: 0; bottom: 0;
    width: 2px;
    background: linear-gradient(180deg, #6366f1, rgba(99,102,241,0.1));
}
.hi-sched-item { position: relative; padding: 0 0 20px 48px; }
.hi-sched-dot {
    position: absolute; left: 9px; top: 4px;
    width: 16px; height: 16px; border-radius: 50%;
    transition: all 0.2s ease;
}
.hi-sched-dot.paid { background: linear-gradient(135deg, #10b981, #34d399); box-shadow: 0 0 10px rgba(16,185,129,0.5); }
.hi-sched-dot.overdue { background: linear-gradient(135deg, #f43f5e, #fb7185); box-shadow: 0 0 10px rgba(244,63,94,0.4); animation: hs-pulse 1.5s ease-in-out infinite; }
.hi-sched-dot.upcoming { background: #e0e7ff; border: 2px solid #6366f1; }

@keyframes hi-fadein {
    from { opacity: 0; transform: translateY(12px); }
    to { opacity: 1; transform: translateY(0); }
}
.hi-fade { animation: hi-fadein .45s ease both; }
.hi-d1 { animation-delay: .06s; }
.hi-d2 { animation-delay: .12s; }
.hi-d3 { animation-delay: .18s; }
</style>
@endpush

@section('content')
@php
    $user = auth()->user();
    $isJHS = str_contains(strtolower($user->level_group ?? ''), 'junior');
    $isSHS = str_contains(strtolower($user->level_group ?? ''), 'senior');
    $levelLabel = $isJHS ? 'Junior High' : ($isSHS ? 'Senior High' : 'High School');
    $accentColor = $isJHS ? '#06b6d4' : '#06b6d4';
@endphp

<div class="space-y-6">

    {{-- ── Page Header ── --}}
    <div class="hi-fade">
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider mb-3"
             style="background: #eef2ff; color: #4f46e5; border: 1px solid #c7d2fe;">
            <span class="w-1.5 h-1.5 rounded-full" style="background: {{ $accentColor }};"></span>
            {{ $levelLabel }} · Installments
        </div>
        <h1 class="font-display text-4xl sm:text-5xl text-gray-800 leading-tight">Installment Plans</h1>
        <p class="text-sm mt-1.5 text-gray-500">
            @if($isJHS)
                Split your annual fees into manageable payments.
            @else
                Split your semester fees into manageable payments.
            @endif
        </p>
    </div>

    {{-- ── No balance notice ── --}}
    @if(($balance ?? 0) <= 0 && !isset($activePlan))
    <div class="hi-fade rounded-2xl p-6 flex items-center gap-4"
         style="background: #ecfdf5; border: 1px solid #a7f3d0;">
        <div class="text-3xl">🎉</div>
        <div>
            <p class="font-bold text-emerald-800">Nothing to pay right now!</p>
            <p class="text-sm mt-0.5 text-emerald-700">Your balance is fully settled. Installment plans will appear when you have outstanding fees.</p>
        </div>
    </div>
    @endif

    {{-- ── Active Plan View ── --}}
    @if(isset($activePlan))
    <div class="hi-fade rounded-2xl overflow-hidden" style="border: 1px solid rgba(14,165,233,0.35);">
        <div class="px-6 py-5 flex items-center justify-between"
             style="background: linear-gradient(135deg, #eef2ff, #e0e7ff); border-bottom: 1px solid #c7d2fe;">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center"
                     style="background: linear-gradient(135deg, #4f46e5, #6366f1);">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="font-bold text-indigo-900">Active Installment Plan</p>
                    <p class="text-xs mt-0.5 text-indigo-600">
                        {{ $activePlan->total_installments }}-part · ₱{{ number_format($activePlan->amount_per_installment, 2) }} each
                        · {{ $activePlan->school_year }}
                        @if($isSHS) · {{ $activePlan->semester == '1' ? '1st' : '2nd' }} Sem @endif
                    </p>
                </div>
            </div>
            <span class="hs-badge hs-badge-green">Active</span>
        </div>

        {{-- Schedule timeline --}}
        <div class="p-6" style="background: #f8faff;">
            <h3 class="font-bold text-gray-800 mb-5 text-sm">Payment Schedule</h3>
            <div class="hi-timeline">
                @foreach($activePlan->schedules ?? [] as $sched)
                @php
                    $isPaid = $sched->is_paid;
                    $isOverdue = $sched->is_overdue && !$isPaid;
                    $dotClass = $isPaid ? 'paid' : ($isOverdue ? 'overdue' : 'upcoming');
                @endphp
                <div class="hi-sched-item">
                    <div class="hi-sched-dot {{ $dotClass }}"></div>
                    <div class="rounded-xl p-4 flex items-center justify-between gap-3"
                         style="background: #ffffff; border: 1px solid {{ $isPaid ? '#a7f3d0' : ($isOverdue ? '#fecaca' : '#e5e7eb') }};">
                        <div>
                            <div class="flex items-center gap-2 mb-1">
                                <p class="text-sm font-bold text-gray-800">Installment {{ $sched->installment_number }}</p>
                                @if($isPaid)
                                    <span class="hs-badge hs-badge-green text-[9px]">Paid</span>
                                @elseif($isOverdue)
                                    <span class="hs-badge hs-badge-red text-[9px]">Overdue</span>
                                @else
                                    <span class="hs-badge hs-badge-amber text-[9px]">Upcoming</span>
                                @endif
                            </div>
                            <p class="text-xs text-gray-400">
                                Due: {{ \Carbon\Carbon::parse($sched->due_date)->format('M d, Y') }}
                                @if($isPaid && $sched->paid_at)
                                    · Paid: {{ \Carbon\Carbon::parse($sched->paid_at)->format('M d, Y') }}
                                @endif
                            </p>
                        </div>
                        <p class="font-mono-num font-bold text-gray-800 text-lg">₱{{ number_format($sched->amount_due, 2) }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- ── Choose / Setup Plan ── --}}
    @elseif(($balance ?? 0) > 0)
    <form method="POST" action="{{ route('hs.installments.choose') }}" id="hi-form">
        @csrf
        <input type="hidden" name="school_year" value="{{ $schoolYear ?? date('Y').'-'.(date('Y')+1) }}">
        @if($isSHS)
        <input type="hidden" name="semester" value="{{ $semester ?? '1' }}">
        @else
        <input type="hidden" name="semester" value="0">
        @endif

        {{-- Balance summary --}}
        <div class="hi-fade hi-d1 rounded-2xl p-5 flex items-center gap-4 mb-6"
             style="background: #eef2ff; border: 1px solid #c7d2fe;">
            <div class="text-3xl">💰</div>
            <div class="flex-1">
                <p class="font-bold text-indigo-900">Outstanding Balance</p>
                <p class="font-mono-num text-2xl font-extrabold mt-1 text-indigo-700">₱{{ number_format($balance ?? 0, 2) }}</p>
            </div>
            <div>
                <p class="text-xs text-right text-gray-400">{{ $schoolYear ?? date('Y').'-'.(date('Y')+1) }}</p>
                @if($isSHS)
                <p class="text-xs text-right mt-0.5 text-indigo-500">{{ ($semester ?? '1') == '1' ? '1st' : '2nd' }} Semester</p>
                @else
                <p class="text-xs text-right mt-0.5 text-indigo-500">Annual · No semester</p>
                @endif
            </div>
        </div>

        {{-- Plan choices --}}
        <h3 class="font-bold text-gray-800 mb-4">Choose Your Payment Plan</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 hi-fade hi-d2" id="hi-plans">
            @php
                $plans = [
                    ['value' => 'full', 'parts' => 1, 'emoji' => '⚡', 'title' => 'Pay in Full', 'desc' => 'One-time complete payment', 'color' => '#10b981', 'gradient' => 'linear-gradient(135deg, #ecfdf5, #d1fae5)'],
                    ['value' => '2',    'parts' => 2, 'emoji' => '✌️', 'title' => '2 Installments', 'desc' => 'Split into 2 equal payments', 'color' => '#06b6d4', 'gradient' => 'linear-gradient(135deg, #ecfeff, #cffafe)'],
                    ['value' => '3',    'parts' => 3, 'emoji' => '🎯', 'title' => '3 Installments', 'desc' => 'Split into 3 equal payments', 'color' => '#0ea5e9', 'gradient' => 'linear-gradient(135deg, #eef2ff, #e0e7ff)'],
                    ['value' => '4',    'parts' => 4, 'emoji' => '🏆', 'title' => '4 Installments', 'desc' => 'Split into 4 equal payments', 'color' => '#06b6d4', 'gradient' => 'linear-gradient(135deg, #fdf2f8, #fce7f3)'],
                ];
                $balance = $balance ?? 0;
            @endphp
            @foreach($plans as $plan)
            @php $perPart = $plan['parts'] > 0 ? $balance / $plan['parts'] : $balance; @endphp
            <label class="hi-plan-card block" style="background: {{ $plan['gradient'] }}; border-color: {{ $plan['color'] }}44;">
                <input type="radio" name="plan_type" value="{{ $plan['value'] }}"
                       {{ $loop->first ? 'checked' : '' }}
                       onchange="document.querySelectorAll('.hi-plan-card').forEach(c=>c.classList.remove('selected')); this.closest('.hi-plan-card').classList.add('selected')">
                <div class="hi-check">
                    <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="p-5 pb-5">
                    <div class="text-3xl mb-3">{{ $plan['emoji'] }}</div>
                    <p class="font-bold text-gray-800 text-base mb-1">{{ $plan['title'] }}</p>
                    <p class="text-xs mb-4 text-gray-500">{{ $plan['desc'] }}</p>
                    <div class="pt-3 border-t border-gray-200">
                        <p class="text-xs font-semibold uppercase tracking-wide mb-1" style="color: {{ $plan['color'] }}; opacity: 0.8;">
                            {{ $plan['parts'] > 1 ? 'Per Installment' : 'Total Amount' }}
                        </p>
                        <p class="font-mono-num font-extrabold text-xl text-gray-800">₱{{ number_format($perPart, 2) }}</p>
                    </div>
                </div>
            </label>
            @endforeach
        </div>

        <div class="mt-6 hi-fade hi-d3">
            <button type="submit"
                    class="w-full sm:w-auto px-8 py-3.5 rounded-2xl font-bold text-white text-base transition-all"
                    style="background: linear-gradient(135deg, #4f46e5, #6366f1);"
                    onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 15px 35px rgba(79,70,229,0.3)';"
                    onmouseout="this.style.transform=''; this.style.boxShadow='';">
                🚀 Confirm Installment Plan
            </button>
        </div>
    </form>

    <script>
        // Mark first plan as selected on load
        document.addEventListener('DOMContentLoaded', function() {
            const first = document.querySelector('.hi-plan-card');
            if (first) first.classList.add('selected');
        });
    </script>
    @endif
</div>
@endsection