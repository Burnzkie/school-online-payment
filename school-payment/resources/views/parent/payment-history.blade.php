{{-- resources/views/parent/payment-history.blade.php --}}
@extends('parent.layouts.app')

@section('title', 'Payment History')
@section('page-title', 'Payment History')
@section('breadcrumb', $student->name . ' ' . $student->last_name . ' › All Payments')

@section('content')

<div class="flex items-center gap-3 mb-6">
    <a href="{{ route('parent.student.detail', $student) }}"
       class="w-9 h-9 rounded-xl flex items-center justify-center text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition">
        <i class="fas fa-arrow-left text-sm"></i>
    </a>
    <h2 class="text-lg font-bold text-gray-800">{{ $student->name }} {{ $student->last_name }} – All Payments</h2>
</div>

<div class="bg-white border border-gray-100 rounded-2xl shadow-sm overflow-hidden">
    @if($payments->isEmpty())
        <div class="p-12 text-center">
            <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-receipt text-2xl text-gray-300"></i>
            </div>
            <p class="text-gray-400">No payments found.</p>
        </div>
    @else
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr class="text-xs text-gray-400 uppercase tracking-wider">
                    <th class="text-left px-6 py-4 font-bold">Date</th>
                    <th class="text-left px-6 py-4 font-bold">School Year</th>
                    <th class="text-left px-6 py-4 font-bold">Semester</th>
                    <th class="text-left px-6 py-4 font-bold">Method</th>
                    <th class="text-left px-6 py-4 font-bold">OR Number</th>
                    <th class="text-left px-6 py-4 font-bold">Reference</th>
                    <th class="text-right px-6 py-4 font-bold">Amount</th>
                    <th class="text-center px-6 py-4 font-bold">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($payments as $pmt)
                <tr class="hover:bg-indigo-50/30 transition">
                    <td class="px-6 py-4 font-semibold text-gray-800">{{ \Carbon\Carbon::parse($pmt->payment_date)->format('M d, Y') }}</td>
                    <td class="px-6 py-4 text-gray-400">{{ $pmt->school_year }}</td>
                    <td class="px-6 py-4">
                        <span class="text-xs px-2.5 py-1 rounded-full bg-indigo-50 text-indigo-600 border border-indigo-100 font-medium">
                            @if($pmt->semester == '1') 1st Sem
                            @elseif($pmt->semester == '2') 2nd Sem
                            @else Summer
                            @endif
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-2 text-gray-600">
                            @php
                            $icon = match($pmt->payment_method) {
                                'GCash' => 'fa-mobile-alt text-blue-500',
                                'PayMaya','Maya' => 'fa-credit-card text-green-500',
                                'Cash' => 'fa-money-bill text-emerald-500',
                                'Bank Transfer' => 'fa-university text-amber-500',
                                default => 'fa-wallet text-gray-400',
                            };
                            @endphp
                            <i class="fas {{ $icon }}"></i>
                            {{ $pmt->payment_method }}
                        </div>
                    </td>
                    <td class="px-6 py-4 font-mono text-xs text-gray-400">{{ $pmt->or_number ?? '—' }}</td>
                    <td class="px-6 py-4 font-mono text-xs text-gray-300">{{ $pmt->reference_number ?? '—' }}</td>
                    <td class="px-6 py-4 text-right font-bold text-emerald-600 text-base">
                        ₱{{ number_format($pmt->amount, 2) }}
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="text-xs px-2.5 py-1 rounded-full font-semibold
                            {{ $pmt->status === 'completed' ? 'bg-emerald-50 text-emerald-600 border border-emerald-100'
                               : ($pmt->status === 'pending' ? 'bg-amber-50 text-amber-600 border border-amber-100'
                               : 'bg-red-50 text-red-500 border border-red-100') }}">
                            {{ ucfirst($pmt->status) }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($payments->hasPages())
    <div class="px-6 py-4 border-t border-gray-100">
        {{ $payments->links() }}
    </div>
    @endif
    @endif
</div>

@endsection