{{-- resources/views/parent/statement.blade.php --}}
@extends('parent.layouts.app')

@section('title', $student->name . ' – Statement')
@section('page-title', 'Account Statement')
@section('breadcrumb', 'Dashboard › ' . $student->name . ' › Statement')

@section('content')

{{-- ── Actions bar ── --}}
<div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
    <div class="flex items-center gap-3">
        <a href="{{ route('parent.student.detail', $student) }}"
           class="w-9 h-9 rounded-xl flex items-center justify-center text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition">
            <i class="fas fa-arrow-left text-sm"></i>
        </a>
        <div>
            <h2 class="font-bold text-lg text-gray-800">{{ $student->name }} {{ $student->last_name }}</h2>
            <p class="text-xs text-gray-400">{{ $schoolYear }} — {{ $semester == '1' ? '1st Semester' : ($semester == '2' ? '2nd Semester' : 'Summer') }}</p>
        </div>
    </div>
    <button onclick="window.print()"
            class="flex items-center gap-2 text-white text-sm font-medium px-4 py-2.5 rounded-xl transition shadow-sm"
            style="background: linear-gradient(135deg, #4f46e5, #6366f1);">
        <i class="fas fa-print"></i> Print Statement
    </button>
</div>

{{-- ── Semester Filter ── --}}
<div class="flex flex-wrap gap-2 mb-6">
    @foreach($availableYears as $yr)
    @foreach(['1' => '1st Sem', '2' => '2nd Sem', 'summer' => 'Summer'] as $sem => $semLabel)
    <a href="{{ route('parent.student.statement', [$student, 'school_year' => $yr, 'semester' => $sem]) }}"
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

