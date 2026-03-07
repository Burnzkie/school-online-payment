{{-- resources/views/cashier/receipt.blade.php --}}
@extends('cashier.layouts.cashier-app')
@section('title', 'Receipt #' . ($payment->or_number ?? $payment->id))

@push('styles')
<style>
@media print {
    header, aside, footer, .no-print { display: none !important; }
    body { background: white !important; }
    .receipt-card { box-shadow: none !important; border: 1px solid #e5e7eb !important; background: white !important; }
}
</style>
@endpush

@section('content')
<div class="max-w-2xl mx-auto space-y-5">

    {{-- ── Actions (no-print) ── --}}
    <div class="flex items-center justify-between no-print c-fade">
        <a href="{{ route('cashier.transactions') }}"
           class="inline-flex items-center gap-1.5 text-xs font-semibold text-gray-400 hover:text-indigo-500 transition-colors">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
            </svg>
            Back to Transactions
        </a>
        <div class="flex items-center gap-2">
            @if($payment->status === 'pending')
            <form method="POST" action="{{ route('cashier.complete-payment', $payment->id) }}">
                @csrf @method('PATCH')
                <button type="submit"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-xl font-bold text-white text-sm bg-indigo-600 hover:bg-indigo-700 transition-colors shadow-sm">
                    ✅ Mark Complete
                </button>
            </form>
            @endif
            <button onclick="window.print()"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-xl font-semibold text-sm text-gray-500 bg-white border border-gray-200 hover:bg-gray-50 transition-all">
                🖨 Print Receipt
            </button>
        </div>
    </div>

    {{-- ── Receipt Card ── --}}
    <div class="receipt-card bg-white rounded-3xl overflow-hidden border border-gray-200 shadow-sm c-fade c-d1">

        {{-- Header band --}}
        <div class="px-8 py-7 bg-indigo-50 border-b border-indigo-100">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-3">
                    <div class="w-11 h-11 rounded-2xl flex items-center justify-center text-xl bg-indigo-100 border border-indigo-200">🎓</div>
                    <div>
                        <p class="font-display text-gray-800 text-lg tracking-wide">Philippine Advent College</p>
                        <p class="text-xs mt-0.5 text-gray-400">Official Payment Receipt</p>
                    </div>
                </div>
                <div class="text-right">
                    @if($payment->status === 'completed')
                        <span class="c-badge c-badge-green text-sm px-4 py-1.5">✓ Completed</span>
                    @elseif($payment->status === 'pending')
                        <span class="c-badge c-badge-amber">Pending</span>
                    @else
                        <span class="c-badge c-badge-red">{{ ucfirst($payment->status) }}</span>
                    @endif
                </div>
            </div>

            <div class="flex items-end justify-between">
                <div>
                    <p class="text-xs font-bold uppercase tracking-widest text-gray-400 mb-1">OR Number</p>
                    <p class="font-mono-num text-2xl font-extrabold text-gray-800">{{ $payment->or_number ?? 'OR-' . str_pad($payment->id, 6, '0', STR_PAD_LEFT) }}</p>
                </div>
                <div class="text-right">
                    <p class="text-xs font-bold uppercase tracking-widest text-gray-400 mb-1">Amount Paid</p>
                    <p class="font-mono-num text-4xl font-extrabold text-indigo-700">₱{{ number_format($payment->amount, 2) }}</p>
                </div>
            </div>
        </div>

        {{-- Details --}}
        <div class="px-8 py-6 space-y-5">

            {{-- Student Info --}}
            <div>
                <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-3">Student Information</p>
                <div class="grid grid-cols-2 gap-x-8 gap-y-3">
                    <div>
                        <p class="text-xs text-gray-400">Full Name</p>
                        <p class="text-sm font-semibold text-gray-800 mt-0.5">{{ $payment->student->name ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Student ID</p>
                        <p class="text-sm font-mono-num font-semibold text-gray-800 mt-0.5">{{ $payment->student->student_id ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Level / Year</p>
                        <p class="text-sm font-semibold text-gray-800 mt-0.5">{{ $payment->student->level_group ?? '—' }} · {{ $payment->student->year_level ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">School Year</p>
                        <p class="text-sm font-semibold text-gray-800 mt-0.5">
                            S.Y. {{ $payment->school_year }}
                            @if($payment->semester === 'summer') · Summer
                            @elseif($payment->semester) · {{ $payment->semester == '1' ? '1st' : '2nd' }} Sem
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            <div class="border-t border-gray-100"></div>

            {{-- Payment Info --}}
            <div>
                <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-3">Payment Information</p>
                <div class="grid grid-cols-2 gap-x-8 gap-y-3">
                    <div>
                        <p class="text-xs text-gray-400">Date of Payment</p>
                        <p class="text-sm font-semibold text-gray-800 mt-0.5">{{ \Carbon\Carbon::parse($payment->payment_date)->format('F j, Y') }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Payment Method</p>
                        <p class="text-sm font-semibold text-gray-800 mt-0.5">{{ $payment->payment_method }}</p>
                    </div>
                    @if($payment->reference_number)
                    <div class="col-span-2">
                        <p class="text-xs text-gray-400">Reference Number</p>
                        <p class="text-sm font-mono-num font-semibold text-gray-800 mt-0.5">{{ $payment->reference_number }}</p>
                    </div>
                    @endif
                    <div>
                        <p class="text-xs text-gray-400">Received by</p>
                        <p class="text-sm font-semibold text-gray-800 mt-0.5">{{ $payment->cashier->name ?? auth()->user()->name }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Recorded</p>
                        <p class="text-sm font-semibold text-gray-800 mt-0.5">{{ \Carbon\Carbon::parse($payment->created_at)->format('M d, Y g:i A') }}</p>
                    </div>
                </div>
            </div>

            @if($payment->notes)
            <div class="border-t border-gray-100 pt-5">
                <p class="text-xs text-gray-400">Notes</p>
                <p class="text-sm text-gray-700 mt-0.5">{{ $payment->notes }}</p>
            </div>
            @endif

            {{-- Footer --}}
            <div class="pt-4 flex items-center justify-between border-t border-dashed border-gray-200">
                <p class="text-xs text-gray-300">PAC Finance Office · {{ now()->format('Y') }}</p>
                <p class="text-xs text-gray-300">This is an official receipt.</p>
            </div>
        </div>
    </div>

    {{-- Back to student ledger --}}
    @if(isset($payment->student_id))
    <div class="text-center no-print">
        <a href="{{ route('cashier.student-ledger', $payment->student_id) }}"
           class="text-sm font-semibold text-indigo-500 hover:text-indigo-700">
            ← View full ledger for {{ $payment->student->name ?? 'student' }}
        </a>
    </div>
    @endif
</div>
@endsection