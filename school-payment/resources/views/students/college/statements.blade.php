@extends('students.college.layouts.student-app')
@section('title', 'Statements & History')

@push('styles')
<style>
/* ── Statements Page ── */
.st-root { font-family: 'Sora', sans-serif; }

/* KPI card number entrance */
@keyframes st-count-up {
    from { opacity: 0; transform: translateY(10px); }
    to   { opacity: 1; transform: translateY(0); }
}
.st-kpi-num { animation: st-count-up .5s ease both; }
.st-d1 .st-kpi-num { animation-delay: .1s; }
.st-d2 .st-kpi-num { animation-delay: .18s; }
.st-d3 .st-kpi-num { animation-delay: .26s; }

/* Transaction row */
.st-row {
    transition: transform .2s cubic-bezier(.34,1.2,.64,1), box-shadow .2s ease, border-color .18s ease;
    cursor: default;
}
.st-row:hover {
    transform: translateX(5px) translateY(-1px);
    box-shadow: 0 6px 24px rgba(37,99,235,.1);
    border-color: #c7d2fe !important;
}
.st-row:hover .st-row-icon { transform: scale(1.12) rotate(-5deg); }
.st-row-icon { transition: transform .25s cubic-bezier(.34,1.56,.64,1); }

/* Amount flip-in */
@keyframes st-amt-in {
    from { opacity: 0; transform: scale(.85); }
    to   { opacity: 1; transform: scale(1); }
}
.st-amt { animation: st-amt-in .35s cubic-bezier(.34,1.3,.64,1) both; }