{{-- ── Printable statement area ── --}}
<div id="statement-area" class="bg-white border border-gray-100 rounded-2xl shadow-sm p-6 lg:p-8 print:shadow-none print:border-none">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 pb-6 border-b border-gray-100 mb-6">
        <div>
            <div class="flex items-center gap-3 mb-2">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center text-2xl shadow-sm"
                     style="background: linear-gradient(135deg, #4f46e5, #6366f1);">🎓</div>
                <div>
                    <p class="font-extrabold text-lg leading-none text-gray-800">Philippine Advent College</p>
                    <p class="text-xs text-gray-400 mt-0.5">Official Account Statement</p>
                </div>
            </div>
        </div>
        <div class="text-right text-xs text-gray-400">
            <p>Generated: {{ now()->format('F j, Y g:i A') }}</p>
            <p class="mt-0.5">Prepared for: <span class="text-gray-700 font-semibold">{{ auth()->user()->name }} {{ auth()->user()->last_name }}</span></p>
        </div>
    </div>

    {{-- Student Info --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-6 pb-6 border-b border-gray-100">
        <div>
            <p class="text-xs text-gray-400 uppercase tracking-wider mb-1 font-bold">Student</p>
            <p class="font-bold text-base text-gray-800">{{ $student->name }} {{ $student->middle_name }} {{ $student->last_name }}</p>
            @if($student->student_id)
            <p class="text-xs text-gray-400 mt-0.5 font-mono">ID: {{ $student->student_id }}</p>
            @endif
        </div>
        <div>
            <p class="text-xs text-gray-400 uppercase tracking-wider mb-1 font-bold">Academic Period</p>
            <p class="font-semibold text-gray-800">{{ $schoolYear }}</p>
            <p class="text-sm text-indigo-500 font-medium">
                @if($semester == '1') 1st Semester
                @elseif($semester == '2') 2nd Semester
                @else Summer Term
                @endif
            </p>
        </div>
        <div>
            <p class="text-xs text-gray-400 uppercase tracking-wider mb-1 font-bold">Year Level</p>
            <p class="font-medium text-gray-800">{{ $student->year_level }}</p>
            <p class="text-xs text-gray-400">{{ $student->level_group }}</p>
        </div>
        @if($student->strand || $student->program)
        <div>
            <p class="text-xs text-gray-400 uppercase tracking-wider mb-1 font-bold">{{ $student->strand ? 'Strand' : 'Program' }}</p>
            <p class="font-medium text-gray-800">{{ $student->strand ?? $student->program }}</p>
        </div>
        @endif
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-3 gap-4 mb-6">
        <div class="bg-gray-50 border border-gray-100 rounded-xl p-4 text-center">
            <p class="text-xs text-gray-400 mb-1">Total Fees</p>
            <p class="text-xl font-bold text-gray-800">₱{{ number_format($totalFees, 2) }}</p>
        </div>
        <div class="bg-emerald-50 border border-emerald-100 rounded-xl p-4 text-center">
            <p class="text-xs text-gray-400 mb-1">Total Paid</p>
            <p class="text-xl font-bold text-emerald-600">₱{{ number_format($totalPaid, 2) }}</p>
        </div>
        <div class="{{ $balance > 0 ? 'bg-red-50 border-red-100' : 'bg-emerald-50 border-emerald-100' }} border rounded-xl p-4 text-center">
            <p class="text-xs text-gray-400 mb-1">Balance</p>
            <p class="text-xl font-bold {{ $balance > 0 ? 'text-red-500' : 'text-emerald-600' }}">₱{{ number_format($balance, 2) }}</p>
        </div>
    </div>

    {{-- Fees Table --}}
    <div class="mb-6">
        <h3 class="font-bold text-xs text-gray-400 uppercase tracking-wider mb-3">Fee Assessment</h3>
        @if($fees->isEmpty())
            <p class="text-gray-400 text-sm text-center py-6 bg-gray-50 rounded-xl border border-gray-100">No fees assessed for this semester.</p>
        @else
        <div class="overflow-x-auto rounded-xl border border-gray-100">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr class="text-xs text-gray-400 uppercase tracking-wider">
                        <th class="text-left px-5 py-3 font-bold">Description</th>
                        <th class="text-left px-5 py-3 font-bold hidden sm:table-cell">Notes</th>
                        <th class="text-center px-5 py-3 font-bold">Status</th>
                        <th class="text-right px-5 py-3 font-bold">Amount</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($fees as $fee)
                    <tr class="hover:bg-indigo-50/30 transition">
                        <td class="px-5 py-3 font-semibold text-gray-800">{{ $fee->fee_name }}</td>
                        <td class="px-5 py-3 text-gray-400 text-xs hidden sm:table-cell">{{ $fee->description ?? '—' }}</td>
                        <td class="px-5 py-3 text-center">
                            <span class="text-xs px-2.5 py-0.5 rounded-full font-semibold
                                {{ $fee->status === 'active'  ? 'bg-emerald-50 text-emerald-600 border border-emerald-100'
                                 : ($fee->status === 'waived' ? 'bg-indigo-50 text-indigo-600 border border-indigo-100'
                                 : 'bg-gray-100 text-gray-500 border border-gray-200') }}">
                                {{ ucfirst($fee->status) }}
                            </span>
                        </td>
                        <td class="px-5 py-3 text-right font-semibold text-gray-800">₱{{ number_format($fee->amount, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50 border-t-2 border-gray-200">
                    <tr>
                        <td colspan="3" class="px-5 py-3 font-bold text-sm text-gray-800">Total Assessment</td>
                        <td class="px-5 py-3 text-right font-bold text-base text-gray-800">₱{{ number_format($totalFees, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
        @endif
    </div>

    {{-- Payments Table --}}
    <div class="mb-6">
        <h3 class="font-bold text-xs text-gray-400 uppercase tracking-wider mb-3">Payment History</h3>
        @if($payments->isEmpty())
            <p class="text-gray-400 text-sm text-center py-6 bg-gray-50 rounded-xl border border-gray-100">No payments recorded this semester.</p>
        @else
        <div class="overflow-x-auto rounded-xl border border-gray-100">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr class="text-xs text-gray-400 uppercase tracking-wider">
                        <th class="text-left px-5 py-3 font-bold">Date</th>
                        <th class="text-left px-5 py-3 font-bold hidden sm:table-cell">OR #</th>
                        <th class="text-left px-5 py-3 font-bold hidden sm:table-cell">Method</th>
                        <th class="text-left px-5 py-3 font-bold hidden md:table-cell">Reference</th>
                        <th class="text-center px-5 py-3 font-bold">Status</th>
                        <th class="text-right px-5 py-3 font-bold">Amount</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($payments as $pmt)
                    <tr class="hover:bg-indigo-50/30 transition">
                        <td class="px-5 py-3 font-semibold text-gray-800 whitespace-nowrap">{{ \Carbon\Carbon::parse($pmt->payment_date)->format('M d, Y') }}</td>
                        <td class="px-5 py-3 font-mono text-xs text-gray-400 hidden sm:table-cell">{{ $pmt->or_number ?? '—' }}</td>
                        <td class="px-5 py-3 hidden sm:table-cell">
                            <span class="text-xs px-2.5 py-1 rounded-full bg-indigo-50 text-indigo-600 border border-indigo-100 font-medium">{{ $pmt->payment_method }}</span>
                        </td>
                        <td class="px-5 py-3 font-mono text-xs text-gray-300 hidden md:table-cell">{{ $pmt->reference_number ?? '—' }}</td>
                        <td class="px-5 py-3 text-center">
                            <span class="text-xs px-2.5 py-0.5 rounded-full font-semibold
                                {{ $pmt->status === 'completed' ? 'bg-emerald-50 text-emerald-600 border border-emerald-100'
                                 : ($pmt->status === 'pending'  ? 'bg-amber-50 text-amber-600 border border-amber-100'
                                 : 'bg-red-50 text-red-500 border border-red-100') }}">
                                {{ ucfirst($pmt->status) }}
                            </span>
                        </td>
                        <td class="px-5 py-3 text-right font-bold text-emerald-600">₱{{ number_format($pmt->amount, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50 border-t-2 border-gray-200">
                    <tr>
                        <td colspan="5" class="px-5 py-3 font-bold text-sm text-gray-800">Total Payments</td>
                        <td class="px-5 py-3 text-right font-bold text-base text-emerald-600">₱{{ number_format($totalPaid, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
        @endif
    </div>

    {{-- Installment Schedule --}}
    @if($installmentPlan)
    <div class="mb-6">
        <h3 class="font-bold text-xs text-gray-400 uppercase tracking-wider mb-3">Installment Schedule</h3>
        <div class="flex items-center gap-3 bg-indigo-50 border border-indigo-100 rounded-xl px-4 py-3 mb-4">
            <i class="fas fa-layer-group text-indigo-500 text-sm"></i>
            <span class="text-sm font-semibold text-gray-800">
                @if($installmentPlan->plan_type === 'full') Full Payment Plan
                @else {{ $installmentPlan->plan_type }}-Installment Plan
                @endif
            </span>
            <span class="ml-auto text-xs px-2.5 py-1 rounded-full font-semibold
                {{ $installmentPlan->status === 'active'    ? 'bg-emerald-50 text-emerald-600 border border-emerald-100'
                 : ($installmentPlan->status === 'completed' ? 'bg-indigo-50 text-indigo-600 border border-indigo-100'
                 : 'bg-gray-100 text-gray-500 border border-gray-200') }}">
                {{ ucfirst($installmentPlan->status) }}
            </span>
        </div>
        <div class="overflow-x-auto rounded-xl border border-gray-100">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr class="text-xs text-gray-400 uppercase tracking-wider">
                        <th class="text-left px-5 py-3 font-bold">#</th>
                        <th class="text-left px-5 py-3 font-bold">Due Date</th>
                        <th class="text-right px-5 py-3 font-bold">Amount Due</th>
                        <th class="text-center px-5 py-3 font-bold">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($installmentPlan->schedules->sortBy('installment_number') as $schedule)
                    <tr class="hover:bg-indigo-50/30 transition">
                        <td class="px-5 py-3">
                            <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold
                                {{ $schedule->is_paid    ? 'bg-emerald-100 text-emerald-600'
                                 : ($schedule->is_overdue ? 'bg-red-100 text-red-500'
                                 : 'bg-gray-100 text-gray-500') }}">
                                @if($schedule->is_paid) <i class="fas fa-check text-xs"></i>
                                @elseif($schedule->is_overdue) <i class="fas fa-times text-xs"></i>
                                @else {{ $schedule->installment_number }}
                                @endif
                            </div>
                        </td>
                        <td class="px-5 py-3 text-gray-700 whitespace-nowrap">{{ \Carbon\Carbon::parse($schedule->due_date)->format('M d, Y') }}</td>
                        <td class="px-5 py-3 text-right font-semibold text-gray-800">₱{{ number_format($schedule->amount_due, 2) }}</td>
                        <td class="px-5 py-3 text-center">
                            @if($schedule->is_paid)
                                <span class="text-xs px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-600 border border-emerald-100 font-semibold">Paid</span>
                            @elseif($schedule->is_overdue)
                                <span class="text-xs px-2.5 py-1 rounded-full bg-red-50 text-red-500 border border-red-100 font-semibold">Overdue</span>
                            @else
                                <span class="text-xs px-2.5 py-1 rounded-full bg-gray-100 text-gray-500 border border-gray-200 font-semibold">Pending</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- Balance Summary --}}
    <div class="border-t border-gray-100 pt-6">
        <div class="max-w-xs ml-auto space-y-2 text-sm">
            <div class="flex justify-between">
                <span class="text-gray-400">Total Assessed</span>
                <span class="font-semibold text-gray-800">₱{{ number_format($totalFees, 2) }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-400">Total Paid</span>
                <span class="font-semibold text-emerald-600">₱{{ number_format($totalPaid, 2) }}</span>
            </div>
            <div class="flex justify-between pt-2 border-t border-gray-200 font-bold text-base">
                <span class="text-gray-800">{{ $balance > 0 ? 'Remaining Balance' : 'Overpayment' }}</span>
                <span class="{{ $balance > 0 ? 'text-red-500' : 'text-emerald-600' }}">
                    ₱{{ number_format(abs($balance), 2) }}
                </span>
            </div>
        </div>
    </div>

    {{-- Footer note --}}
    <p class="text-xs text-gray-300 text-center mt-8">
        This is a system-generated statement. For concerns, please contact the school cashier.
    </p>

</div>

<style>
@media print {
    body { background: white !important; }
    #statement-area { border: 1px solid #e5e7eb !important; box-shadow: none !important; }
    button, .flex.flex-wrap.gap-2, .flex.flex-col { }
}
</style>

@endsection