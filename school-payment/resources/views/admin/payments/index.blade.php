{{-- resources/views/admin/payments/index.blade.php --}}
@extends('admin.layouts.admin-app')
@section('title', 'Payments')

@section('content')

<div class="flex items-center justify-between a-fade">
    <div>
        <h2 class="text-xl font-bold text-gray-800">Payment Transactions</h2>
        <p class="text-sm mt-0.5 text-gray-400">Full transaction log with void capabilities</p>
    </div>
</div>

<div class="grid grid-cols-3 gap-4 a-fade a-d1">
    <div class="a-card px-5 py-4">
        <p class="text-xl font-bold text-gray-800 font-mono-num">₱{{ number_format($stats['today'],2) }}</p>
        <p class="text-xs mt-1 font-semibold text-indigo-600">Today's Collection</p>
    </div>
    <div class="a-card px-5 py-4">
        <p class="text-2xl font-bold text-gray-800">{{ number_format($stats['completed']) }}</p>
        <p class="text-xs mt-1 font-semibold text-emerald-600">Completed</p>
    </div>
    <div class="a-card px-5 py-4">
        <p class="text-2xl font-bold text-gray-800">{{ number_format($stats['pending']) }}</p>
        <p class="text-xs mt-1 font-semibold text-amber-500">Pending</p>
    </div>
</div>

<form class="a-card p-4 flex flex-wrap gap-3 a-fade a-d2" method="GET">
    <input name="q" value="{{ request('q') }}" placeholder="Search student or OR no…" class="a-input flex-1 min-w-44">
    <select name="method" class="a-input a-select w-36">
        <option value="">All Methods</option>
        @foreach(['Cash','GCash','PayMaya','Bank Transfer','Check'] as $m)
        <option value="{{ $m }}" {{ request('method')===$m?'selected':'' }}>{{ $m }}</option>
        @endforeach
    </select>
    <select name="status" class="a-input a-select w-32">
        <option value="">All Status</option>
        @foreach(['pending','completed','failed','refunded'] as $s)
        <option value="{{ $s }}" {{ request('status')===$s?'selected':'' }}>{{ ucfirst($s) }}</option>
        @endforeach
    </select>
    <select name="school_year" class="a-input a-select w-36">
        <option value="">All Years</option>
        @foreach($schoolYears as $y)<option value="{{ $y }}" {{ request('school_year')===$y?'selected':'' }}>{{ $y }}</option>@endforeach
    </select>
    <input name="date_from" type="date" value="{{ request('date_from') }}" class="a-input w-36">
    <input name="date_to"   type="date" value="{{ request('date_to') }}"   class="a-input w-36">
    <button type="submit" class="a-btn-primary px-5">Filter</button>
    <a href="{{ route('admin.payments') }}" class="a-btn-secondary">Reset</a>
</form>

@if($totalAmount > 0)
<div class="px-5 py-3 rounded-xl a-fade a-d2 bg-indigo-50 border border-indigo-200">
    <span class="text-sm font-semibold text-indigo-600">Filtered total (completed): </span>
    <span class="font-bold text-gray-800 font-mono-num">₱{{ number_format($totalAmount,2) }}</span>
</div>
@endif

<div class="a-card a-fade a-d3">
    <div class="overflow-x-auto">
        <table class="a-table">
            <thead>
                <tr><th>Student</th><th>Amount</th><th>Method</th><th>OR Number</th><th>Cashier</th><th>Date</th><th>Status</th><th>Actions</th></tr>
            </thead>
            <tbody>
                @forelse($payments as $p)
                <tr>
                    <td>
                        <p class="font-semibold text-gray-800 text-sm">{{ $p->student->name ?? '—' }}</p>
                        <p class="text-xs font-mono-num text-gray-400">{{ $p->student->student_id ?? '' }}</p>
                    </td>
                    <td class="font-bold font-mono-num text-indigo-600">₱{{ number_format($p->amount,2) }}</td>
                    <td><span class="a-badge a-badge-sky">{{ $p->payment_method }}</span></td>
                    <td class="font-mono-num text-xs text-gray-400">{{ $p->or_number ?? '—' }}</td>
                    <td class="text-xs text-gray-400">{{ $p->cashier->name ?? 'System' }}</td>
                    <td class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($p->payment_date)->format('M d, Y') }}</td>
                    <td><span class="a-badge {{ $p->status==='completed'?'a-badge-emerald':($p->status==='pending'?'a-badge-amber':'a-badge-red') }}">{{ ucfirst($p->status) }}</span></td>
                    <td>
                        <div class="flex gap-2">
                            <a href="{{ route('admin.payments.show', $p) }}" class="a-btn-secondary text-xs py-1.5 px-3">View</a>
                            @if(in_array($p->status, ['completed','pending']))
                            <form method="POST" action="{{ route('admin.payments.void', $p) }}" onsubmit="return confirm('Void payment OR#{{ $p->or_number }}?')">
                                @csrf @method('PATCH')
                                <button type="submit" class="a-btn-danger text-xs py-1.5 px-3">Void</button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center py-12 text-gray-400">No transactions found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-6 py-4">{{ $payments->withQueryString()->links() }}</div>
</div>
@endsection