/* Filter tabs */
.st-tab {
    padding: 8px 16px; border-radius: 10px; font-size: 13px; font-weight: 700;
    cursor: pointer; border: 1.5px solid transparent; transition: all .18s ease;
    white-space: nowrap;
}
.st-tab.active {
    background: linear-gradient(135deg, #4f46e5, #7c3aed);
    color: #fff;
    box-shadow: 0 4px 14px rgba(99,102,241,.3);
}
.st-tab:not(.active) {
    background: #fff; color: #64748b; border-color: #e2e8f0;
}
.st-tab:not(.active):hover { border-color: #c7d2fe; color: #4f46e5; background: #eef2ff; }

/* Totals bar */
.st-totals-row { transition: background .15s; }
.st-totals-row:hover { background: #f8faff; }

/* Empty state */
.st-empty-icon {
    animation: st-float 3s ease-in-out infinite;
}
@keyframes st-float {
    0%,100% { transform: translateY(0); }
    50% { transform: translateY(-8px); }
}

/* Fade-up */
@keyframes st-fadein {
    from { opacity: 0; transform: translateY(14px); }
    to   { opacity: 1; transform: translateY(0); }
}
.st-fade { animation: st-fadein .45s ease both; }
.st-fd1 { animation-delay: .06s; }
.st-fd2 { animation-delay: .12s; }
.st-fd3 { animation-delay: .18s; }

/* Badge pill */
.st-badge { display: inline-flex; align-items: center; gap: 5px; padding: 3px 10px; border-radius: 999px; font-size: 11px; font-weight: 800; }
.st-badge-paid    { background: #ecfdf5; color: #065f46; }
.st-badge-pending { background: #fffbeb; color: #92400e; }

/* Print */
@media print {
    header, aside, nav, button, a[href], select, .st-no-print { display: none !important; }
    .shadow-sm, .shadow-md { box-shadow: none !important; }
    body { background: white; }
}
</style>
@endpush

@section('content')
@php
    $totalPaid    = collect($statements ?? [])->where('paid', true)->sum('amount');
    $totalPending = collect($statements ?? [])->where('paid', false)->sum('amount');
    $paidCount    = collect($statements ?? [])->where('paid', true)->count();
    $pendingCount = collect($statements ?? [])->where('paid', false)->count();
    $totalCount   = count($statements ?? []);
@endphp

<div class="st-root space-y-6" x-data="{ filter: 'all' }">

    {{-- ── Header ── --}}
    <div class="st-fade flex flex-col sm:flex-row sm:items-end justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider mb-3"
                 style="background: rgba(99,102,241,.1); color: #4f46e5;">
                <span class="w-1.5 h-1.5 rounded-full bg-indigo-500"></span>
                Account Records
            </div>
            <h1 class="font-display text-4xl sm:text-5xl leading-tight" class="font-display text-4xl sm:text-5xl leading-tight text-gray-800">Payment History</h1>
            <p class="text-slate-500 text-sm mt-1.5">A complete record of your charges and payments.</p>
        </div>
        <div class="flex items-center gap-2 st-no-print">
            {{-- Filter tabs --}}
            <div class="flex items-center gap-1.5 bg-slate-100 rounded-xl p-1">
                <button class="st-tab" :class="filter==='all' ? 'active' : ''" @click="filter='all'">All ({{ $totalCount }})</button>
                <button class="st-tab" :class="filter==='paid' ? 'active' : ''" @click="filter='paid'">Paid ({{ $paidCount }})</button>
                <button class="st-tab" :class="filter==='pending' ? 'active' : ''" @click="filter='pending'">Pending ({{ $pendingCount }})</button>
            </div>
            <button onclick="window.print()"
                    class="hidden sm:flex items-center gap-1.5 px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-sm font-semibold text-slate-600 hover:bg-slate-50 shadow-sm transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                Print
            </button>
        </div>
    </div>

    {{-- ── Summary KPI Cards ── --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 st-fade st-fd1">

        <div class="st-d1 card-lift bg-white rounded-2xl border border-slate-200 p-5 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl flex items-center justify-center flex-shrink-0 shadow-md"
                 style="background: linear-gradient(135deg, #4f46e5, #7c3aed);">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Entries</p>
                <p class="st-kpi-num font-mono-num font-extrabold text-3xl text-slate-800 mt-0.5">{{ $totalCount }}</p>
            </div>
        </div>

        <div class="st-d2 card-lift bg-white rounded-2xl border border-slate-200 p-5 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-emerald-500 flex items-center justify-center flex-shrink-0 shadow-md">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Paid</p>
                <p class="st-kpi-num font-mono-num font-extrabold text-3xl text-emerald-600 mt-0.5">{{ $paidCount }}</p>
                <p class="text-xs text-slate-400 mt-0.5 font-mono-num">₱{{ number_format($totalPaid, 2) }}</p>
            </div>
        </div>

        <div class="st-d3 card-lift bg-white rounded-2xl border border-slate-200 p-5 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-amber-500 flex items-center justify-center flex-shrink-0 shadow-md">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Pending</p>
                <p class="st-kpi-num font-mono-num font-extrabold text-3xl text-amber-600 mt-0.5">{{ $pendingCount }}</p>
                <p class="text-xs text-slate-400 mt-0.5 font-mono-num">₱{{ number_format($totalPending, 2) }}</p>
            </div>
        </div>
    </div>

    {{-- ── Totals Bar ── --}}
    @if($totalCount > 0)
    <div class="st-fade st-fd2 bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
        <div class="px-5 py-3 border-b border-slate-100 flex items-center gap-2"
             style="background: #f8faff; border-bottom: 1px solid #e0e7ff;">
            <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
            </svg>
            <span class="text-indigo-700 text-xs font-bold uppercase tracking-wider">Financial Summary</span>
        </div>
        <div class="flex flex-col sm:flex-row sm:divide-x sm:divide-slate-100">
            <div class="st-totals-row flex-1 flex items-center gap-3 px-6 py-4">
                <div class="w-3 h-3 rounded-full bg-emerald-500 flex-shrink-0"></div>
                <span class="text-sm text-slate-500 font-medium">Total Paid</span>
                <span class="font-mono-num font-bold text-emerald-700 ml-auto tabular-nums">₱{{ number_format($totalPaid, 2) }}</span>
            </div>
            <div class="st-totals-row flex-1 flex items-center gap-3 px-6 py-4 border-t sm:border-t-0 border-slate-100">
                <div class="w-3 h-3 rounded-full bg-amber-500 flex-shrink-0"></div>
                <span class="text-sm text-slate-500 font-medium">Pending</span>
                <span class="font-mono-num font-bold text-amber-700 ml-auto tabular-nums">₱{{ number_format($totalPending, 2) }}</span>
            </div>
            <div class="st-totals-row flex-1 flex items-center gap-3 px-6 py-4 border-t sm:border-t-0 border-slate-100" style="background: #fafbff;">
                <div class="w-3 h-3 rounded-full bg-indigo-500 flex-shrink-0"></div>
                <span class="text-sm font-bold text-slate-700">Grand Total</span>
                <span class="font-mono-num font-extrabold text-slate-900 ml-auto tabular-nums">₱{{ number_format($totalPaid + $totalPending, 2) }}</span>
            </div>
        </div>
    </div>
    @endif

    {{-- ── Transactions List ── --}}
    <div class="space-y-2.5 st-fade st-fd3">
        @forelse($statements ?? [] as $item)
        <div class="st-row bg-white rounded-2xl border border-slate-200 px-5 py-4 group"
             x-show="filter === 'all'
                     || (filter === 'paid' && {{ ($item['paid'] ?? false) ? 'true' : 'false' }})
                     || (filter === 'pending' && {{ ($item['paid'] ?? false) ? 'false' : 'true' }})">
            <div class="flex items-center gap-4">
                {{-- Status icon --}}
                <div class="st-row-icon w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0
                            {{ ($item['paid'] ?? false) ? 'bg-emerald-100' : 'bg-amber-100' }}">
                    @if($item['paid'] ?? false)
                        <svg class="w-5 h-5 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                    @else
                        <svg class="w-5 h-5 text-amber-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                        </svg>
                    @endif
                </div>

                {{-- Info --}}
                <div class="flex-1 min-w-0">
                    <p class="font-bold text-slate-800 text-sm truncate">{{ $item['description'] ?? 'Payment' }}</p>
                    <div class="flex items-center gap-2 mt-1 flex-wrap">
                        <span class="text-xs text-slate-400">{{ $item['date'] ?? '—' }}</span>
                        @if(isset($item['reference']))
                        <span class="text-xs font-mono text-slate-300 bg-slate-50 px-1.5 py-0.5 rounded">Ref: {{ $item['reference'] }}</span>
                        @endif
                    </div>
                </div>

                {{-- Amount + Badge --}}
                <div class="flex items-center gap-3 flex-shrink-0">
                    <p class="st-amt font-mono-num font-extrabold text-lg tabular-nums {{ ($item['paid'] ?? false) ? 'text-emerald-600' : 'text-rose-500' }}">
                        {{ ($item['paid'] ?? false) ? '+' : '-' }}₱{{ number_format($item['amount'] ?? 0, 2) }}
                    </p>
                    <span class="hidden sm:inline-flex st-badge {{ ($item['paid'] ?? false) ? 'st-badge-paid' : 'st-badge-pending' }}">
                        @if($item['paid'] ?? false)
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            Paid
                        @else
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/></svg>
                            Pending
                        @endif
                    </span>
                </div>
            </div>
        </div>
        @empty
        <div class="bg-white rounded-2xl border border-slate-200 py-20 text-center">
            <div class="st-empty-icon inline-flex w-20 h-20 rounded-3xl bg-slate-100 items-center justify-center mb-5">
                <svg class="w-10 h-10 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <p class="font-bold text-slate-600 text-xl">No transactions yet</p>
            <p class="text-slate-400 text-sm mt-2 max-w-xs mx-auto">Your payment history will appear here once payments are recorded.</p>
        </div>
        @endforelse

        @if($totalCount > 0)
        <div x-show="filter !== 'all' &&
                     ((filter === 'paid' && {{ $paidCount }} === 0) ||
                      (filter === 'pending' && {{ $pendingCount }} === 0))"
             class="text-center py-10 text-slate-400 text-sm font-semibold" style="display:none">
            <div class="inline-flex flex-col items-center gap-2">
                <svg class="w-8 h-8 text-slate-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                No <span x-text="filter"></span> transactions found.
            </div>
        </div>
        @endif
    </div>

</div>
@endsection