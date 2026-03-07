{{-- resources/views/treasurer/student-detail.blade.php --}}
@extends('treasurer.layouts.treasurer-app')
@section('title', $student->name)
@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center gap-4 fade-up">
        <a href="{{ route('treasurer.students') }}" class="btn-secondary px-3 py-2 rounded-xl text-sm self-start">← Students</a>
        <div class="flex-1">
            <h1 class="text-2xl font-bold text-gray-800">{{ $student->name }}</h1>
            <p class="text-sm text-gray-400">{{ $student->student_id ?? 'No ID' }} · {{ $student->level_group }} {{ $student->year_level }}</p>
        </div>
        <div class="flex gap-2 flex-wrap self-start">
            <a href="{{ route('treasurer.soa', $student) }}" class="btn-secondary">📄 View SOA</a>
            <a href="{{ route('treasurer.fees.create') }}?student_id={{ $student->id }}" class="btn-primary">➕ Add Fee</a>
        </div>
    </div>

    @if($clearance)
    <div class="flex items-center justify-between px-5 py-3 rounded-2xl fade-up"
         style="background: {{ $clearance->is_cleared ? '#f0fdf4' : '#fff1f2' }}; border: 1px solid {{ $clearance->is_cleared ? '#bbf7d0' : '#fecdd3' }};">
        <div class="flex items-center gap-3">
            <span class="text-2xl">{{ $clearance->is_cleared ? '✅' : '🚫' }}</span>
            <div>
                <p class="text-sm font-bold" style="color: {{ $clearance->is_cleared ? '#16a34a' : '#e11d48' }}">
                    {{ $clearance->is_cleared ? 'Finance Cleared' : 'On Hold — Finance Clearance Withheld' }}
                </p>
                @if(!$clearance->is_cleared && $clearance->hold_reason)
                    <p class="text-xs text-gray-500">{{ $clearance->hold_reason }}</p>
                @endif
            </div>
        </div>
        <a href="{{ route('treasurer.clearances') }}" class="text-xs font-semibold text-indigo-500 hover:text-indigo-700">Manage →</a>
    </div>
    @endif

    <div class="grid grid-cols-3 gap-4 fade-up fade-up-d1">
        <div class="stat-card">
            <p class="text-xs uppercase tracking-wider font-bold mb-2 text-gray-400">Total Fees</p>
            <p class="text-2xl font-bold text-gray-800 font-mono-num">₱{{ number_format($totalFees, 2) }}</p>
            <p class="text-xs mt-1 text-gray-400">S.Y. {{ $currentYear }}</p>
        </div>
        <div class="stat-card">
            <p class="text-xs uppercase tracking-wider font-bold mb-2 text-gray-400">Total Paid</p>
            <p class="text-2xl font-bold font-mono-num text-indigo-600">₱{{ number_format($totalPaid, 2) }}</p>
        </div>
        <div class="stat-card">
            <p class="text-xs uppercase tracking-wider font-bold mb-2 text-gray-400">Balance</p>
            <p class="text-2xl font-bold font-mono-num" style="color: {{ $balance > 0 ? '#e11d48' : '#16a34a' }}">
                {{ $balance > 0 ? '₱'.number_format($balance, 2) : 'Settled ✓' }}
            </p>
        </div>
    </div>

    @if($totalFees > 0)
    <div class="fade-up fade-up-d1">
        <div class="progress-bar-track" style="height: 10px; border-radius: 999px;">
            <div class="progress-bar-fill" style="width: {{ min(100, round(($totalPaid/$totalFees)*100)) }}%; height: 10px;"></div>
        </div>
        <p class="text-xs mt-1.5 font-semibold text-gray-400">{{ min(100, round(($totalPaid/$totalFees)*100)) }}% of fees paid</p>
    </div>
    @endif

    @if($scholarships->count())
    <div class="section-card p-5 fade-up fade-up-d2">
        <div class="flex items-center justify-between mb-3">
            <h3 class="text-sm font-bold text-gray-800">Active Scholarships / Discounts</h3>
            <a href="{{ route('treasurer.scholarships') }}" class="text-xs font-semibold text-indigo-500 hover:text-indigo-700">Manage →</a>
        </div>
        <div class="flex flex-wrap gap-2">
            @foreach($scholarships as $sc)
            <div class="px-3 py-2 rounded-xl text-xs bg-amber-50 border border-amber-100">
                <span class="font-bold text-gray-800">{{ $sc->scholarship_name }}</span>
                <span class="ml-2 text-amber-600 font-semibold">{{ $sc->discount_label }} off</span>
                @if($sc->applies_to_fee)<span class="text-gray-400"> · {{ $sc->applies_to_fee }}</span>@endif
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 fade-up fade-up-d2">
        <div class="section-card">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 bg-gray-50">
                <h3 class="text-sm font-bold text-gray-800">Assigned Fees</h3>
                <a href="{{ route('treasurer.fees') }}?search={{ urlencode($student->name) }}" class="text-xs font-semibold text-indigo-500 hover:text-indigo-700">Manage →</a>
            </div>
            <div class="divide-y divide-gray-50">
                @forelse($fees as $fee)
                <div class="flex items-center justify-between px-5 py-3.5">
                    <div>
                        <p class="text-sm font-semibold text-gray-800">{{ $fee->fee_name }}</p>
                        <p class="text-xs text-gray-400">{{ $fee->school_year }} · Sem {{ $fee->semester }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-bold font-mono-num text-indigo-600">₱{{ number_format($fee->amount, 2) }}</p>
                        @if($fee->status === 'active') <span class="col-badge col-badge-green text-xs">Active</span>
                        @elseif($fee->status === 'waived') <span class="col-badge col-badge-amber text-xs">Waived</span>
                        @else <span class="col-badge col-badge-red text-xs">Cancelled</span>
                        @endif
                    </div>
                </div>
                @empty
                <p class="px-5 py-6 text-sm text-center text-gray-400">No fees assigned.</p>
                @endforelse
            </div>
        </div>

        <div class="section-card">
            <div class="px-5 py-4 border-b border-gray-100 bg-gray-50">
                <h3 class="text-sm font-bold text-gray-800">Payment History</h3>
            </div>
            <div class="divide-y divide-gray-50">
                @forelse($payments as $p)
                <div class="flex items-center justify-between px-5 py-3.5">
                    <div>
                        <p class="text-sm font-semibold text-gray-800">{{ \Carbon\Carbon::parse($p->payment_date)->format('M j, Y') }}</p>
                        <p class="text-xs text-gray-400">{{ $p->payment_method }}@if($p->or_number) · OR# {{ $p->or_number }}@endif</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-bold font-mono-num text-indigo-600">₱{{ number_format($p->amount, 2) }}</p>
                        @if($p->status === 'completed') <span class="col-badge col-badge-green text-xs">Done</span>
                        @elseif($p->status === 'pending') <span class="col-badge col-badge-amber text-xs">Pending</span>
                        @else <span class="col-badge col-badge-red text-xs">{{ ucfirst($p->status) }}</span>
                        @endif
                    </div>
                </div>
                @empty
                <p class="px-5 py-6 text-sm text-center text-gray-400">No payments yet.</p>
                @endforelse
            </div>
        </div>
    </div>

    @if($installmentPlan)
    <div class="section-card fade-up fade-up-d3">
        <div class="px-5 py-4 border-b border-gray-100 bg-gray-50 flex items-center justify-between">
            <h3 class="text-sm font-bold text-gray-800">Installment Plan — {{ $installmentPlan->plan_type == 'full' ? 'Full Payment' : $installmentPlan->plan_type.'-installment' }}</h3>
            @if($installmentPlan->status === 'active') <span class="col-badge col-badge-indigo">Active</span>
            @elseif($installmentPlan->status === 'completed') <span class="col-badge col-badge-green">Completed</span>
            @else <span class="col-badge col-badge-red">Cancelled</span>
            @endif
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100 bg-gray-50">
                        <th class="text-left px-5 py-3 text-xs font-bold uppercase tracking-wider text-gray-400">#</th>
                        <th class="text-left px-4 py-3 text-xs font-bold uppercase tracking-wider text-gray-400">Due Date</th>
                        <th class="text-right px-4 py-3 text-xs font-bold uppercase tracking-wider text-gray-400">Amount Due</th>
                        <th class="text-right px-4 py-3 text-xs font-bold uppercase tracking-wider text-gray-400">Paid</th>
                        <th class="text-center px-4 py-3 text-xs font-bold uppercase tracking-wider text-gray-400">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($installmentPlan->schedules as $sched)
                    <tr class="tbl-row">
                        <td class="px-5 py-3 font-bold text-gray-800">{{ $sched->installment_number }}</td>
                        <td class="px-4 py-3 text-gray-700">{{ \Carbon\Carbon::parse($sched->due_date)->format('M j, Y') }}</td>
                        <td class="px-4 py-3 text-right font-mono-num text-gray-700">₱{{ number_format($sched->amount_due, 2) }}</td>
                        <td class="px-4 py-3 text-right font-mono-num text-indigo-600">{{ $sched->amount_paid > 0 ? '₱'.number_format($sched->amount_paid, 2) : '—' }}</td>
                        <td class="px-4 py-3 text-center">
                            @if($sched->is_paid) <span class="col-badge col-badge-green">Paid</span>
                            @elseif($sched->is_overdue) <span class="col-badge col-badge-red">Overdue</span>
                            @else <span class="col-badge col-badge-amber">Pending</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <div class="section-card p-5 fade-up fade-up-d4">
        <h3 class="text-sm font-bold text-gray-800 mb-4">Student Information</h3>
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 text-sm">
            @foreach([
                ['Email', $student->email],
                ['Phone', $student->phone ?? '—'],
                ['Level Group', $student->level_group ?? '—'],
                ['Year Level', $student->year_level ?? '—'],
                ['Program', $student->program ?? $student->strand ?? '—'],
                ['Gender', $student->gender ?? '—'],
            ] as [$label, $value])
            <div>
                <p class="text-xs font-bold uppercase tracking-wider mb-1 text-gray-400">{{ $label }}</p>
                <p class="text-gray-800 font-semibold">{{ $value }}</p>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection