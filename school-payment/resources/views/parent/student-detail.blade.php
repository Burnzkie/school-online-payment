{{-- resources/views/parent/student-detail.blade.php --}}
@extends('parent.layouts.app')

@section('title', $student->name . ' – Billing')
@section('page-title', $student->name . ' ' . $student->last_name)
@section('breadcrumb', 'Dashboard › ' . $student->name . ' › Billing')

@section('content')

{{-- ── Student Header ── --}}
<div class="bg-white border border-gray-100 rounded-2xl shadow-sm p-6 mb-6 flex flex-col sm:flex-row items-start sm:items-center gap-5">
    @if($student->profile_picture)
        <img src="{{ asset('storage/'.$student->profile_picture) }}"
             class="w-16 h-16 rounded-2xl object-cover ring-2 ring-indigo-100">
    @else
        <div class="w-16 h-16 rounded-2xl flex items-center justify-center text-2xl font-bold text-white"
             style="background: linear-gradient(135deg, #4f46e5, #6366f1);">
            {{ strtoupper(substr($student->name, 0, 1)) }}
        </div>
    @endif
    <div class="flex-1">
        <h2 class="text-xl font-bold text-gray-800">{{ $student->name }} {{ $student->middle_name }} {{ $student->last_name }}</h2>
        <div class="flex flex-wrap gap-2 mt-2">
            <span class="text-xs bg-indigo-50 text-indigo-600 border border-indigo-100 px-2.5 py-1 rounded-full font-medium">{{ $student->year_level }}</span>
            <span class="text-xs bg-gray-100 text-gray-500 px-2.5 py-1 rounded-full">{{ $student->level_group }}</span>
            @if($student->student_id)
            <span class="text-xs bg-gray-100 text-gray-400 px-2.5 py-1 rounded-full font-mono">ID: {{ $student->student_id }}</span>
            @endif
        </div>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('parent.student.payments', $student) }}"
           class="text-sm bg-white hover:bg-gray-50 border border-gray-200 text-gray-600 px-4 py-2 rounded-xl transition">
            <i class="fas fa-history mr-1.5"></i> History
        </a>
        <a href="{{ route('parent.student.statement', $student) }}"
           class="text-sm text-white px-4 py-2 rounded-xl transition shadow-sm"
           style="background: linear-gradient(135deg, #4f46e5, #6366f1);">
            <i class="fas fa-receipt mr-1.5"></i> Statement
        </a>
    </div>
</div>

{{-- ── Semester Filter ── --}}
<div class="flex flex-wrap gap-2 mb-6">
    @foreach($availableYears as $yr)
    @foreach(['1' => '1st Sem', '2' => '2nd Sem', 'summer' => 'Summer'] as $sem => $semLabel)
    <a href="{{ route('parent.student.detail', [$student, 'school_year' => $yr, 'semester' => $sem]) }}"
       class="text-sm px-4 py-2 rounded-xl transition font-medium
              {{ $schoolYear == $yr && $semester == $sem
                 ? 'text-white shadow-sm'
                 : 'bg-white border border-gray-200 text-gray-500 hover:border-indigo-200 hover:text-indigo-600' }}"
       @if($schoolYear == $yr && $semester == $sem)
       style="background: linear-gradient(135deg, #4f46e5, #6366f1);"
       @endif>
        {{ $yr }} {{ $semLabel }}
    </a>
    @endforeach
    @endforeach
</div>

