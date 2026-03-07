{{-- resources/views/admin/payments/show.blade.php --}}
@extends('admin.layouts.admin-app')
@section('title', 'Payment Receipt')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="a-fade">
        <a href="{{ route('admin.payments') }}" class="a-btn-secondary text-xs mb-4 inline-flex">← Back</a>
    </div>

    <div class="a-card p-7 a-fade a-d1 print-area">
        <div class="flex items-center justify-between mb-6">
            <div>
                <p class="font-bold text-xl text-gray-800">Payment Receipt</p>
                <p class="text-xs mt-1 text-gray-400">Philippine Advent College</p>
            </div>
            <div class="text-right">
                <p class="text-xs font-bold text-gray-400">OR Number</p>
                <p class="font-bold font-mono-num text-gray-800 text-lg">{{ $payment->or_number ?? 'N/A' }}</p>
                <span class="a-badge {{ $payment->status==='completed'?'a-badge-emerald':($payment->status==='pending'?'a-badge-amber':'a-badge-red') }}">
                    {{ ucfirst($payment->status) }}
                </span>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-6 mb-6">
            <div>
                <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Student</h4>
                <p class="font-semibold text-gray-800">{{ $payment->student->name ?? '—' }} {{ $payment->student->last_name ?? '' }}</p>
                <p class="text-xs mt-1 text-gray-400">ID: {{ $payment->student->student_id ?? '—' }}</p>
                <p class="text-xs text-gray-400">{{ $payment->student->level_group ?? '' }} · {{ $payment->student->year_level ?? '' }}</p>
            </div>
            <div>
                <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Payment Details</h4>
                <dl class="space-y-1.5 text-sm">
                    @foreach([
                        ['Date',   \Carbon\Carbon::parse($payment->payment_date)->format('F d, Y')],
                        ['Method', $payment->payment_method],
                        ['Ref No', $payment->reference_number ?? '—'],
                        ['Period', $payment->school_year.' · Sem '.$payment->semester],
                        ['Cashier',$payment->cashier->name ?? 'System'],
                    ] as [$k,$v])
                    <div class="flex justify-between">
                        <span class="text-gray-400">{{ $k }}</span>
                        <span class="font-semibold text-gray-700">{{ $v }}</span>
                    </div>
                    @endforeach
                </dl>
            </div>
        </div>

        <div class="rounded-2xl px-6 py-5 text-center bg-indigo-50 border border-indigo-100">
            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Amount Paid</p>
            <p class="text-4xl font-bold font-mono-num text-indigo-600">₱{{ number_format($payment->amount,2) }}</p>
        </div>

        @if($payment->notes)
        <div class="mt-5 pt-4 border-t border-gray-100">
            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Notes</p>
            <p class="text-sm text-gray-600">{{ $payment->notes }}</p>
        </div>
        @endif

        <div class="mt-6 flex gap-3 no-print">
            <button onclick="window.print()" class="a-btn-secondary flex-1 justify-center">🖨️ Print</button>
            @if(in_array($payment->status,['completed','pending']))
            <form method="POST" action="{{ route('admin.payments.void', $payment) }}" class="flex-1" onsubmit="return confirm('Void this payment?')">
                @csrf @method('PATCH')
                <button type="submit" class="a-btn-danger w-full py-2.5">Void Payment</button>
            </form>
            @endif
        </div>
    </div>
</div>
@endsection