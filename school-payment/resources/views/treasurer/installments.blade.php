{{-- resources/views/treasurer/installments.blade.php --}}
@extends('treasurer.layouts.treasurer-app')
@section('title', 'Installment Plans')
@section('content')
<div class="space-y-6">
    <div class="fade-up">
        <h1 class="text-2xl font-bold text-gray-800">Installment Plans</h1>
        <p class="text-sm mt-1 text-gray-400">Monitor all student payment plan schedules</p>
    </div>
    <div class="grid grid-cols-3 gap-4 fade-up fade-up-d1">
        <div class="stat-card">
            <p class="text-xs uppercase tracking-wider font-bold mb-2 text-gray-400">Active Plans</p>
            <p class="text-2xl font-bold font-mono-num text-indigo-600">{{ $stats['active'] }}</p>
        </div>
        <div class="stat-card">
            <p class="text-xs uppercase tracking-wider font-bold mb-2 text-gray-400">Completed</p>
            <p class="text-2xl font-bold font-mono-num text-emerald-600">{{ $stats['completed'] }}</p>
        </div>
        <div class="stat-card">
            <p class="text-xs uppercase tracking-wider font-bold mb-2 text-gray-400">Overdue Installments</p>
            <p class="text-2xl font-bold font-mono-num" style="color: {{ $stats['overdue']>0 ? '#e11d48' : '#16a34a' }}">{{ $stats['overdue'] }}</p>
        </div>
    </div>
    @if($overdueSchedules->count() > 0)
    <div class="section-card fade-up fade-up-d2 border-red-100">
        <div class="px-5 py-4 border-b border-red-100 bg-red-50">
            <h3 class="text-sm font-bold text-red-600">🚨 Overdue Installments</h3>
        </div>
        <div class="divide-y divide-gray-50">
            @foreach($overdueSchedules as $sched)
            <div class="flex items-center justify-between px-5 py-3.5">
                <div>
                    <p class="text-sm font-semibold text-gray-800">{{ $sched->plan->student->name ?? '—' }}</p>
                    <p class="text-xs text-gray-400">Installment #{{ $sched->installment_number }} · Due {{ \Carbon\Carbon::parse($sched->due_date)->format('M j, Y') }} ({{ \Carbon\Carbon::parse($sched->due_date)->diffForHumans() }})</p>
                </div>
                <div class="text-right">
                    <p class="text-sm font-bold font-mono-num text-red-500">₱{{ number_format($sched->amount_due, 2) }}</p>
                    <a href="{{ route('treasurer.student.detail', $sched->plan->student_id) }}" class="text-xs font-semibold text-indigo-500 hover:text-indigo-700">View Student →</a>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
    <div class="section-card p-4 fade-up fade-up-d2">
        <form method="GET" class="flex flex-wrap gap-3 items-end">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Student name or ID…" class="form-input flex-1 min-w-[180px]">
            <select name="school_year" class="form-input w-auto">
                <option value="">All Years</option>
                @foreach($schoolYears as $sy)
                    <option value="{{ $sy }}" {{ request('school_year')==$sy?'selected':'' }}>{{ $sy }}</option>
                @endforeach
            </select>
            <select name="status" class="form-input w-auto">
                <option value="">All Status</option>
                <option value="active"    {{ request('status')=='active'?'selected':'' }}>Active</option>
                <option value="completed" {{ request('status')=='completed'?'selected':'' }}>Completed</option>
                <option value="cancelled" {{ request('status')=='cancelled'?'selected':'' }}>Cancelled</option>
            </select>
            <button type="submit" class="btn-primary">🔍 Filter</button>
            <a href="{{ route('treasurer.installments') }}" class="btn-secondary">Clear</a>
        </form>
    </div>
    <div class="section-card fade-up fade-up-d3">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100 bg-gray-50">
                        <th class="text-left px-5 py-4 text-xs font-bold uppercase tracking-wider text-gray-400">Student</th>
                        <th class="text-left px-4 py-4 text-xs font-bold uppercase tracking-wider text-gray-400">Plan</th>
                        <th class="text-right px-4 py-4 text-xs font-bold uppercase tracking-wider text-gray-400">Total</th>
                        <th class="text-right px-4 py-4 text-xs font-bold uppercase tracking-wider text-gray-400">Per Installment</th>
                        <th class="text-center px-4 py-4 text-xs font-bold uppercase tracking-wider text-gray-400">Progress</th>
                        <th class="text-center px-4 py-4 text-xs font-bold uppercase tracking-wider text-gray-400">Status</th>
                        <th class="text-center px-4 py-4 text-xs font-bold uppercase tracking-wider text-gray-400">Detail</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($plans as $plan)
                    @php
                        $paidCount  = $plan->schedules->where('is_paid', true)->count();
                        $totalCount = $plan->total_installments;
                        $progressPct = $totalCount > 0 ? round(($paidCount/$totalCount)*100) : 0;
                        $hasOverdue  = $plan->schedules->where('is_paid', false)->where('is_overdue', true)->count() > 0;
                    @endphp
                    <tr class="tbl-row">
                        <td class="px-5 py-3.5">
                            <p class="font-semibold text-gray-800">{{ $plan->student->name ?? '—' }}</p>
                            <p class="text-xs text-gray-400">{{ $plan->school_year }} · Sem {{ $plan->semester }}</p>
                        </td>
                        <td class="px-4 py-3.5">
                            <p class="font-semibold text-gray-800">{{ $plan->plan_type == 'full' ? 'Full Payment' : $plan->plan_type.'-Installment' }}</p>
                            <p class="text-xs text-gray-400">{{ $paidCount }}/{{ $totalCount }} paid</p>
                        </td>
                        <td class="px-4 py-3.5 text-right font-bold font-mono-num text-gray-800">₱{{ number_format($plan->total_amount, 2) }}</td>
                        <td class="px-4 py-3.5 text-right font-mono-num text-gray-500">₱{{ number_format($plan->amount_per_installment, 2) }}</td>
                        <td class="px-4 py-3.5 text-center w-28">
                            <div class="progress-bar-track">
                                <div class="progress-bar-fill" style="width: {{ $progressPct }}%; background: {{ $hasOverdue ? 'linear-gradient(90deg,#e11d48,#f43f5e)' : 'linear-gradient(90deg,#4f46e5,#6366f1)' }};"></div>
                            </div>
                            <p class="text-xs mt-1 text-gray-400">{{ $progressPct }}%</p>
                        </td>
                        <td class="px-4 py-3.5 text-center">
                            @if($plan->status === 'active') @if($hasOverdue) <span class="col-badge col-badge-red">Overdue</span> @else <span class="col-badge col-badge-indigo">Active</span> @endif
                            @elseif($plan->status === 'completed') <span class="col-badge col-badge-green">Completed</span>
                            @else <span class="col-badge col-badge-red">Cancelled</span>
                            @endif
                        </td>
                        <td class="px-4 py-3.5 text-center">
                            <a href="{{ route('treasurer.student.detail', $plan->student_id) }}" class="text-xs font-semibold px-3 py-1.5 rounded-lg bg-indigo-50 text-indigo-500 border border-indigo-100 hover:bg-indigo-100 transition">View →</a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="px-5 py-10 text-center text-sm text-gray-400">No installment plans found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($plans->hasPages())
        <div class="px-5 py-4 border-t border-gray-100 bg-gray-50">{{ $plans->links() }}</div>
        @endif
    </div>
</div>
@endsection