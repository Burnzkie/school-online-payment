{{-- resources/views/cashier/transactions.blade.php --}}
@extends('cashier.layouts.cashier-app')
@section('title', 'Transactions')

@section('content')
<div class="space-y-6">

    {{-- ── Page Header ── --}}
    <div class="c-fade flex flex-col sm:flex-row sm:items-end justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider mb-3 bg-amber-50 text-amber-600 border border-amber-200">
                🧾 Payment Records
            </div>
            <h1 class="font-display text-4xl sm:text-5xl text-gray-800 leading-tight">Transactions</h1>
            <p class="text-sm mt-1.5 text-gray-400">All payment records and official receipts.</p>
        </div>
        <a href="{{ route('cashier.receive-payment') }}"
           class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl font-bold text-white text-sm transition-all hover:scale-[1.02] shadow-sm"
           style="background: linear-gradient(135deg, #4f46e5, #6366f1);">
            💵 Receive Payment
        </a>
    </div>

    {{-- ── Filters ── --}}
    <form method="GET" action="{{ route('cashier.transactions') }}" class="c-fade c-d1">
        <div class="bg-white rounded-2xl p-4 flex flex-col sm:flex-row gap-3 border border-gray-100 shadow-sm">
            <div class="flex-1 relative">
                <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 pointer-events-none text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" name="q" value="{{ request('q') }}"
                       placeholder="Search by student name, OR#, or reference..."
                       class="c-input pl-10">
            </div>
            <select name="method" class="c-input c-select" style="width:auto; min-width:145px;">
                <option value="">All Methods</option>
                <option value="Cash"          {{ request('method') === 'Cash'          ? 'selected' : '' }}>Cash</option>
                <option value="GCash"         {{ request('method') === 'GCash'         ? 'selected' : '' }}>GCash</option>
                <option value="PayMaya"       {{ request('method') === 'PayMaya'       ? 'selected' : '' }}>PayMaya</option>
                <option value="Bank Transfer" {{ request('method') === 'Bank Transfer' ? 'selected' : '' }}>Bank Transfer</option>
                <option value="Check"         {{ request('method') === 'Check'         ? 'selected' : '' }}>Check</option>
            </select>
            <select name="status" class="c-input c-select" style="width:auto; min-width:130px;">
                <option value="">All Statuses</option>
                <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                <option value="pending"   {{ request('status') === 'pending'   ? 'selected' : '' }}>Pending</option>
                <option value="refunded"  {{ request('status') === 'refunded'  ? 'selected' : '' }}>Refunded</option>
            </select>
            <input type="date" name="date_from" value="{{ request('date_from') }}" class="c-input" style="width:auto; min-width:130px;" title="From date">
            <input type="date" name="date_to"   value="{{ request('date_to') }}"   class="c-input" style="width:auto; min-width:130px;" title="To date">
            <button type="submit"
                    class="px-5 py-2.5 rounded-xl font-bold text-white text-sm whitespace-nowrap shadow-sm hover:opacity-90 transition-all"
                    style="background: linear-gradient(135deg, #4f46e5, #6366f1);">
                Filter
            </button>
        </div>
    </form>

    {{-- ── Summary Strip ── --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 c-fade c-d2">
        <div class="bg-white rounded-xl p-4 border border-gray-100 shadow-sm">
            <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1">Showing</p>
            <p class="font-mono-num font-bold text-gray-800 text-lg">{{ $payments->total() ?? count($payments ?? []) }}</p>
            <p class="text-xs mt-0.5 text-gray-400">transactions</p>
        </div>
        <div class="bg-indigo-50 rounded-xl p-4 border border-indigo-100">
            <p class="text-[10px] font-bold uppercase tracking-widest text-indigo-400 mb-1">Total</p>
            <p class="font-mono-num font-bold text-indigo-700 text-lg">₱{{ number_format($filteredTotal ?? 0, 2) }}</p>
            <p class="text-xs mt-0.5 text-indigo-400">Collected</p>
        </div>
        <div class="bg-white rounded-xl p-4 border border-gray-100 shadow-sm col-span-2">
            <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1">Period</p>
            <p class="text-sm font-semibold text-gray-700">
                @if(request('date_from') || request('date_to'))
                    {{ request('date_from') ? \Carbon\Carbon::parse(request('date_from'))->format('M d, Y') : 'Start' }}
                    — {{ request('date_to') ? \Carbon\Carbon::parse(request('date_to'))->format('M d, Y') : 'Today' }}
                @else
                    All time
                @endif
            </p>
        </div>
    </div>

    {{-- ── Transactions Table ── --}}
    <div class="bg-white rounded-2xl overflow-hidden border border-gray-100 shadow-sm c-fade c-d3">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[750px]">
                <thead>
                    <tr class="border-b border-gray-100 bg-gray-50">
                        <th class="px-5 py-3.5 text-left text-[10px] font-bold uppercase tracking-widest text-gray-400">Date</th>
                        <th class="px-5 py-3.5 text-left text-[10px] font-bold uppercase tracking-widest text-gray-400">Student</th>
                        <th class="px-5 py-3.5 text-left text-[10px] font-bold uppercase tracking-widest text-gray-400">OR #</th>
                        <th class="px-5 py-3.5 text-left text-[10px] font-bold uppercase tracking-widest text-gray-400">Method</th>
                        <th class="px-5 py-3.5 text-right text-[10px] font-bold uppercase tracking-widest text-gray-400">Amount</th>
                        <th class="px-5 py-3.5 text-center text-[10px] font-bold uppercase tracking-widest text-gray-400">Status</th>
                        <th class="px-5 py-3.5 text-right text-[10px] font-bold uppercase tracking-widest text-gray-400">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments ?? [] as $payment)
                    <tr class="c-row border-b border-gray-50">
                        <td class="px-5 py-3.5">
                            <p class="text-sm text-gray-800">{{ \Carbon\Carbon::parse($payment->payment_date)->format('M d, Y') }}</p>
                            <p class="text-xs mt-0.5 text-gray-400">{{ \Carbon\Carbon::parse($payment->created_at)->format('g:i A') }}</p>
                        </td>
                        <td class="px-5 py-3.5">
                            <p class="text-sm font-semibold text-gray-800">{{ $payment->student->name ?? '—' }}</p>
                            <p class="text-xs mt-0.5 text-gray-400">{{ $payment->student->student_id ?? '' }} · {{ $payment->student->level_group ?? '' }}</p>
                        </td>
                        <td class="px-5 py-3.5 font-mono-num text-sm text-gray-500">
                            {{ $payment->or_number ?? '—' }}
                        </td>
                        <td class="px-5 py-3.5">
                            <span class="c-badge c-badge-cyan">{{ $payment->payment_method }}</span>
                        </td>
                        <td class="px-5 py-3.5 text-right font-mono-num font-bold text-sm text-indigo-600">
                            ₱{{ number_format($payment->amount, 2) }}
                        </td>
                        <td class="px-5 py-3.5 text-center">
                            @if($payment->status === 'completed')     <span class="c-badge c-badge-green">Completed</span>
                            @elseif($payment->status === 'pending')   <span class="c-badge c-badge-amber">Pending</span>
                            @elseif($payment->status === 'refunded')  <span class="c-badge c-badge-sky">Refunded</span>
                            @else <span class="c-badge c-badge-red">{{ ucfirst($payment->status) }}</span>
                            @endif
                        </td>
                        <td class="px-5 py-3.5">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('cashier.receipt', $payment->id) }}"
                                   class="text-xs font-semibold px-3 py-1.5 rounded-lg bg-indigo-50 text-indigo-500 border border-indigo-100 hover:bg-indigo-100 transition-colors">
                                    Receipt
                                </a>
                                @if($payment->status === 'pending')
                                <form method="POST" action="{{ route('cashier.complete-payment', $payment->id) }}" class="inline">
                                    @csrf @method('PATCH')
                                    <button type="submit"
                                            class="text-xs font-semibold px-3 py-1.5 rounded-lg bg-amber-50 text-amber-600 border border-amber-100 hover:bg-amber-100 transition-colors">
                                        Confirm
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-5 py-16 text-center">
                            <div class="text-4xl mb-3">🧾</div>
                            <p class="text-sm font-semibold text-gray-400">No transactions found</p>
                            <p class="text-xs mt-1 text-gray-300">Try adjusting your filters.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(method_exists($payments ?? collect(), 'links') && ($payments->hasPages() ?? false))
        <div class="px-5 py-4 bg-gray-50 border-t border-gray-100">
            {{ $payments->withQueryString()->links() }}
        </div>
        @endif
    </div>

</div>
@endsection