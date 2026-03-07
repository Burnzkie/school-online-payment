{{-- resources/views/cashier/dashboard.blade.php --}}
@extends('cashier.layouts.cashier-app')
@section('title', 'Dashboard')

@push('styles')
<style>
.cd-shimmer { position: relative; overflow: hidden; }
.cd-shimmer::after {
    content: ''; position: absolute; top: 0; left: -100%; width: 50%; height: 100%;
    background: linear-gradient(105deg, transparent 40%, rgba(255,255,255,.4) 50%, transparent 60%);
    animation: cd-shim 3.5s ease-in-out infinite;
}
@keyframes cd-shim { 0% { left: -100%; } 50%, 100% { left: 160%; } }
</style>
@endpush

@section('content')
<div class="space-y-6">

    {{-- ── Page Header ── --}}
    <div class="c-fade">
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider mb-3 bg-emerald-50 text-emerald-600 border border-emerald-200">
            <span class="w-1.5 h-1.5 rounded-full c-pulse bg-emerald-500"></span>
            Cashier Portal
        </div>
        <h1 class="font-display text-4xl sm:text-5xl text-gray-800 leading-tight">Dashboard</h1>
        <p class="text-sm mt-1.5 text-gray-400">
            {{ now()->format('l, F j, Y') }} &nbsp;·&nbsp; Welcome back, {{ explode(' ', auth()->user()->name)[0] }}
        </p>
    </div>

    {{-- ── Stats Cards ── --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 c-fade c-d1">
        <div class="cd-shimmer bg-emerald-50 border border-emerald-100 rounded-2xl p-5 c-card-lift col-span-2 sm:col-span-1">
            <p class="text-[10px] font-bold uppercase tracking-widest text-emerald-500 mb-2">Today's Collections</p>
            <p class="font-mono-num text-3xl font-extrabold text-emerald-700">₱{{ number_format($todayCollections ?? 0, 2) }}</p>
            <p class="text-xs mt-2 text-emerald-500">{{ $todayPaymentCount ?? 0 }} transaction(s)</p>
        </div>
        <div class="cd-shimmer bg-indigo-50 border border-indigo-100 rounded-2xl p-5 c-card-lift">
            <p class="text-[10px] font-bold uppercase tracking-widest text-indigo-400 mb-2">This Month</p>
            <p class="font-mono-num text-3xl font-extrabold text-indigo-700">₱{{ number_format($monthCollections ?? 0, 2) }}</p>
            <p class="text-xs mt-2 text-indigo-400">{{ now()->format('F Y') }}</p>
        </div>
        <div class="cd-shimmer bg-violet-50 border border-violet-100 rounded-2xl p-5 c-card-lift">
            <p class="text-[10px] font-bold uppercase tracking-widest text-violet-400 mb-2">Total Students</p>
            <p class="font-mono-num text-3xl font-extrabold text-violet-700">{{ number_format($totalStudents ?? 0) }}</p>
            <p class="text-xs mt-2 text-violet-400">All levels enrolled</p>
        </div>
        <div class="cd-shimmer bg-red-50 border border-red-100 rounded-2xl p-5 c-card-lift">
            <p class="text-[10px] font-bold uppercase tracking-widest text-red-400 mb-2">Unpaid Balance</p>
            <p class="font-mono-num text-3xl font-extrabold text-red-600">{{ number_format($studentsWithBalance ?? 0) }}</p>
            <p class="text-xs mt-2 text-red-400">Students with outstanding fees</p>
        </div>
    </div>

    {{-- ── Quick Actions ── --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 c-fade c-d2">
        <a href="{{ route('cashier.receive-payment') }}"
           class="group bg-white border border-gray-100 shadow-sm rounded-2xl p-5 flex items-center gap-4 transition-all hover:shadow-md hover:border-emerald-200 hover:scale-[1.02]">
            <div class="w-12 h-12 rounded-2xl flex items-center justify-center flex-shrink-0 text-2xl bg-emerald-100">💵</div>
            <div>
                <p class="font-bold text-gray-800 text-base">Receive Payment</p>
                <p class="text-xs mt-0.5 text-gray-400">Record a student payment</p>
            </div>
        </a>
        <a href="{{ route('cashier.students') }}"
           class="group bg-white border border-gray-100 shadow-sm rounded-2xl p-5 flex items-center gap-4 transition-all hover:shadow-md hover:border-indigo-200 hover:scale-[1.02]">
            <div class="w-12 h-12 rounded-2xl flex items-center justify-center flex-shrink-0 text-2xl bg-indigo-100">👥</div>
            <div>
                <p class="font-bold text-gray-800 text-base">Student Lookup</p>
                <p class="text-xs mt-0.5 text-gray-400">Search & view student fees</p>
            </div>
        </a>
        <a href="{{ route('cashier.transactions') }}"
           class="group bg-white border border-gray-100 shadow-sm rounded-2xl p-5 flex items-center gap-4 transition-all hover:shadow-md hover:border-amber-200 hover:scale-[1.02]">
            <div class="w-12 h-12 rounded-2xl flex items-center justify-center flex-shrink-0 text-2xl bg-amber-100">🧾</div>
            <div>
                <p class="font-bold text-gray-800 text-base">Transactions</p>
                <p class="text-xs mt-0.5 text-gray-400">Payment history & receipts</p>
            </div>
        </a>
    </div>

    {{-- ── Recent Transactions ── --}}
    <div class="bg-white rounded-2xl overflow-hidden border border-gray-100 shadow-sm c-fade c-d3">
        <div class="px-5 py-4 flex items-center justify-between border-b border-gray-100 bg-gray-50">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center bg-emerald-100">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
                <h2 class="font-bold text-gray-800">Recent Transactions</h2>
            </div>
            <a href="{{ route('cashier.transactions') }}" class="text-xs font-semibold text-indigo-500 hover:text-indigo-700">View All →</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full min-w-[580px]">
                <thead>
                    <tr class="border-b border-gray-100 bg-gray-50">
                        <th class="px-5 py-3 text-left text-[10px] font-bold uppercase tracking-widest text-gray-400">Student</th>
                        <th class="px-5 py-3 text-left text-[10px] font-bold uppercase tracking-widest text-gray-400">Amount</th>
                        <th class="px-5 py-3 text-left text-[10px] font-bold uppercase tracking-widest text-gray-400">Method</th>
                        <th class="px-5 py-3 text-left text-[10px] font-bold uppercase tracking-widest text-gray-400">Date</th>
                        <th class="px-5 py-3 text-left text-[10px] font-bold uppercase tracking-widest text-gray-400">Status</th>
                        <th class="px-5 py-3 text-right text-[10px] font-bold uppercase tracking-widest text-gray-400">Receipt</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentPayments ?? [] as $payment)
                    <tr class="c-row border-b border-gray-50">
                        <td class="px-5 py-3.5">
                            <p class="text-sm font-semibold text-gray-800">{{ $payment->student->name ?? '—' }}</p>
                            <p class="text-xs mt-0.5 text-gray-400">{{ $payment->student->student_id ?? '' }}</p>
                        </td>
                        <td class="px-5 py-3.5 font-mono-num font-bold text-sm text-emerald-600">
                            ₱{{ number_format($payment->amount, 2) }}
                        </td>
                        <td class="px-5 py-3.5">
                            <span class="c-badge c-badge-cyan">{{ $payment->payment_method }}</span>
                        </td>
                        <td class="px-5 py-3.5 text-sm text-gray-500">
                            {{ \Carbon\Carbon::parse($payment->payment_date)->format('M d, Y') }}
                        </td>
                        <td class="px-5 py-3.5">
                            @if($payment->status === 'completed') <span class="c-badge c-badge-green">Completed</span>
                            @elseif($payment->status === 'pending') <span class="c-badge c-badge-amber">Pending</span>
                            @else <span class="c-badge c-badge-red">{{ ucfirst($payment->status) }}</span>
                            @endif
                        </td>
                        <td class="px-5 py-3.5 text-right">
                            <a href="{{ route('cashier.receipt', $payment->id) }}"
                               class="text-xs font-semibold px-3 py-1.5 rounded-lg bg-indigo-50 text-indigo-500 border border-indigo-100 hover:bg-indigo-100 transition-colors">
                                View
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-5 py-14 text-center">
                            <div class="text-3xl mb-3">💳</div>
                            <p class="text-sm font-semibold text-gray-400">No transactions today</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection