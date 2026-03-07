{{-- resources/views/treasurer/payments/index.blade.php --}}
@extends('treasurer.layouts.treasurer-app')
@section('title', 'Payments')
@section('content')
<div class="space-y-6">

    <div class="fade-up">
        <h1 class="text-2xl font-bold text-gray-800">Payment Transactions</h1>
        <p class="text-sm mt-1 text-gray-400">Read-only view of all payments processed by cashiers</p>
    </div>

    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 fade-up fade-up-d1">
        <div class="stat-card">
            <p class="text-xs uppercase tracking-wider font-bold mb-2 text-gray-400">Total Collected</p>
            <p class="text-lg font-bold text-gray-800 font-mono-num">₱{{ number_format($totals['all'], 2) }}</p>
        </div>
        <div class="stat-card">
            <p class="text-xs uppercase tracking-wider font-bold mb-2 text-gray-400">This Month</p>
            <p class="text-lg font-bold font-mono-num text-emerald-600">₱{{ number_format($totals['month'], 2) }}</p>
        </div>
        <div class="stat-card">
            <p class="text-xs uppercase tracking-wider font-bold mb-2 text-gray-400">Pending</p>
            <p class="text-lg font-bold font-mono-num text-amber-600">₱{{ number_format($totals['pending'], 2) }}</p>
        </div>
        <div class="stat-card">
            <p class="text-xs uppercase tracking-wider font-bold mb-2 text-gray-400">Refunded</p>
            <p class="text-lg font-bold font-mono-num text-red-500">₱{{ number_format($totals['refunded'], 2) }}</p>
        </div>
    </div>

    <div class="section-card p-4 fade-up fade-up-d2">
        <form method="GET" class="flex flex-wrap gap-3 items-end">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="OR#, Reference, Student name…" class="form-input flex-1 min-w-[180px]">
            <select name="school_year" class="form-input w-auto">
                <option value="">All Years</option>
                @foreach($schoolYears as $sy)
                    <option value="{{ $sy }}" {{ request('school_year')==$sy?'selected':'' }}>{{ $sy }}</option>
                @endforeach
            </select>
            <select name="semester" class="form-input w-auto">
                <option value="">All Semesters</option>
                <option value="1"      {{ request('semester')=='1'?'selected':'' }}>1st</option>
                <option value="2"      {{ request('semester')=='2'?'selected':'' }}>2nd</option>
                <option value="summer" {{ request('semester')=='summer'?'selected':'' }}>Summer</option>
            </select>
            <select name="status" class="form-input w-auto">
                <option value="">All Status</option>
                <option value="completed" {{ request('status')=='completed'?'selected':'' }}>Completed</option>
                <option value="pending"   {{ request('status')=='pending'?'selected':'' }}>Pending</option>
                <option value="failed"    {{ request('status')=='failed'?'selected':'' }}>Failed</option>
                <option value="refunded"  {{ request('status')=='refunded'?'selected':'' }}>Refunded</option>
            </select>
            <select name="method" class="form-input w-auto">
                <option value="">All Methods</option>
                @foreach($methods as $m)
                    <option value="{{ $m }}" {{ request('method')==$m?'selected':'' }}>{{ $m }}</option>
                @endforeach
            </select>
            <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-input w-auto">
            <input type="date" name="date_to"   value="{{ request('date_to') }}"   class="form-input w-auto">
            <button type="submit" class="btn-primary">🔍 Filter</button>
            <a href="{{ route('treasurer.payments') }}" class="btn-secondary">Clear</a>
        </form>
    </div>

    <div class="section-card fade-up fade-up-d3">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100 bg-gray-50">
                        <th class="text-left px-5 py-4 text-xs font-bold uppercase tracking-wider text-gray-400">Student</th>
                        <th class="text-left px-4 py-4 text-xs font-bold uppercase tracking-wider text-gray-400">OR / Ref</th>
                        <th class="text-left px-4 py-4 text-xs font-bold uppercase tracking-wider text-gray-400">Date</th>
                        <th class="text-left px-4 py-4 text-xs font-bold uppercase tracking-wider text-gray-400">Method</th>
                        <th class="text-right px-4 py-4 text-xs font-bold uppercase tracking-wider text-gray-400">Amount</th>
                        <th class="text-center px-4 py-4 text-xs font-bold uppercase tracking-wider text-gray-400">Status</th>
                        <th class="text-center px-4 py-4 text-xs font-bold uppercase tracking-wider text-gray-400">Cashier</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $p)
                    <tr class="tbl-row">
                        <td class="px-5 py-3.5">
                            <p class="font-semibold text-gray-800">{{ $p->student->name ?? '—' }}</p>
                            <p class="text-xs text-gray-400">{{ $p->student->student_id ?? '' }} · {{ $p->school_year }} Sem{{ $p->semester }}</p>
                        </td>
                        <td class="px-4 py-3.5">
                            <p class="font-mono text-xs text-gray-700">{{ $p->or_number ?? '—' }}</p>
                            @if($p->reference_number)<p class="font-mono text-xs text-gray-400">{{ $p->reference_number }}</p>@endif
                        </td>
                        <td class="px-4 py-3.5 text-gray-700">{{ \Carbon\Carbon::parse($p->payment_date)->format('M j, Y') }}</td>
                        <td class="px-4 py-3.5"><span class="col-badge col-badge-indigo">{{ $p->payment_method }}</span></td>
                        <td class="px-4 py-3.5 text-right font-bold font-mono-num text-indigo-600">₱{{ number_format($p->amount, 2) }}</td>
                        <td class="px-4 py-3.5 text-center">
                            @if($p->status === 'completed')    <span class="col-badge col-badge-green">Completed</span>
                            @elseif($p->status === 'pending')  <span class="col-badge col-badge-amber">Pending</span>
                            @elseif($p->status === 'refunded') <span class="col-badge col-badge-sky">Refunded</span>
                            @else <span class="col-badge col-badge-red">Failed</span>
                            @endif
                        </td>
                        <td class="px-4 py-3.5 text-center text-xs text-gray-400">{{ $p->cashier->name ?? 'System' }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="px-5 py-10 text-center text-sm text-gray-400">No payments found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($payments->hasPages())
        <div class="px-5 py-4 border-t border-gray-100 bg-gray-50">{{ $payments->links() }}</div>
        @endif
    </div>
</div>
@endsection