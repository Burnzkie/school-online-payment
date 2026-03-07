{{-- resources/views/admin/installments/index.blade.php --}}
@extends('admin.layouts.admin-app')
@section('title', 'Installment Plans')

@section('content')

<div class="a-fade">
    <h2 class="text-xl font-bold text-gray-800">Installment Plans</h2>
    <p class="text-sm mt-0.5 text-gray-400">Monitor all active payment plans and overdue schedules</p>
</div>

<div class="grid grid-cols-3 gap-4 a-fade a-d1">
    <div class="a-card px-5 py-4"><p class="text-2xl font-bold text-gray-800">{{ $stats['active'] }}</p><p class="text-xs mt-1 font-semibold text-indigo-600">Active Plans</p></div>
    <div class="a-card px-5 py-4"><p class="text-2xl font-bold text-gray-800">{{ $stats['completed'] }}</p><p class="text-xs mt-1 font-semibold text-emerald-600">Completed</p></div>
    <div class="a-card px-5 py-4"><p class="text-2xl font-bold text-gray-800">{{ $stats['overdue'] }}</p><p class="text-xs mt-1 font-semibold text-red-500">Overdue Installments</p></div>
</div>

{{-- Overdue Alert --}}
@if($overdueSchedules->count())
<div class="a-card a-fade a-d2 border-red-100">
    <div class="px-6 py-4 border-b border-red-100">
        <h3 class="font-bold text-red-500">⚠️ Overdue Installments</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="a-table">
            <thead><tr><th>Student</th><th>Installment</th><th>Amount Due</th><th>Due Date</th><th>Days Overdue</th></tr></thead>
            <tbody>
                @foreach($overdueSchedules as $s)
                <tr>
                    <td>
                        <p class="font-semibold text-gray-800">{{ $s->plan->student->name ?? '—' }}</p>
                        <p class="text-xs font-mono-num text-gray-400">{{ $s->plan->student->student_id ?? '' }}</p>
                    </td>
                    <td class="text-sm text-gray-600">No. {{ $s->installment_number }} of {{ $s->plan->total_installments }}</td>
                    <td class="font-bold font-mono-num text-red-500">₱{{ number_format($s->amount_due,2) }}</td>
                    <td class="text-sm text-red-500">{{ \Carbon\Carbon::parse($s->due_date)->format('M d, Y') }}</td>
                    <td class="font-bold text-red-600">{{ \Carbon\Carbon::parse($s->due_date)->diffInDays(now()) }} days</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

<form class="a-card p-4 flex flex-wrap gap-3 a-fade a-d3" method="GET">
    <input name="q" value="{{ request('q') }}" placeholder="Search student…" class="a-input flex-1 min-w-44">
    <select name="school_year" class="a-input a-select w-36"><option value="">All Years</option>@foreach($schoolYears as $y)<option value="{{ $y }}" {{ request('school_year')===$y?'selected':'' }}>{{ $y }}</option>@endforeach</select>
    <select name="semester" class="a-input a-select w-36"><option value="">All Semesters</option>@foreach(['1'=>'1st','2'=>'2nd','summer'=>'Summer'] as $v=>$l)<option value="{{ $v }}" {{ request('semester')===$v?'selected':'' }}>{{ $l }}</option>@endforeach</select>
    <select name="status" class="a-input a-select w-32"><option value="">All Status</option>@foreach(['active','completed','cancelled'] as $s)<option value="{{ $s }}" {{ request('status')===$s?'selected':'' }}>{{ ucfirst($s) }}</option>@endforeach</select>
    <button type="submit" class="a-btn-primary px-5">Filter</button>
    <a href="{{ route('admin.installments') }}" class="a-btn-secondary">Reset</a>
</form>

<div class="a-card a-fade a-d4">
    <div class="overflow-x-auto">
        <table class="a-table">
            <thead><tr><th>Student</th><th>Plan</th><th>Total</th><th>Per Installment</th><th>Progress</th><th>Period</th><th>Status</th></tr></thead>
            <tbody>
                @forelse($plans as $plan)
                @php
                    $paidCount = $plan->schedules->where('is_paid',true)->count();
                    $pct = $plan->total_installments > 0 ? round($paidCount/$plan->total_installments*100) : 0;
                @endphp
                <tr>
                    <td>
                        <p class="font-semibold text-gray-800 text-sm">{{ $plan->student->name ?? '—' }}</p>
                        <p class="text-xs font-mono-num text-gray-400">{{ $plan->student->student_id ?? '' }}</p>
                    </td>
                    <td><span class="a-badge a-badge-sky">{{ $plan->plan_type==='full'?'Full':'Part '.$plan->plan_type }}</span></td>
                    <td class="font-bold font-mono-num text-sm text-indigo-600">₱{{ number_format($plan->total_amount,2) }}</td>
                    <td class="font-mono-num text-sm text-gray-700">₱{{ number_format($plan->amount_per_installment,2) }}</td>
                    <td class="w-36">
                        <div class="flex items-center gap-2">
                            <div class="a-progress-track flex-1"><div class="a-progress-fill" style="width:{{ $pct }}%"></div></div>
                            <span class="text-xs text-gray-400">{{ $paidCount }}/{{ $plan->total_installments }}</span>
                        </div>
                    </td>
                    <td class="text-xs text-gray-400">{{ $plan->school_year }} · Sem {{ $plan->semester }}</td>
                    <td><span class="a-badge {{ $plan->status==='active'?'a-badge-emerald':($plan->status==='completed'?'a-badge-sky':'a-badge-gray') }}">{{ ucfirst($plan->status) }}</span></td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center py-12 text-gray-400">No installment plans found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-6 py-4">{{ $plans->withQueryString()->links() }}</div>
</div>
@endsection