{{-- ── Summary Cards ── --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    <div class="bg-white border border-gray-100 rounded-2xl shadow-sm p-5">
        <p class="text-xs text-gray-400 mb-2">Total Fees</p>
        <p class="text-2xl font-bold text-gray-800">₱{{ number_format($totalFees, 2) }}</p>
    </div>
    <div class="bg-white border border-gray-100 rounded-2xl shadow-sm p-5">
        <p class="text-xs text-gray-400 mb-2">Total Paid</p>
        <p class="text-2xl font-bold text-emerald-600">₱{{ number_format($totalPaid, 2) }}</p>
    </div>
    <div class="bg-white border border-gray-100 rounded-2xl shadow-sm p-5">
        <p class="text-xs text-gray-400 mb-2">Balance</p>
        <p class="text-2xl font-bold {{ $balance > 0 ? 'text-red-500' : 'text-emerald-600' }}">
            ₱{{ number_format($balance, 2) }}
        </p>
    </div>
    <div class="bg-white border border-gray-100 rounded-2xl shadow-sm p-5">
        <p class="text-xs text-gray-400 mb-2">Progress</p>
        <p class="text-2xl font-bold text-indigo-600">{{ $progress }}%</p>
        <div class="h-1.5 bg-gray-100 rounded-full mt-2 overflow-hidden">
            <div class="h-full rounded-full"
                 style="width: {{ $progress }}%; background: linear-gradient(90deg, #4f46e5, #6366f1);"></div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

    {{-- ── Fees Breakdown ── --}}
    <div class="bg-white border border-gray-100 rounded-2xl shadow-sm p-6">
        <h3 class="font-bold text-lg mb-4 flex items-center gap-2 text-gray-800">
            <i class="fas fa-file-invoice text-indigo-400"></i> Fee Breakdown
        </h3>
        @if($fees->isEmpty())
            <p class="text-gray-400 text-sm text-center py-6 bg-gray-50 rounded-xl">No fees found for this semester.</p>
        @else
        <div class="space-y-3">
            @foreach($fees as $fee)
            <div class="flex items-center justify-between py-2.5 border-b border-gray-50 last:border-0">
                <div>
                    <p class="text-sm font-semibold text-gray-800">{{ $fee->fee_name }}</p>
                    @if($fee->description)
                    <p class="text-xs text-gray-400 mt-0.5">{{ $fee->description }}</p>
                    @endif
                </div>
                <div class="text-right">
                    <p class="font-semibold text-gray-800">₱{{ number_format($fee->amount, 2) }}</p>
                    <span class="text-xs px-2 py-0.5 rounded-full font-semibold
                        {{ $fee->status === 'active' ? 'bg-emerald-50 text-emerald-600 border border-emerald-100'
                           : ($fee->status === 'waived' ? 'bg-indigo-50 text-indigo-600 border border-indigo-100'
                           : 'bg-gray-100 text-gray-500') }}">
                        {{ ucfirst($fee->status) }}
                    </span>
                </div>
            </div>
            @endforeach
            <div class="flex justify-between pt-3 font-bold text-sm border-t-2 border-gray-200">
                <span class="text-gray-800">Total</span>
                <span class="text-gray-800">₱{{ number_format($totalFees, 2) }}</span>
            </div>
        </div>
        @endif
    </div>

    {{-- ── Installment Plan ── --}}
    <div class="bg-white border border-gray-100 rounded-2xl shadow-sm p-6">
        <h3 class="font-bold text-lg mb-4 flex items-center gap-2 text-gray-800">
            <i class="fas fa-calendar-check text-indigo-400"></i> Payment Plan
        </h3>
        @if(!$installmentPlan)
            <p class="text-gray-400 text-sm text-center py-6 bg-gray-50 rounded-xl">No installment plan set up for this semester.</p>
        @else
        <div class="mb-4 flex items-center gap-3 bg-indigo-50 border border-indigo-100 rounded-xl p-4">
            <div class="w-10 h-10 bg-indigo-100 rounded-xl flex items-center justify-center">
                <i class="fas fa-layer-group text-indigo-500"></i>
            </div>
            <div>
                <p class="text-sm font-semibold text-gray-800">
                    @if($installmentPlan->plan_type === 'full') Full Payment
                    @else {{ $installmentPlan->plan_type }}-Installment Plan
                    @endif
                </p>
                <p class="text-xs text-gray-400">
                    ₱{{ number_format($installmentPlan->amount_per_installment, 2) }}/installment
                    &bull; Total: ₱{{ number_format($installmentPlan->total_amount, 2) }}
                </p>
            </div>
            <span class="ml-auto text-xs px-2.5 py-1 rounded-full font-semibold
                {{ $installmentPlan->status === 'active'    ? 'bg-emerald-50 text-emerald-600 border border-emerald-100'
                   : ($installmentPlan->status === 'completed' ? 'bg-indigo-50 text-indigo-600 border border-indigo-100'
                   : 'bg-gray-100 text-gray-500') }}">
                {{ ucfirst($installmentPlan->status) }}
            </span>
        </div>

        <div class="space-y-3">
            @foreach($installmentPlan->schedules->sortBy('installment_number') as $schedule)
            <div class="flex items-center gap-3 py-2.5 border-b border-gray-50 last:border-0">
                <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0
                    {{ $schedule->is_paid    ? 'bg-emerald-100 text-emerald-600'
                       : ($schedule->is_overdue ? 'bg-red-100 text-red-500'
                       : 'bg-gray-100 text-gray-500') }}">
                    @if($schedule->is_paid) <i class="fas fa-check text-xs"></i>
                    @elseif($schedule->is_overdue) <i class="fas fa-times text-xs"></i>
                    @else {{ $schedule->installment_number }}
                    @endif
                </div>
                <div class="flex-1">
                    <p class="text-sm font-semibold text-gray-800">Installment {{ $schedule->installment_number }}</p>
                    <p class="text-xs text-gray-400">Due {{ \Carbon\Carbon::parse($schedule->due_date)->format('M d, Y') }}</p>
                </div>
                <div class="text-right">
                    <p class="text-sm font-semibold text-gray-800">₱{{ number_format($schedule->amount_due, 2) }}</p>
                    @if($schedule->is_paid)
                        <span class="text-xs text-emerald-600 font-medium">Paid</span>
                    @elseif($schedule->is_overdue)
                        <span class="text-xs text-red-500 font-medium">Overdue</span>
                    @else
                        <span class="text-xs text-gray-400">Pending</span>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>

</div>

{{-- ── Recent Payments ── --}}
<div class="bg-white border border-gray-100 rounded-2xl shadow-sm p-6 mt-6">
    <div class="flex items-center justify-between mb-4">
        <h3 class="font-bold text-lg flex items-center gap-2 text-gray-800">
            <i class="fas fa-history text-indigo-400"></i> Payment History
        </h3>
        <a href="{{ route('parent.student.payments', $student) }}"
           class="text-xs text-indigo-500 hover:text-indigo-700 transition font-medium">View all →</a>
    </div>

    @if($payments->isEmpty())
        <p class="text-gray-400 text-sm text-center py-6 bg-gray-50 rounded-xl">No payments recorded for this semester.</p>
    @else
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-xs text-gray-400 border-b border-gray-100">
                    <th class="text-left pb-3 font-bold uppercase tracking-wider">Date</th>
                    <th class="text-left pb-3 font-bold uppercase tracking-wider">OR #</th>
                    <th class="text-left pb-3 font-bold uppercase tracking-wider">Method</th>
                    <th class="text-right pb-3 font-bold uppercase tracking-wider">Amount</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($payments as $pmt)
                <tr class="hover:bg-indigo-50/30 transition">
                    <td class="py-3 font-medium text-gray-800">{{ \Carbon\Carbon::parse($pmt->payment_date)->format('M d, Y') }}</td>
                    <td class="py-3 text-gray-400 font-mono text-xs">{{ $pmt->or_number ?? '—' }}</td>
                    <td class="py-3">
                        <span class="text-xs px-2.5 py-1 rounded-full bg-indigo-50 text-indigo-600 border border-indigo-100 font-medium">
                            {{ $pmt->payment_method }}
                        </span>
                    </td>
                    <td class="py-3 text-right font-semibold text-emerald-600">
                        ₱{{ number_format($pmt->amount, 2) }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>

@endsection