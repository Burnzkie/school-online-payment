{{-- resources/views/treasurer/dashboard.blade.php --}}
@extends('treasurer.layouts.treasurer-app')
@section('title', 'Dashboard')

@section('content')
<div class="space-y-6">

    {{-- Page header --}}
    <div class="fade-up">
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-800">Finance Dashboard</h1>
        <p class="text-sm mt-1 text-gray-400">
            School Year {{ $currentYear }} &nbsp;·&nbsp; {{ now()->format('l, F j, Y') }}
        </p>
    </div>

    {{-- KPI Row --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 fade-up fade-up-d1">
        <div class="stat-card">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold uppercase tracking-wider text-gray-400">Total Revenue</span>
                <span class="text-xl">💰</span>
            </div>
            <p class="text-2xl font-bold text-gray-800 font-mono-num">₱{{ number_format($totalRevenue, 2) }}</p>
            <p class="text-xs mt-1 text-indigo-500">S.Y. {{ $currentYear }}</p>
        </div>

        <div class="stat-card">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold uppercase tracking-wider text-gray-400">This Month</span>
                <span class="text-xl">📆</span>
            </div>
            <p class="text-2xl font-bold text-gray-800 font-mono-num">₱{{ number_format($monthlyRevenue, 2) }}</p>
            <p class="text-xs mt-1 text-gray-400">{{ now()->format('F Y') }}</p>
        </div>

        <div class="stat-card">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold uppercase tracking-wider text-gray-400">Collection Rate</span>
                <span class="text-xl">📊</span>
            </div>
            <p class="text-2xl font-bold font-mono-num" style="color: {{ $collectionRate >= 80 ? '#16a34a' : ($collectionRate >= 50 ? '#d97706' : '#e11d48') }}">
                {{ $collectionRate }}%
            </p>
            <div class="progress-bar-track mt-2">
                <div class="progress-bar-fill" style="width: {{ $collectionRate }}%;
                     background: {{ $collectionRate >= 80 ? 'linear-gradient(90deg,#16a34a,#22c55e)' : ($collectionRate >= 50 ? 'linear-gradient(90deg,#d97706,#f59e0b)' : 'linear-gradient(90deg,#e11d48,#f43f5e)') }};"></div>
            </div>
        </div>

        <div class="stat-card">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold uppercase tracking-wider text-gray-400">Outstanding</span>
                <span class="text-xl">⚠️</span>
            </div>
            <p class="text-2xl font-bold font-mono-num text-amber-600">₱{{ number_format($outstanding, 2) }}</p>
            <p class="text-xs mt-1 text-gray-400">Uncollected fees</p>
        </div>
    </div>

    {{-- Status row --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 fade-up fade-up-d1">
        <a href="{{ route('treasurer.clearances') }}" class="stat-card flex flex-col gap-1 hover:border-red-200 transition">
            <span class="text-xs font-bold uppercase tracking-wider text-gray-400">On Hold</span>
            <span class="text-3xl font-bold font-mono-num text-red-500">{{ $onHoldCount }}</span>
            <span class="text-xs text-gray-400">Students blocked</span>
        </a>
        <a href="{{ route('treasurer.scholarships') }}" class="stat-card flex flex-col gap-1 hover:border-indigo-200 transition">
            <span class="text-xs font-bold uppercase tracking-wider text-gray-400">Scholarships</span>
            <span class="text-3xl font-bold font-mono-num text-indigo-500">{{ $activeScholarships }}</span>
            <span class="text-xs text-gray-400">Active discounts</span>
        </a>
        <div class="stat-card flex flex-col gap-1">
            <span class="text-xs font-bold uppercase tracking-wider text-gray-400">Pending Payments</span>
            <span class="text-3xl font-bold font-mono-num text-amber-600">{{ $pendingPayments }}</span>
            <span class="text-xs text-gray-400">Awaiting confirmation</span>
        </div>
        <div class="stat-card flex flex-col gap-1">
            <span class="text-xs font-bold uppercase tracking-wider text-gray-400">Overdue Plans</span>
            <span class="text-3xl font-bold font-mono-num" style="color: {{ $overdueCount > 0 ? '#e11d48' : '#16a34a' }}">{{ $overdueCount }}</span>
            <span class="text-xs text-gray-400">Installment schedules</span>
        </div>
    </div>

    {{-- Aging + Student Status --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 fade-up fade-up-d2">

        {{-- Aging Buckets --}}
        <div class="section-card p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-bold text-gray-800">Receivables Aging</h3>
                <a href="{{ route('treasurer.aging') }}" class="text-xs font-semibold text-indigo-500 hover:text-indigo-700">Full Report →</a>
            </div>
            @php
                $agingColors = ['1-30'=>'#d97706','31-60'=>'#ea580c','61-90'=>'#dc2626','90+'=>'#e11d48'];
                $agingLabels = ['1-30'=>'1–30 days','31-60'=>'31–60 days','61-90'=>'61–90 days','90+'=>'Over 90 days'];
                $agingMax = max(max(array_values($aging)), 1);
            @endphp
            <div class="space-y-3">
                @foreach($aging as $bucket => $amount)
                <div>
                    <div class="flex justify-between text-xs mb-1">
                        <span class="text-gray-500">{{ $agingLabels[$bucket] }}</span>
                        <span class="font-bold font-mono-num" style="color: {{ $agingColors[$bucket] }}">
                            ₱{{ number_format($amount, 0) }}
                        </span>
                    </div>
                    <div class="progress-bar-track">
                        <div class="progress-bar-fill" style="width: {{ $agingMax > 0 ? round(($amount/$agingMax)*100) : 0 }}%;
                             background: {{ $agingColors[$bucket] }};"></div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Student Payment Status --}}
        <div class="section-card p-5">
            <h3 class="text-sm font-bold text-gray-800 mb-4">Student Payment Status</h3>
            <div class="space-y-3">
                <div>
                    <div class="flex justify-between text-xs mb-1">
                        <span class="text-gray-500">Has Payments</span>
                        <span class="font-bold text-indigo-500">{{ $paidStudents }}</span>
                    </div>
                    <div class="progress-bar-track">
                        <div class="progress-bar-fill" style="width: {{ $totalStudents > 0 ? round(($paidStudents/$totalStudents)*100) : 0 }}%;"></div>
                    </div>
                </div>
                <div>
                    <div class="flex justify-between text-xs mb-1">
                        <span class="text-gray-500">No Payments Yet</span>
                        <span class="font-bold text-red-500">{{ $unpaidStudents }}</span>
                    </div>
                    <div class="progress-bar-track">
                        <div class="progress-bar-fill" style="width: {{ $totalStudents > 0 ? round(($unpaidStudents/$totalStudents)*100) : 0 }}%; background: linear-gradient(90deg,#e11d48,#f43f5e);"></div>
                    </div>
                </div>
            </div>
            <div class="mt-4 pt-4 border-t border-gray-100">
                <p class="text-xs text-gray-400">Total enrolled</p>
                <p class="text-xl font-bold text-gray-800 font-mono-num">{{ $totalStudents }}</p>
            </div>
        </div>

        {{-- Revenue by Level --}}
        <div class="section-card p-5">
            <h3 class="text-sm font-bold text-gray-800 mb-4">Revenue by Level Group</h3>
            <div class="space-y-2">
                @forelse($revenueByLevel as $lg => $row)
                <div class="flex items-center justify-between py-1.5">
                    <span class="text-xs font-semibold text-gray-500 truncate">{{ $lg ?: 'Unassigned' }}</span>
                    <span class="text-xs font-bold font-mono-num text-gray-800 ml-2">₱{{ number_format($row->total, 0) }}</span>
                </div>
                @empty
                <p class="text-xs text-gray-400">No data yet.</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Top Delinquent + Recent Payments + Chart --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 fade-up fade-up-d3">

        {{-- Top Delinquent --}}
        <div class="section-card">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 bg-gray-50">
                <h3 class="text-sm font-bold text-gray-800">🚨 Top Delinquent Accounts</h3>
                <a href="{{ route('treasurer.aging') }}" class="text-xs font-semibold text-indigo-500 hover:text-indigo-700">Full Aging →</a>
            </div>
            <div class="divide-y divide-gray-50">
                @forelse($topDelinquent as $row)
                <div class="flex items-center justify-between px-5 py-3.5">
                    <div>
                        <p class="text-sm font-semibold text-gray-800">{{ $row['student']->name }}</p>
                        <p class="text-xs text-gray-400">
                            {{ $row['student']->student_id ?? 'No ID' }} · {{ $row['student']->level_group }}
                        </p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-bold font-mono-num text-red-500">₱{{ number_format($row['balance'], 2) }}</p>
                        <a href="{{ route('treasurer.student.detail', $row['student']) }}" class="text-xs font-semibold text-indigo-500 hover:text-indigo-700">View →</a>
                    </div>
                </div>
                @empty
                <div class="px-5 py-6 flex items-center gap-3">
                    <span class="text-2xl">✅</span>
                    <p class="text-sm font-semibold text-emerald-600">No outstanding balances!</p>
                </div>
                @endforelse
            </div>
        </div>

        {{-- Monthly Chart --}}
        <div class="section-card p-5">
            <h3 class="text-sm font-bold text-gray-800 mb-4">Monthly Revenue (Last 6 Months)</h3>
            @php $maxAmt = max(max(array_column($monthlyChart, 'amount')), 1); @endphp
            <div class="flex items-end gap-2 h-32">
                @foreach($monthlyChart as $bar)
                <div class="flex-1 flex flex-col items-center gap-1">
                    <span class="text-xs font-mono-num text-gray-400">
                        {{ $bar['amount'] > 0 ? '₱'.number_format($bar['amount']/1000, 0).'k' : '' }}
                    </span>
                    <div class="w-full rounded-t-lg transition-all"
                         style="height: {{ max(4, round(($bar['amount']/$maxAmt)*100)) }}px;
                                background: linear-gradient(180deg,#4f46e5,#6366f1);
                                opacity: {{ $bar === end($monthlyChart) ? '1' : '0.55' }};">
                    </div>
                    <span class="text-xs truncate w-full text-center text-gray-400">
                        {{ \Carbon\Carbon::parse($bar['month'])->format('M') }}
                    </span>
                </div>
                @endforeach
            </div>

            {{-- Recent Payments mini-list --}}
            <div class="mt-5 pt-4 border-t border-gray-100">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-xs font-bold text-gray-700">Recent Payments</p>
                    <a href="{{ route('treasurer.payments') }}" class="text-xs font-semibold text-indigo-500 hover:text-indigo-700">View all →</a>
                </div>
                <div class="space-y-2">
                    @foreach($recentPayments->take(4) as $p)
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-semibold text-gray-800 truncate max-w-[150px]">{{ $p->student->name ?? '—' }}</p>
                            <p class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($p->payment_date)->format('M j') }} · {{ $p->payment_method }}</p>
                        </div>
                        <span class="text-xs font-bold font-mono-num text-indigo-500">₱{{ number_format($p->amount, 0) }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- Alerts --}}
    @if($overdueCount > 0 || $pendingPayments > 0 || $onHoldCount > 0)
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 fade-up fade-up-d4">
        @if($overdueCount > 0)
        <a href="{{ route('treasurer.installments') }}"
           class="flex items-center gap-3 p-4 rounded-2xl transition hover:opacity-90 bg-red-50 border border-red-100">
            <span class="text-2xl">🚨</span>
            <div>
                <p class="text-sm font-bold text-red-600">{{ $overdueCount }} Overdue Installments</p>
                <p class="text-xs text-gray-400">Students past due date</p>
            </div>
        </a>
        @endif
        @if($pendingPayments > 0)
        <a href="{{ route('treasurer.payments') }}?status=pending"
           class="flex items-center gap-3 p-4 rounded-2xl transition hover:opacity-90 bg-amber-50 border border-amber-100">
            <span class="text-2xl">⏳</span>
            <div>
                <p class="text-sm font-bold text-amber-700">{{ $pendingPayments }} Pending Payments</p>
                <p class="text-xs text-gray-400">Awaiting cashier confirmation</p>
            </div>
        </a>
        @endif
        @if($onHoldCount > 0)
        <a href="{{ route('treasurer.clearances') }}"
           class="flex items-center gap-3 p-4 rounded-2xl transition hover:opacity-90 bg-red-50 border border-red-100">
            <span class="text-2xl">🚫</span>
            <div>
                <p class="text-sm font-bold text-red-600">{{ $onHoldCount }} Students on Hold</p>
                <p class="text-xs text-gray-400">Finance clearance blocked</p>
            </div>
        </a>
        @endif
    </div>
    @endif

    {{-- Quick links --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 fade-up fade-up-d4">
        <a href="{{ route('treasurer.fees.create') }}" class="btn-primary justify-center py-4 rounded-2xl text-center">➕ Add Fee</a>
        <a href="{{ route('treasurer.fees.bulk-create') }}" class="btn-secondary justify-center py-4 rounded-2xl text-center">📋 Bulk Fee</a>
        <a href="{{ route('treasurer.clearances') }}" class="btn-secondary justify-center py-4 rounded-2xl text-center">🚫 Clearances</a>
        <a href="{{ route('treasurer.aging') }}" class="btn-secondary justify-center py-4 rounded-2xl text-center">📉 Aging</a>
    </div>
</div>
@endsection