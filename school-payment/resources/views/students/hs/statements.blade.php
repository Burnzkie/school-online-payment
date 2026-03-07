{{-- resources/views/students/hs/statements.blade.php --}}
@extends('students.hs.layouts.hs-app')
@section('title', 'Statements')

@push('styles')
<style>
.hs-stmt-row { transition: background .15s ease, transform .15s ease; }
.hs-stmt-row:hover { background: #eef2ff !important; transform: translateX(3px); }

.hs-method-badge {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 3px 10px; border-radius: 999px;
    font-size: 11px; font-weight: 700;
}
.method-cash    { background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; }
.method-gcash   { background: #cffafe; color: #155e75; border: 1px solid #a5f3fc; }
.method-bank    { background: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd; }
.method-default { background: #f3f4f6; color: #6b7280; border: 1px solid #e5e7eb; }

@keyframes hs-stmt-fade {
    from { opacity: 0; transform: translateY(8px); }
    to { opacity: 1; transform: translateY(0); }
}
.hs-stmt-fade { animation: hs-stmt-fade .4s ease both; }
.hs-stmt-d1 { animation-delay: .06s; }
.hs-stmt-d2 { animation-delay: .12s; }

@media print {
    body * { visibility: hidden; }
    main, main * { visibility: visible; }
    main { position: absolute; left: 0; top: 0; width: 100%; background: white !important; }
    .no-print { display: none !important; }
}
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
    <div class="hs-stmt-fade flex flex-col sm:flex-row sm:items-end justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider mb-3"
                 style="background: #eef2ff; color: #4f46e5; border: 1px solid #c7d2fe;">
                <span class="w-1.5 h-1.5 rounded-full" style="background: {{ $accentColor }};"></span>
                {{ $levelLabel }} · Statements
            </div>
            <h1 class="font-display text-4xl sm:text-5xl text-gray-800 leading-tight">Statements</h1>
            <p class="text-sm mt-1.5 text-gray-500">Your complete payment history and official receipts.</p>
        </div>
        <div class="flex items-center gap-3 no-print">
            <button onclick="window.print()"
                    class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-semibold rounded-xl transition-colors"
                    style="background: #ffffff; border: 1px solid #e5e7eb; color: #6b7280;">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                Print Statement
            </button>
        </div>
    </div>

    {{-- ── Summary Cards ── --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 hs-stmt-fade hs-stmt-d1">
        @php
            $totalPaid = collect($payments ?? [])->sum('amount');
            $paymentCount = count($payments ?? []);
            $latestDate = !empty($payments) ? collect($payments)->max('payment_date') : null;
        @endphp
        <div class="rounded-2xl p-4 bg-white border border-gray-100 shadow-sm">
            <p class="text-xs font-bold uppercase tracking-wide mb-1 text-emerald-600">Total Paid</p>
            <p class="font-mono-num font-bold text-xl text-gray-800">₱{{ number_format($totalPaid, 2) }}</p>
        </div>
        <div class="rounded-2xl p-4 bg-white border border-gray-100 shadow-sm">
            <p class="text-xs font-bold uppercase tracking-wide mb-1 text-indigo-600">Transactions</p>
            <p class="font-mono-num font-bold text-xl text-gray-800">{{ $paymentCount }}</p>
        </div>
        <div class="rounded-2xl p-4 bg-white border border-gray-100 shadow-sm">
            <p class="text-xs font-bold uppercase tracking-wide mb-1 text-cyan-600">Period</p>
            <p class="font-bold text-gray-800 text-sm">{{ $isJHS ? 'Annual' : (($selectedSemester ?? '1') == '1' ? '1st Sem' : '2nd Sem') }}</p>
        </div>
        <div class="rounded-2xl p-4 bg-white border border-gray-100 shadow-sm">
            <p class="text-xs font-bold uppercase tracking-wide mb-1 text-amber-600">Last Payment</p>
            <p class="text-gray-800 font-bold text-sm">{{ $latestDate ? \Carbon\Carbon::parse($latestDate)->format('M d') : '—' }}</p>
        </div>
    </div>

    {{-- ── Filters ── --}}
    <div class="hs-stmt-fade rounded-2xl p-4"
         style="background: #ffffff; border: 1px solid #e5e7eb;">
        <form method="GET" action="{{ route('hs.statements') }}" class="flex flex-wrap gap-3 items-center">
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-wide mb-1.5 text-gray-400">School Year</label>
                <select name="school_year" onchange="this.form.submit()"
                        class="text-sm font-semibold rounded-xl px-3.5 py-2"
                        style="background: #f9fafb; border: 1px solid #e5e7eb; color: #1f2937; appearance: none;">
                    @foreach($availableYears ?? ['2025-2026', '2024-2025'] as $yr)
                    <option value="{{ $yr }}" {{ ($selectedYear ?? '') == $yr ? 'selected' : '' }} style="background: #ffffff;">{{ $yr }}</option>
                    @endforeach
                </select>
            </div>
            @if($isSHS)
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-wide mb-1.5 text-gray-400">Semester</label>
                <select name="semester" onchange="this.form.submit()"
                        class="text-sm font-semibold rounded-xl px-3.5 py-2"
                        style="background: #f9fafb; border: 1px solid #e5e7eb; color: #1f2937; appearance: none;">
                    <option value="all" {{ ($selectedSemester ?? 'all') == 'all' ? 'selected' : '' }} style="background: #ffffff;">All Semesters</option>
                    <option value="1" {{ ($selectedSemester ?? '') == '1' ? 'selected' : '' }} style="background: #ffffff;">1st Semester</option>
                    <option value="2" {{ ($selectedSemester ?? '') == '2' ? 'selected' : '' }} style="background: #ffffff;">2nd Semester</option>
                </select>
            </div>
            @endif
        </form>
    </div>

    {{-- ── Payments Table ── --}}
    <div class="rounded-2xl overflow-hidden shadow-lg hs-stmt-fade hs-stmt-d2"
         style="border: 1px solid rgba(14,165,233,0.2);">
        <div class="px-6 py-4"
             style="background: #f8faff; border-bottom: 1px solid #e0e7ff;">
            <h2 class="font-bold text-gray-800 text-base">Payment Transactions</h2>
            <p class="text-xs mt-0.5 text-gray-400">
                {{ $selectedYear ?? date('Y').'-'.(date('Y')+1) }}
                @if($isSHS && ($selectedSemester ?? 'all') != 'all')
                    · {{ $selectedSemester == '1' ? '1st' : '2nd' }} Semester
                @elseif($isJHS)
                    · Full Year
                @endif
            </p>
        </div>

        <div class="overflow-x-auto" style="background: #ffffff;">
            <table class="w-full min-w-[600px]">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-6 py-3.5 text-left text-[10px] font-bold uppercase tracking-widest text-gray-400">Date</th>
                        <th class="px-6 py-3.5 text-left text-[10px] font-bold uppercase tracking-widest text-gray-400">OR #</th>
                        @if($isSHS)
                        <th class="px-6 py-3.5 text-left text-[10px] font-bold uppercase tracking-widest text-gray-400">Semester</th>
                        @endif
                        <th class="px-6 py-3.5 text-left text-[10px] font-bold uppercase tracking-widest text-gray-400">Method</th>
                        <th class="px-6 py-3.5 text-right text-[10px] font-bold uppercase tracking-widest text-gray-400">Amount</th>
                        <th class="px-6 py-3.5 text-center text-[10px] font-bold uppercase tracking-widest text-gray-400">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments ?? [] as $payment)
                    @php
                        $method = strtolower($payment->payment_method ?? '');
                        $methodClass = str_contains($method, 'cash') ? 'method-cash' : (str_contains($method, 'gcash') ? 'method-gcash' : (str_contains($method, 'bank') ? 'method-bank' : 'method-default'));
                    @endphp
                    <tr class="hs-stmt-row border-b border-gray-50 {{ $loop->even ? 'bg-gray-50/50' : 'bg-white' }}">
                        <td class="px-6 py-4">
                            <p class="text-sm font-semibold text-gray-800">{{ \Carbon\Carbon::parse($payment->payment_date)->format('M d, Y') }}</p>
                            <p class="text-xs mt-0.5 text-gray-400">{{ \Carbon\Carbon::parse($payment->payment_date)->format('l') }}</p>
                        </td>
                        <td class="px-6 py-4">
                            <span class="font-mono-num text-sm font-semibold text-indigo-600">{{ $payment->or_number ?? '—' }}</span>
                        </td>
                        @if($isSHS)
                        <td class="px-6 py-4">
                            <span class="hs-badge {{ $payment->semester == '1' ? 'hs-badge-sky' : 'hs-badge-cyan' }}">
                                {{ $payment->semester == '1' ? '1st' : '2nd' }} Sem
                            </span>
                        </td>
                        @endif
                        <td class="px-6 py-4">
                            <span class="hs-method-badge {{ $methodClass }}">{{ $payment->payment_method ?? '—' }}</span>
                        </td>
                        <td class="px-6 py-4 text-right font-mono-num font-bold text-lg text-emerald-600">
                            +₱{{ number_format($payment->amount, 2) }}
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($payment->status === 'completed')
                                <span class="hs-badge hs-badge-green">✓ Completed</span>
                            @elseif($payment->status === 'pending')
                                <span class="hs-badge hs-badge-amber">Pending</span>
                            @else
                                <span class="hs-badge hs-badge-red">{{ ucfirst($payment->status) }}</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ $isSHS ? '6' : '5' }}" class="px-6 py-16 text-center">
                            <div class="text-5xl mb-3">📭</div>
                            <p class="text-sm font-semibold text-gray-400">No payment records found</p>
                            <p class="text-xs mt-1 text-gray-300">Transactions will appear here once payments are processed.</p>
                        </td>
                    </tr>
                    @endforelse

                    @if(!empty($payments))
                    <tr class="bg-gray-100 border-t-2 border-gray-200">
                        <td class="px-6 py-4 font-bold text-gray-800 text-sm" colspan="{{ $isSHS ? '3' : '2' }}">Total Payments</td>
                        <td class="px-6 py-4"></td>
                        <td class="px-6 py-4 text-right font-mono-num font-extrabold text-xl text-emerald-600">
                            ₱{{ number_format(collect($payments)->sum('amount'), 2) }}
                        </td>
                        <td></td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection