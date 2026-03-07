{{-- resources/views/admin/fees/index.blade.php --}}
@extends('admin.layouts.admin-app')
@section('title', 'Fee Management')

@section('content')

<div class="flex items-center justify-between a-fade">
    <div>
        <h2 class="text-xl font-bold text-gray-800">Fee Management</h2>
        <p class="text-sm mt-0.5 text-gray-400">Configure and assign fee structures</p>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('admin.fees.bulk-create') }}" class="a-btn-secondary">Bulk Assign</a>
        <a href="{{ route('admin.fees.create') }}" class="a-btn-primary flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Add Fee
        </a>
    </div>
</div>

<div class="grid grid-cols-3 gap-4 a-fade a-d1">
    <div class="a-card px-5 py-4">
        <p class="text-2xl font-bold text-gray-800">{{ number_format($stats['total']) }}</p>
        <p class="text-xs mt-1 font-semibold text-indigo-600">Active Fees</p>
    </div>
    <div class="a-card px-5 py-4">
        <p class="text-2xl font-bold text-gray-800">₱{{ number_format($stats['amount'],2) }}</p>
        <p class="text-xs mt-1 font-semibold text-emerald-600">Total Assessed</p>
    </div>
    <div class="a-card px-5 py-4">
        <p class="text-2xl font-bold text-gray-800">{{ number_format($stats['waived']) }}</p>
        <p class="text-xs mt-1 font-semibold text-amber-500">Waived</p>
    </div>
</div>

<form class="a-card p-4 flex flex-wrap gap-3 a-fade a-d2" method="GET">
    <input name="q" value="{{ request('q') }}" placeholder="Search fee name…" class="a-input flex-1 min-w-44">
    <select name="school_year" class="a-input a-select w-36">
        <option value="">All Years</option>
        @foreach($schoolYears as $y)<option value="{{ $y }}" {{ request('school_year')===$y?'selected':'' }}>{{ $y }}</option>@endforeach
    </select>
    <select name="semester" class="a-input a-select w-36">
        <option value="">All Semesters</option>
        @foreach(['1'=>'1st Sem','2'=>'2nd Sem','summer'=>'Summer'] as $v=>$l)
        <option value="{{ $v }}" {{ request('semester')===$v?'selected':'' }}>{{ $l }}</option>
        @endforeach
    </select>
    <select name="level_group" class="a-input a-select w-40">
        <option value="">All Levels</option>
        @foreach($levelGroups as $lg)<option value="{{ $lg }}" {{ request('level_group')===$lg?'selected':'' }}>{{ $lg }}</option>@endforeach
    </select>
    <select name="status" class="a-input a-select w-32">
        <option value="">All Status</option>
        @foreach(['active','waived','cancelled'] as $s)
        <option value="{{ $s }}" {{ request('status')===$s?'selected':'' }}>{{ ucfirst($s) }}</option>
        @endforeach
    </select>
    <button type="submit" class="a-btn-primary px-5">Filter</button>
    <a href="{{ route('admin.fees') }}" class="a-btn-secondary">Reset</a>
</form>

<div class="a-card a-fade a-d3">
    <div class="overflow-x-auto">
        <table class="a-table">
            <thead>
                <tr><th>Student</th><th>Fee Name</th><th>Amount</th><th>Period</th><th>Status</th><th>Actions</th></tr>
            </thead>
            <tbody>
                @forelse($fees as $fee)
                <tr>
                    <td>
                        <p class="font-semibold text-gray-800 text-sm">{{ $fee->student->name ?? '—' }}</p>
                        <p class="text-xs font-mono-num text-gray-400">{{ $fee->student->student_id ?? '' }}</p>
                    </td>
                    <td>
                        <p class="font-semibold text-gray-800">{{ $fee->fee_name }}</p>
                        @if($fee->description)<p class="text-xs text-gray-400">{{ $fee->description }}</p>@endif
                    </td>
                    <td class="font-bold font-mono-num text-indigo-600">₱{{ number_format($fee->amount,2) }}</td>
                    <td class="text-xs text-gray-400">{{ $fee->school_year }} · Sem {{ $fee->semester }}</td>
                    <td><span class="a-badge {{ $fee->status==='active'?'a-badge-emerald':($fee->status==='waived'?'a-badge-sky':'a-badge-gray') }}">{{ ucfirst($fee->status) }}</span></td>
                    <td>
                        <div class="flex gap-2">
                            <a href="{{ route('admin.fees.edit', $fee) }}" class="a-btn-secondary text-xs py-1.5 px-3">Edit</a>
                            <form method="POST" action="{{ route('admin.fees.destroy', $fee) }}" onsubmit="return confirm('Delete this fee?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="a-btn-danger text-xs py-1.5 px-3">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center py-12 text-gray-400">No fees found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-6 py-4">{{ $fees->withQueryString()->links() }}</div>
</div>
@endsection