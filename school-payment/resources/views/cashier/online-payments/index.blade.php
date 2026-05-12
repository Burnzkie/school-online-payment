{{-- resources/views/cashier/online-payments/index.blade.php --}}
@extends('cashier.layouts.cashier-app')
@section('title', 'Online Payment Submissions')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="c-fade flex flex-col sm:flex-row sm:items-end justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider mb-3 bg-indigo-50 text-indigo-500 border border-indigo-100">
                📱 Online Submissions
            </div>
            <h1 class="font-display text-4xl sm:text-5xl text-gray-800 leading-tight">Online Payments</h1>
            <p class="text-sm mt-1.5 text-gray-400">Review and approve GCash / Maya / Bank Transfer submissions from students.</p>
        </div>
    </div>

    {{-- Status Tabs --}}
    <div class="flex gap-2 flex-wrap c-fade c-d1">
        @foreach(['pending' => '⏳ Pending', 'verified' => '✅ Approved', 'rejected' => '❌ Rejected', 'all' => 'All'] as $status => $label)
        <a href="{{ route('cashier.online-payments', ['status' => $status]) }}"
           class="px-4 py-2 rounded-xl text-sm font-semibold transition-all border
                  {{ $currentStatus === $status
                     ? 'bg-indigo-600 text-white border-indigo-600 shadow-sm'
                     : 'bg-white text-gray-500 border-gray-200 hover:border-indigo-300' }}">
            {{ $label }}
            @if($status !== 'all')
                <span class="ml-1 px-1.5 py-0.5 rounded-full text-xs
                      {{ $currentStatus === $status ? 'bg-white/20 text-white' : 'bg-gray-100 text-gray-500' }}">
                    {{ $counts[$status] ?? 0 }}
                </span>
            @endif
        </a>
        @endforeach
    </div>

    {{-- Submissions Table --}}
    <div class="bg-white rounded-2xl overflow-hidden border border-gray-100 shadow-sm c-fade c-d2">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[750px]">
                <thead>
                    <tr class="border-b border-gray-100 bg-gray-50">
                        <th class="px-5 py-3.5 text-left text-[10px] font-bold uppercase tracking-widest text-gray-400">Student</th>
                        <th class="px-5 py-3.5 text-left text-[10px] font-bold uppercase tracking-widest text-gray-400">Method</th>
                        <th class="px-5 py-3.5 text-left text-[10px] font-bold uppercase tracking-widest text-gray-400">Reference</th>
                        <th class="px-5 py-3.5 text-left text-[10px] font-bold uppercase tracking-widest text-gray-400">Period</th>
                        <th class="px-5 py-3.5 text-right text-[10px] font-bold uppercase tracking-widest text-gray-400">Amount</th>
                        <th class="px-5 py-3.5 text-center text-[10px] font-bold uppercase tracking-widest text-gray-400">Status</th>
                        <th class="px-5 py-3.5 text-center text-[10px] font-bold uppercase tracking-widest text-gray-400">Submitted</th>
                        <th class="px-5 py-3.5 text-right text-[10px] font-bold uppercase tracking-widest text-gray-400">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($submissions as $sub)
                    <tr class="c-row border-b border-gray-50">
                        <td class="px-5 py-3.5">
                            <p class="text-sm font-semibold text-gray-800">{{ $sub->student->name ?? '—' }}</p>
                            <p class="text-xs mt-0.5 text-gray-400">{{ $sub->student->student_id ?? '' }} · {{ $sub->student->level_group ?? '' }}</p>
                        </td>
                        <td class="px-5 py-3.5">
                            <span class="c-badge c-badge-cyan">{{ $sub->payment_method }}</span>
                        </td>
                        <td class="px-5 py-3.5 font-mono text-xs text-gray-600">{{ $sub->reference_number }}</td>
                        <td class="px-5 py-3.5 text-xs text-gray-500">
                            {{ $sub->school_year }}<br>
                            {{ match($sub->semester) { '1' => '1st Sem', '2' => '2nd Sem', 'summer' => 'Summer', default => $sub->semester } }}
                        </td>
                        <td class="px-5 py-3.5 text-right font-mono-num font-bold text-indigo-600">
                            ₱{{ number_format($sub->amount, 2) }}
                        </td>
                        <td class="px-5 py-3.5 text-center">
                            @if($sub->status === 'pending')   <span class="c-badge c-badge-amber">Pending</span>
                            @elseif($sub->status === 'verified') <span class="c-badge c-badge-green">Approved</span>
                            @else <span class="c-badge c-badge-red">Rejected</span>
                            @endif
                        </td>
                        <td class="px-5 py-3.5 text-center text-xs text-gray-400">
                            {{ $sub->created_at->format('M d, Y') }}<br>
                            {{ $sub->created_at->format('g:i A') }}
                        </td>
                        <td class="px-5 py-3.5 text-right">
                            <a href="{{ route('cashier.online-payments.show', $sub) }}"
                               class="text-xs font-semibold px-3 py-1.5 rounded-lg bg-indigo-50 text-indigo-500 border border-indigo-100 hover:bg-indigo-100 transition-colors">
                                {{ $sub->isPending() ? 'Review' : 'View' }}
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-5 py-16 text-center">
                            <div class="text-4xl mb-3">📱</div>
                            <p class="text-sm font-semibold text-gray-400">No {{ $currentStatus === 'all' ? '' : $currentStatus }} submissions found</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($submissions->hasPages())
        <div class="px-5 py-4 bg-gray-50 border-t border-gray-100">
            {{ $submissions->withQueryString()->links() }}
        </div>
        @endif
    </div>
</div>
@endsection