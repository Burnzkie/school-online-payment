{{-- resources/views/students/hs/dashboard.blade.php --}}
@extends('students.hs.layouts.hs-app')
@section('title', 'Dashboard')

@push('styles')
<style>
/* ── HS Dashboard Specific Styles ── */
@keyframes hs-spin-slow { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
@keyframes hs-counter { from { opacity: 0; transform: scale(0.7); } to { opacity: 1; transform: scale(1); } }
@keyframes hs-streak { 0%,100% { transform: scale(1) rotate(-2deg); } 50% { transform: scale(1.08) rotate(2deg); } }

.xp-ring-spin { animation: hs-spin-slow 12s linear infinite; }
.stat-counter  { animation: hs-counter .6s cubic-bezier(.34,1.56,.64,1) both; }
.streak-badge  { animation: hs-streak 2s ease-in-out infinite; }

/* Hero card */
.hs-hero {
    background: linear-gradient(140deg, #eef2ff 0%, #e0e7ff 45%, #f0f9ff 100%);
    border: 1px solid #c7d2fe;
    border-radius: 24px;
    position: relative;
    overflow: hidden;
}
.hs-hero::before {
    content: '';
    position: absolute; inset: 0;
    background-image: radial-gradient(rgba(79,70,229,0.07) 1px, transparent 1px);
    background-size: 24px 24px;
    pointer-events: none;
}

/* Quick action buttons */
.hs-quick-btn {
    display: flex; flex-direction: column; align-items: center; justify-content: center;
    gap: 8px; padding: 16px 12px; border-radius: 18px;
    transition: all 0.22s ease; cursor: pointer; text-decoration: none;
    border: 1px solid #e5e7eb;
    background: #ffffff;
}
.hs-quick-btn:hover {
    transform: translateY(-4px);
    border-color: #c7d2fe;
    background: #eef2ff;
    box-shadow: 0 12px 30px rgba(79,70,229,0.12);
}

/* XP progress bar */
.xp-bar-track {
    height: 8px; border-radius: 99px; overflow: hidden;
    background: #e0e7ff;
}
.xp-bar-fill {
    height: 100%; border-radius: 99px;
    background: linear-gradient(90deg, #7c3aed, #ec4899, #f59e0b);
    background-size: 200% 100%;
    animation: hs-bar-fill 1.6s cubic-bezier(.4,0,.2,1) both, xp-shimmer 3s ease-in-out 2s infinite;
    position: relative;
}
@keyframes xp-shimmer {
    0%, 100% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
}

/* Achievement card */
.ach-card {
    background: #f9fafb;
    border: 1px solid #e5e7eb;
    border-radius: 16px; padding: 16px;
    transition: all 0.2s ease;
}
.ach-card:hover { border-color: #c7d2fe; background: #eef2ff; }

/* Payment timeline */
.timeline-item { position: relative; padding-left: 24px; }
.timeline-item::before {
    content: '';
    position: absolute; left: 6px; top: 0; bottom: -20px;
    width: 1px; background: #e5e7eb;
}
.timeline-item:last-child::before { display: none; }
.timeline-dot {
    position: absolute; left: 0; top: 6px;
    width: 13px; height: 13px; border-radius: 50%;
    background: linear-gradient(135deg, #7c3aed, #ec4899);
    box-shadow: 0 0 10px rgba(124,58,237,0.5);
}
</style>
@endpush

@section('content')
@php
    $user = auth()->user();
    $p = min(100, max(0, $progress ?? 0));
    $isJHS = str_contains(strtolower($user->level_group ?? ''), 'junior');
    $isSHS = str_contains(strtolower($user->level_group ?? ''), 'senior');
    $levelLabel = $isJHS ? 'Junior High' : ($isSHS ? 'Senior High' : 'High School');
    $accentColor = $isJHS ? '#06b6d4' : '#ec4899';
    $xpLevel = $p >= 100 ? 'S' : ($p >= 75 ? 'A' : ($p >= 50 ? 'B' : ($p >= 25 ? 'C' : 'D')));
    $gradeLabel = ['S' => 'Honor Roll!', 'A' => 'Excellent', 'B' => 'Great Job', 'C' => 'Keep Going', 'D' => 'Just Started'][$xpLevel];
    $ringColor = $p >= 100 ? '#34d399' : ($p >= 75 ? '#a78bfa' : ($p >= 50 ? '#ec4899' : '#f59e0b'));
    $r = 46; $circ = 2 * M_PI * $r;
    $offset = $circ * (1 - $p / 100);
@endphp

{{-- ── Hero ── --}}
<div class="hs-hero hs-fade">
    {{-- Decorative elements --}}
    <div class="absolute -top-24 -right-24 w-72 h-72 rounded-full pointer-events-none"
         style="background: radial-gradient(circle, rgba(79,70,229,0.12) 0%, transparent 65%); filter: blur(20px);"></div>
    <div class="absolute -bottom-16 -left-16 w-56 h-56 rounded-full pointer-events-none"
         style="background: radial-gradient(circle, rgba({{ $isJHS ? '6,182,212' : '236,72,153' }},0.15) 0%, transparent 65%); filter: blur(20px);"></div>

    <div class="relative px-6 py-8 sm:px-10 sm:py-10">

        {{-- Top badges row --}}
        <div class="flex items-center justify-between mb-6 flex-wrap gap-3">
            <div class="flex items-center gap-2">
                <span class="hs-badge {{ $isJHS ? 'hs-badge-cyan' : 'hs-badge-violet' }}">
                    <span class="w-1.5 h-1.5 rounded-full hs-pulse" style="background: {{ $accentColor }};"></span>
                    {{ $levelLabel }} School
                </span>
                <span class="hs-badge hs-badge-amber">
                    {{ $user->year_level ?? 'N/A' }}
                </span>
                @if($isSHS && ($user->strand ?? false))
                <span class="hs-badge hs-badge-violet">{{ $user->strand }}</span>
                @endif
            </div>
            <div class="flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-bold"
                 style="background: #fff; color: #6b7280; border: 1px solid #e0e7ff;">
                📅 {{ now()->format('l, M j, Y') }}
            </div>
        </div>

        {{-- Main hero row --}}
        <div class="flex items-center justify-between gap-6">
            <div class="flex-1 min-w-0">

                {{-- Greeting --}}
                <h1 style="font-family: 'Plus Jakarta Sans', sans-serif; font-weight: 800; font-size: clamp(1.6rem, 4vw, 2.6rem); color: #1f2937; line-height: 1.15; margin: 0 0 6px;">
                    Hey, <span class="grad-text">{{ explode(' ', $user->name ?? 'Student')[0] }}!</span> 👋
                </h1>
                <p style="color: #6b7280; font-size: 14px; font-weight: 500; margin: 0 0 18px;">
                    {{ $user->program ?? ($user->strand ? $user->strand . ' Strand' : $levelLabel) }}
                    @if($isSHS && ($semester ?? false))
                        · {{ $semester == '1' ? '1st' : '2nd' }} Semester
                    @endif
                </p>

                {{-- XP progress --}}
                <div style="max-width: 360px;">
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center gap-2">
                            <span class="font-display text-sm" style="color: {{ $ringColor }};">
                                LVL {{ $xpLevel }}
                            </span>
                            <span class="text-xs font-semibold text-gray-400">{{ $gradeLabel }}</span>
                        </div>
                        <span class="font-mono-num text-xs font-bold text-gray-400">{{ $p }}% paid</span>
                    </div>
                    <div class="xp-bar-track">
                        <div class="xp-bar-fill" style="width: {{ $p }}%"></div>
                    </div>
                    <div class="flex justify-between mt-1.5">
                        @foreach(['0','25','50','75','100'] as $m)
                        <span class="text-[9px] font-bold" style="color: {{ $p >= intval($m) ? $ringColor : '#d1d5db' }}">{{ $m }}%</span>
                        @endforeach
                    </div>
                </div>

                {{-- Balance status --}}
                <div class="mt-4">
                    @if(($balance ?? 0) <= 0)
                    <div class="inline-flex items-center gap-2.5 px-4 py-2.5 rounded-2xl"
                         style="background: #ecfdf5; border: 1px solid #a7f3d0;">
                        <span class="text-lg">🎉</span>
                        <div>
                            <p class="text-sm font-bold text-emerald-700">All paid up!</p>
                            <p class="text-xs text-emerald-600">Zero balance — great work!</p>
                        </div>
                    </div>
                    @else
                    <div class="inline-flex items-center gap-2.5 px-4 py-2.5 rounded-2xl"
                         style="background: #fffbeb; border: 1px solid #fde68a;">
                        <span class="text-lg">⚡</span>
                        <div>
                            <p class="text-sm font-bold text-amber-700">Balance Due</p>
                            <p class="font-mono-num text-xs font-bold text-amber-600">₱{{ number_format($balance ?? 0, 2) }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Progress Ring --}}
            <div class="flex-shrink-0 hidden sm:flex flex-col items-center gap-2" style="width: 120px;">
                <div style="position: relative; width: 110px; height: 110px;">
                    {{-- Outer glow --}}
                    <div style="position: absolute; inset: 5px; border-radius: 50%; background: radial-gradient(circle, {{ $ringColor }}22, transparent); filter: blur(10px);"></div>
                    {{-- Spinning decoration ring --}}
                    <div class="xp-ring-spin" style="position: absolute; inset: -4px; border-radius: 50%; border: 2px dashed rgba(79,70,229,0.15);"></div>
                    <svg width="110" height="110" viewBox="0 0 110 110"
                         style="transform: rotate(-90deg); display: block; position: relative; z-index: 1;">
                        <circle cx="55" cy="55" r="{{ $r }}" fill="none"
                                stroke="#e0e7ff" stroke-width="8"/>
                        <circle cx="55" cy="55" r="{{ $r }}" fill="none"
                                stroke="{{ $ringColor }}"
                                stroke-width="8" stroke-linecap="round"
                                stroke-dasharray="{{ round($circ, 2) }}"
                                stroke-dashoffset="{{ round($offset, 2) }}"
                                style="transition: stroke-dashoffset 1.4s cubic-bezier(.4,0,.2,1); filter: drop-shadow(0 0 8px {{ $ringColor }}90);"/>
                    </svg>
                    <div style="position: absolute; inset: 0; display: flex; flex-direction: column; align-items: center; justify-content: center; z-index: 2;">
                        <span class="font-display" style="color: #1f2937; font-size: 28px; line-height: 1;">{{ $xpLevel }}</span>
                        <span style="font-size: 9px; font-weight: 700; color: #9ca3af; text-transform: uppercase; letter-spacing: .1em;">Level</span>
                    </div>
                </div>
                <p class="text-xs font-bold text-center text-gray-400">Payment Status</p>
            </div>
        </div>
    </div>
</div>

{{-- ── Stats Row ── --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 hs-fade hs-d1">
    @php
        $stats = [
            ['label' => 'Total Fees', 'value' => '₱' . number_format($total ?? 0, 2), 'icon' => '💰', 'color' => '#7c3aed', 'bg' => 'rgba(124,58,237,0.12)'],
            ['label' => 'Amount Paid', 'value' => '₱' . number_format($paid ?? 0, 2), 'icon' => '✅', 'color' => '#10b981', 'bg' => 'rgba(16,185,129,0.10)'],
            ['label' => 'Balance', 'value' => '₱' . number_format($balance ?? 0, 2), 'icon' => '⚠️', 'color' => $balance > 0 ? '#f59e0b' : '#10b981', 'bg' => $balance > 0 ? 'rgba(245,158,11,0.10)' : 'rgba(16,185,129,0.10)'],
            ['label' => $isJHS ? 'School Year' : 'Semester', 'value' => $isJHS ? (date('Y') . '-' . (date('Y')+1)) : (($semester ?? '1') == '1' ? '1st Sem' : '2nd Sem'), 'icon' => '📚', 'color' => $accentColor, 'bg' => 'rgba(' . ($isJHS ? '6,182,212' : '236,72,153') . ',0.10)'],
        ];
    @endphp
    @foreach($stats as $stat)
    <div class="hs-card-lift rounded-2xl p-5 stat-counter"
         style="background: #ffffff; border: 1px solid #e5e7eb; animation-delay: {{ $loop->index * 0.07 }}s;">
        <div class="text-2xl mb-2">{{ $stat['icon'] }}</div>
        <p class="text-xs font-bold uppercase tracking-wider mb-1" style="color: {{ $stat['color'] }}; opacity: 0.8;">{{ $stat['label'] }}</p>
        <p class="font-mono-num font-bold text-lg text-gray-800 leading-tight">{{ $stat['value'] }}</p>
    </div>
    @endforeach
</div>

{{-- ── Main Grid ── --}}
<div class="grid grid-cols-1 lg:grid-cols-5 gap-6 hs-fade hs-d2">

    {{-- Left: Progress + Quick Actions (3 cols) --}}
    <div class="lg:col-span-3 space-y-5">

        {{-- Detailed payment progress --}}
        <div class="rounded-2xl p-6 hs-card-lift"
             style="background: #ffffff; border: 1px solid #e5e7eb;">
            <div class="flex items-center justify-between mb-5">
                <div>
                    <h3 class="font-bold text-gray-800 text-base">Payment Progress</h3>
                    <p class="text-xs mt-0.5 text-gray-400">
                        {{ $isJHS ? 'Annual' : (($semester ?? '1') == '1' ? '1st Semester' : '2nd Semester') }} · S.Y. {{ date('Y') . '–' . (date('Y')+1) }}
                    </p>
                </div>
                <div class="text-right">
                    <p class="font-mono-num font-bold text-2xl" style="color: {{ $ringColor }};">{{ $p }}%</p>
                    <p class="text-xs text-gray-400">complete</p>
                </div>
            </div>

            {{-- Bar --}}
            <div class="xp-bar-track mb-3">
                <div class="xp-bar-fill" style="width: {{ $p }}%"></div>
            </div>

            {{-- Milestones --}}
            <div class="grid grid-cols-4 gap-2">
                @foreach([[25,'🌱','Seedling'],[50,'🌿','Growing'],[75,'🌟','Almost'],[100,'🏆','Complete']] as [$milestone, $emoji, $mlabel])
                <div class="text-center p-2.5 rounded-xl {{ $p >= $milestone ? '' : 'opacity-30' }}"
                     style="{{ $p >= $milestone ? 'background: #ede9fe; border: 1px solid #ddd6fe;' : 'background: #f9fafb; border: 1px solid #e5e7eb;' }}">
                    <p class="text-lg">{{ $emoji }}</p>
                    <p class="text-[9px] font-bold mt-1 {{ $p >= $milestone ? 'text-violet-700' : 'text-gray-300' }}">{{ $mlabel }}</p>
                    <p class="text-[9px] font-mono-num mt-0.5 {{ $p >= $milestone ? 'text-violet-500' : 'text-gray-300' }}">{{ $milestone }}%</p>
                </div>
                @endforeach
            </div>

            {{-- Totals --}}
            <div class="grid grid-cols-2 gap-3 mt-4">
                <div class="p-3.5 rounded-xl" style="background: #ecfdf5; border: 1px solid #a7f3d0;">
                    <p class="text-[10px] font-bold uppercase tracking-wide text-emerald-600">Paid</p>
                    <p class="font-mono-num font-bold text-emerald-700 text-lg mt-1">₱{{ number_format($paid ?? 0, 2) }}</p>
                </div>
                <div class="p-3.5 rounded-xl" style="background: #fffbeb; border: 1px solid #fde68a;">
                    <p class="text-[10px] font-bold uppercase tracking-wide text-amber-600">Remaining</p>
                    <p class="font-mono-num font-bold text-amber-700 text-lg mt-1">₱{{ number_format($balance ?? 0, 2) }}</p>
                </div>
            </div>
        </div>

        {{-- Due date reminder / all clear --}}
        @if(($balance ?? 0) > 0)
        <div class="rounded-2xl p-5 flex items-center gap-4"
             style="background: linear-gradient(135deg, #fffbeb, #fef3c7); border: 1px solid #fde68a;">
            <div class="w-12 h-12 rounded-2xl flex items-center justify-center flex-shrink-0 text-xl"
                 style="background: #fde68a;">⚡</div>
            <div class="flex-1">
                <p class="font-bold text-amber-900 text-sm">Payment Due Soon</p>
                <p class="text-xs mt-0.5 text-amber-700">
                    Balance of ₱{{ number_format($balance, 2) }}
                    @if($nextDueDate ?? false) · Due {{ $nextDueDate }} @endif
                </p>
            </div>
            <a href="{{ route('hs.billing') }}"
               class="flex-shrink-0 px-4 py-2 rounded-xl text-xs font-bold text-white transition-all"
               style="background: #f59e0b; color: white; border: none;">
                Pay Now →
            </a>
        </div>
        @else
        <div class="rounded-2xl p-5 flex items-center gap-4"
             style="background: linear-gradient(135deg, #ecfdf5, #d1fae5); border: 1px solid #a7f3d0;">
            <div class="w-12 h-12 rounded-2xl flex items-center justify-center flex-shrink-0 text-2xl"
                 style="background: #a7f3d0;">🏆</div>
            <div>
                <p class="font-bold text-emerald-900 text-sm">You're fully paid! Amazing!</p>
                <p class="text-xs mt-0.5 text-emerald-700">No outstanding balance — keep up the great work!</p>
            </div>
        </div>
        @endif
    </div>

    {{-- Right: Recent Payments (2 cols) --}}
    <div class="lg:col-span-2 space-y-4">

        {{-- Next due card (JHS shows annual, SHS shows semester) --}}
        <div class="rounded-2xl p-5 hs-shimmer"
             style="background: linear-gradient(135deg, #eef2ff, #e0e7ff); border: 1px solid #c7d2fe;">
            <div class="flex items-center gap-2 mb-3">
                <span class="text-lg">{{ $isJHS ? '📚' : '🎓' }}</span>
                <p class="text-xs font-bold uppercase tracking-wider text-indigo-600">
                    {{ $isJHS ? 'Annual Fees' : 'Semester Fees' }}
                </p>
            </div>
            <p class="font-mono-num font-bold text-2xl text-gray-800">₱{{ number_format($total ?? 0, 2) }}</p>
            <p class="text-xs mt-1 text-gray-400">
                S.Y. {{ date('Y') . '–' . (date('Y')+1) }}
                {{ $isSHS ? ' · ' . (($semester ?? '1') == '1' ? '1st' : '2nd') . ' Sem' : '' }}
            </p>
            @if($nextDueDate ?? false)
            <div class="flex items-center gap-2 mt-3 pt-3" style="border-top: 1px solid #e0e7ff;">
                <span class="w-1.5 h-1.5 rounded-full hs-pulse bg-amber-400"></span>
                <p class="text-xs font-semibold text-amber-600">Due: {{ $nextDueDate }}</p>
            </div>
            @endif
        </div>

        {{-- Recent payments timeline --}}
        <div class="rounded-2xl p-5 flex-1"
             style="background: #ffffff; border: 1px solid #e5e7eb;">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-bold text-gray-800 text-sm">Recent Payments</h3>
                <a href="{{ route('hs.statements') }}"
                   class="text-xs font-bold transition-colors"
                   class="text-indigo-600 hover:text-indigo-800 font-bold">
                    View all →
                </a>
            </div>

            <div class="space-y-4">
                @forelse($recentPayments ?? [] as $payment)
                <div class="timeline-item">
                    <div class="timeline-dot"></div>
                    <div class="flex items-center justify-between gap-2">
                        <div>
                            <p class="text-sm font-semibold text-gray-800">
                                {{ $payment['date'] instanceof \Carbon\Carbon ? $payment['date']->format('M d, Y') : $payment['date'] }}
                            </p>
                            <p class="text-xs mt-0.5 text-gray-400">{{ $payment['method'] ?? 'Payment' }}</p>
                        </div>
                        <div class="text-right">
                            <p class="font-mono-num font-bold text-sm text-emerald-600">+₱{{ number_format($payment['amount'] ?? 0, 2) }}</p>
                            <p class="hs-badge hs-badge-green text-[9px] mt-1">Paid</p>
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-center py-8">
                    <div class="text-4xl mb-3">📭</div>
                    <p class="text-sm font-semibold text-gray-400">No payments yet</p>
                    <p class="text-xs mt-1 text-gray-300">Your transactions will appear here</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

{{-- ── Achievements Row ── --}}
<div class="hs-fade hs-d3">
    <div class="flex items-center justify-between mb-4">
        <h3 class="font-bold text-gray-800 text-base">🏅 Payment Achievements</h3>
        <span class="text-xs font-semibold text-gray-400">Unlock milestones by paying on time</span>
    </div>
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        @php
            $achievements = [
                ['icon' => '🌟', 'title' => 'First Payment', 'desc' => 'Made your first payment', 'unlocked' => ($paid ?? 0) > 0],
                ['icon' => '🔥', 'title' => 'Half Way!', 'desc' => '50% of fees paid', 'unlocked' => $p >= 50],
                ['icon' => '💎', 'title' => 'Almost There', 'desc' => '75% of fees paid', 'unlocked' => $p >= 75],
                ['icon' => '🏆', 'title' => 'Fully Paid!', 'desc' => 'All fees settled', 'unlocked' => $p >= 100],
            ];
        @endphp
        @foreach($achievements as $ach)
        <div class="ach-card {{ $ach['unlocked'] ? 'streak-badge' : 'opacity-40' }}"
             style="{{ $ach['unlocked'] ? 'border-color: #c4b5fd; background: #ede9fe;' : '' }}">
            <div class="text-2xl mb-2">{{ $ach['icon'] }}</div>
            <p class="text-sm font-bold {{ $ach['unlocked'] ? 'text-violet-800' : 'text-gray-400' }}">{{ $ach['title'] }}</p>
            <p class="text-xs mt-1 {{ $ach['unlocked'] ? 'text-violet-600' : 'text-gray-300' }}">{{ $ach['desc'] }}</p>
            @if($ach['unlocked'])
            <div class="flex items-center gap-1.5 mt-2">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 hs-pulse"></span>
                <span class="text-[10px] font-bold text-emerald-600">Unlocked!</span>
            </div>
            @else
            <div class="mt-2">
                <span class="text-[10px] font-bold text-gray-300">🔒 Locked</span>
            </div>
            @endif
        </div>
        @endforeach
    </div>
</div>

@endsection