{{-- resources/views/cashier/student-ledger.blade.php --}}
@extends('cashier.layouts.cashier-app')
@section('title', 'Student Ledger – ' . ($student->name ?? ''))

@push('styles')
<style>
.sl-row { transition: background 0.15s ease; }
.sl-row:hover { background: #f5f3ff !important; }
</style>
@endpush

@section('content')
@php
    $isJHS = str_contains(strtolower($student->level_group ?? ''), 'junior');
    $isSHS = str_contains(strtolower($student->level_group ?? ''), 'senior');
    $sy = date('Y').'-'.(date('Y')+1);
@endphp

<div class="space-y-6 print-area">

    {{-- ── Back + Header ── --}}
    <div class="c-fade flex flex-col sm:flex-row sm:items-start justify-between gap-4">
        <div>
            <a href="{{ route('cashier.students') }}"
               class="inline-flex items-center gap-1.5 text-xs font-semibold mb-3 text-gray-400 hover:text-indigo-500 transition-colors no-print">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                </svg>
                Back to Students
            </a>
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-lg font-bold text-white flex-shrink-0"
                     style="background: linear-gradient(135deg, #4f46e5, #6366f1);">
                    {{ strtoupper(substr($student->name, 0, 1)) }}
                </div>
                <div>
                    <h1 class="font-display text-3xl sm:text-4xl text-gray-800">{{ $student->name }}</h1>
                    <p class="text-sm mt-0.5 text-gray-400">
                        {{ $student->student_id ?? 'No ID' }}
                        &nbsp;·&nbsp; {{ $student->level_group ?? '—' }}
                        &nbsp;·&nbsp; {{ $student->year_level ?? '—' }}
                    </p>
                </div>
            </div>
        </div>
        <div class="flex items-center gap-2 no-print">
            <a href="{{ route('cashier.receive-payment', ['student_id' => $student->id]) }}"
               class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl font-bold text-white text-sm transition-all shadow-sm"
               style="background: linear-gradient(135deg, #4f46e5, #6366f1);">
                💵 Receive Payment
            </a>
            <button onclick="window.print()"
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl font-semibold text-sm text-gray-500 bg-white border border-gray-200 hover:bg-gray-50 transition-all">
                🖨 Print
            </button>
        </div>
    </div>

    {{-- ── Summary Cards ── --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 c-fade c-d1">

        {{-- Total Charges --}}
        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
            <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-2">Total Charges (S.Y. {{ $sy }})</p>
            <p class="font-mono-num text-3xl font-extrabold text-gray-800">₱{{ number_format($totalCharges ?? 0, 2) }}</p>
        </div>

        {{-- Total Paid --}}
        <div class="bg-indigo-50 rounded-2xl p-5 border border-indigo-100">
            <p class="text-[10px] font-bold uppercase tracking-widest text-indigo-400 mb-2">Total Paid</p>
            <p class="font-mono-num text-3xl font-extrabold text-indigo-700">₱{{ number_format($totalPaid ?? 0, 2) }}</p>
        </div>

        {{-- Balance Due --}}
        <div class="rounded-2xl p-5 border {{ ($balance ?? 0) > 0 ? 'bg-red-50 border-red-100' : 'bg-emerald-50 border-emerald-100' }}">
            <p class="text-[10px] font-bold uppercase tracking-widest mb-2 {{ ($balance ?? 0) > 0 ? 'text-red-400' : 'text-emerald-400' }}">Balance Due</p>
            <p class="font-mono-num text-3xl font-extrabold {{ ($balance ?? 0) > 0 ? 'text-red-600' : 'text-emerald-600' }}">
                ₱{{ number_format($balance ?? 0, 2) }}
            </p>
        </div>
    </div>

    {{-- ── Semester / Year filter ── --}}
    <form method="GET" action="{{ route('cashier.student-ledger', $student->id) }}" class="c-fade c-d2 no-print">
        <div class="flex items-center gap-3 flex-wrap">
            <select name="school_year" class="c-input c-select" style="width:auto; min-width:150px;" onchange="this.form.submit()">
                @for($y = date('Y'); $y >= date('Y')-3; $y--)
                @php $opt = $y.'-'.($y+1); @endphp
                <option value="{{ $opt }}" {{ ($selectedYear ?? $sy) === $opt ? 'selected' : '' }}>S.Y. {{ $opt }}</option>
                @endfor
            </select>
            @if($isSHS)
            <select name="semester" class="c-input c-select" style="width:auto; min-width:130px;" onchange="this.form.submit()">
                <option value="1" {{ ($selectedSemester ?? '1') === '1' ? 'selected' : '' }}>1st Semester</option>
                <option value="2" {{ ($selectedSemester ?? '1') === '2' ? 'selected' : '' }}>2nd Semester</option>
            </select>
            @endif
        </div>
    </form>

    {{-- ── Fees Table ── --}}
    <div class="bg-white rounded-2xl overflow-hidden border border-gray-100 shadow-sm c-fade c-d2">
        <div class="px-5 py-4 border-b border-gray-100 bg-gray-50">
            <h2 class="font-bold text-gray-800">Fee Schedule</h2>
            <p class="text-xs mt-0.5 text-gray-400">
                S.Y. {{ $selectedYear ?? $sy }}
                @if($isSHS) · {{ ($selectedSemester ?? '1') === '1' ? '1st' : '2nd' }} Semester @endif
            </p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full min-w-[560px]">
                <thead>
                    <tr class="border-b border-gray-100">
                        <th class="px-5 py-3 text-left text-[10px] font-bold uppercase tracking-widest text-gray-400">Fee Description</th>
                        <th class="px-5 py-3 text-right text-[10px] font-bold uppercase tracking-widest text-gray-400">Amount</th>
                        <th class="px-5 py-3 text-right text-[10px] font-bold uppercase tracking-widest text-gray-400">Paid</th>
                        <th class="px-5 py-3 text-right text-[10px] font-bold uppercase tracking-widest text-gray-400">Balance</th>
                        <th class="px-5 py-3 text-right text-[10px] font-bold uppercase tracking-widest text-gray-400">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($fees ?? [] as $fee)
                    @php
                        $feePaid = $fee->studentFee->amount_paid ?? 0;
                        $feeBal  = max(0, $fee->amount - $feePaid);
                    @endphp
                    <tr class="sl-row border-b border-gray-50">
                        <td class="px-5 py-4">
                            <p class="text-sm font-medium text-gray-800">{{ $fee->fee_name }}</p>
                            @if($fee->fee_category)
                            <p class="text-xs text-gray-400 mt-0.5">{{ $fee->fee_category }}</p>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-right font-mono-num text-sm text-gray-700">₱{{ number_format($fee->amount, 2) }}</td>
                        <td class="px-5 py-4 text-right font-mono-num text-sm text-indigo-500">₱{{ number_format($feePaid, 2) }}</td>
                        <td class="px-5 py-4 text-right font-mono-num font-bold text-sm {{ $feeBal > 0 ? 'text-red-500' : 'text-emerald-500' }}">
                            ₱{{ number_format($feeBal, 2) }}
                        </td>
                        <td class="px-5 py-4 text-right">
                            @if($fee->status === 'waived')
                                <span class="c-badge c-badge-sky">Waived</span>
                            @elseif($feeBal <= 0)
                                <span class="c-badge c-badge-green">Paid</span>
                            @else
                                <span class="c-badge c-badge-amber">Unpaid</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-5 py-14 text-center">
                            <div class="text-3xl mb-3">📋</div>
                            <p class="text-sm text-gray-400">No fees assigned for this period.</p>
                        </td>
                    </tr>
                    @endforelse

                    {{-- Totals row --}}
                    @if(count($fees ?? []) > 0)
                    <tr class="bg-gray-50 border-t border-gray-100">
                        <td class="px-5 py-4 font-bold text-gray-500 text-sm uppercase tracking-wide">Total</td>
                        <td class="px-5 py-4 text-right font-mono-num font-bold text-sm text-gray-800">₱{{ number_format($totalCharges ?? 0, 2) }}</td>
                        <td class="px-5 py-4 text-right font-mono-num font-bold text-sm text-indigo-500">₱{{ number_format($totalPaid ?? 0, 2) }}</td>
                        <td class="px-5 py-4 text-right font-mono-num font-bold text-sm {{ ($balance ?? 0) > 0 ? 'text-red-500' : 'text-emerald-500' }}">₱{{ number_format($balance ?? 0, 2) }}</td>
                        <td></td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>

    {{-- ── Payment History ── --}}
    <div class="bg-white rounded-2xl overflow-hidden border border-gray-100 shadow-sm c-fade c-d3">
        <div class="px-5 py-4 border-b border-gray-100 bg-gray-50">
            <h2 class="font-bold text-gray-800">Payment History</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full min-w-[560px]">
                <thead>
                    <tr class="border-b border-gray-100">
                        <th class="px-5 py-3 text-left text-[10px] font-bold uppercase tracking-widest text-gray-400">Date</th>
                        <th class="px-5 py-3 text-left text-[10px] font-bold uppercase tracking-widest text-gray-400">OR #</th>
                        <th class="px-5 py-3 text-left text-[10px] font-bold uppercase tracking-widest text-gray-400">Method</th>
                        <th class="px-5 py-3 text-right text-[10px] font-bold uppercase tracking-widest text-gray-400">Amount</th>
                        <th class="px-5 py-3 text-right text-[10px] font-bold uppercase tracking-widest text-gray-400">Status</th>
                        <th class="px-5 py-3 text-right text-[10px] font-bold uppercase tracking-widest text-gray-400">Receipt</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments ?? [] as $payment)
                    <tr class="sl-row border-b border-gray-50">
                        <td class="px-5 py-3.5 text-sm text-gray-700">{{ \Carbon\Carbon::parse($payment->payment_date)->format('M d, Y') }}</td>
                        <td class="px-5 py-3.5 font-mono-num text-sm text-gray-400">{{ $payment->or_number ?? '—' }}</td>
                        <td class="px-5 py-3.5"><span class="c-badge c-badge-cyan">{{ $payment->payment_method }}</span></td>
                        <td class="px-5 py-3.5 text-right font-mono-num font-bold text-sm text-indigo-500">₱{{ number_format($payment->amount, 2) }}</td>
                        <td class="px-5 py-3.5 text-right">
                            @if($payment->status === 'completed') <span class="c-badge c-badge-green">Completed</span>
                            @elseif($payment->status === 'pending') <span class="c-badge c-badge-amber">Pending</span>
                            @else <span class="c-badge c-badge-red">{{ ucfirst($payment->status) }}</span>
                            @endif
                        </td>
                        <td class="px-5 py-3.5 text-right">
                            <a href="{{ route('cashier.receipt', $payment->id) }}"
                               class="text-xs font-semibold px-3 py-1.5 rounded-lg no-print bg-indigo-50 text-indigo-500 border border-indigo-100 hover:bg-indigo-100 transition-colors">
                                Receipt
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-5 py-12 text-center">
                            <p class="text-sm text-gray-400">No payments recorded for this period.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ── Installment Plan (if any) ── --}}
    @if(isset($installmentPlan))
    <div class="bg-white rounded-2xl p-5 border border-indigo-100 c-fade c-d4">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-9 h-9 rounded-xl flex items-center justify-center bg-indigo-50 border border-indigo-100">📅</div>
            <div>
                <p class="font-bold text-gray-800">Active Installment Plan — {{ $installmentPlan->total_installments }}-Part</p>
                <p class="text-xs mt-0.5 text-gray-400">
                    ₱{{ number_format($installmentPlan->amount_per_installment, 2) }} per installment
                </p>
            </div>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            @foreach($installmentPlan->schedules ?? [] as $sched)
            <div class="rounded-xl p-3 border
                {{ $sched->is_paid    ? 'bg-emerald-50 border-emerald-100'
                 : ($sched->is_overdue ? 'bg-red-50 border-red-100'
                 : 'bg-gray-50 border-gray-100') }}">
                <p class="text-[10px] font-bold uppercase tracking-wide text-gray-400 mb-1">
                    Installment {{ $sched->installment_number }}
                </p>
                <p class="font-mono-num font-bold text-gray-800 text-sm">₱{{ number_format($sched->amount_due, 2) }}</p>
                <p class="text-[10px] mt-1 text-gray-400">Due: {{ \Carbon\Carbon::parse($sched->due_date)->format('M d') }}</p>
                <div class="mt-2">
                    @if($sched->is_paid)       <span class="c-badge c-badge-green text-[9px]">Paid</span>
                    @elseif($sched->is_overdue) <span class="c-badge c-badge-red text-[9px]">Overdue</span>
                    @else                       <span class="c-badge c-badge-amber text-[9px]">Upcoming</span>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

</div>
@endsection