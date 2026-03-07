{{-- resources/views/treasurer/fees/index.blade.php --}}
@extends('treasurer.layouts.treasurer-app')
@section('title', 'Fee Management')
@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 fade-up">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Fee Management</h1>
            <p class="text-sm mt-1 text-gray-400">Assign and manage fees per student or level group</p>
        </div>
        <div class="flex gap-2 flex-wrap">
            <a href="{{ route('treasurer.fees.bulk-create') }}" class="btn-secondary">📋 Bulk Assign</a>
            <a href="{{ route('treasurer.fees.create') }}" class="btn-primary">➕ Add Fee</a>
        </div>
    </div>

    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 fade-up fade-up-d1">
        <div class="stat-card">
            <p class="text-xs uppercase tracking-wider font-bold mb-2 text-gray-400">Total Assessed</p>
            <p class="text-xl font-bold text-gray-800 font-mono-num">₱{{ number_format($stats['total'], 0) }}</p>
        </div>
        <div class="stat-card">
            <p class="text-xs uppercase tracking-wider font-bold mb-2 text-gray-400">Active Fees</p>
            <p class="text-xl font-bold font-mono-num text-emerald-600">{{ $stats['count'] }}</p>
        </div>
        <div class="stat-card">
            <p class="text-xs uppercase tracking-wider font-bold mb-2 text-gray-400">Waived</p>
            <p class="text-xl font-bold font-mono-num text-amber-600">{{ $stats['waived'] }}</p>
        </div>
        <div class="stat-card">
            <p class="text-xs uppercase tracking-wider font-bold mb-2 text-gray-400">Cancelled</p>
            <p class="text-xl font-bold font-mono-num text-red-500">{{ $stats['cancelled'] }}</p>
        </div>
    </div>

    <div class="section-card p-4 fade-up fade-up-d2">
        <form method="GET" class="flex flex-wrap gap-3 items-end">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search student or fee name…" class="form-input flex-1 min-w-[180px]">
            <select name="school_year" class="form-input w-auto">
                <option value="">All Years</option>
                @foreach($schoolYears as $sy)
                    <option value="{{ $sy }}" {{ request('school_year')==$sy?'selected':'' }}>{{ $sy }}</option>
                @endforeach
            </select>
            <select name="semester" class="form-input w-auto">
                <option value="">All Semesters</option>
                <option value="1"      {{ request('semester')=='1'?'selected':'' }}>1st Semester</option>
                <option value="2"      {{ request('semester')=='2'?'selected':'' }}>2nd Semester</option>
                <option value="summer" {{ request('semester')=='summer'?'selected':'' }}>Summer</option>
            </select>
            <select name="status" class="form-input w-auto">
                <option value="">All Status</option>
                <option value="active"    {{ request('status')=='active'?'selected':'' }}>Active</option>
                <option value="waived"    {{ request('status')=='waived'?'selected':'' }}>Waived</option>
                <option value="cancelled" {{ request('status')=='cancelled'?'selected':'' }}>Cancelled</option>
            </select>
            <button type="submit" class="btn-primary">🔍 Filter</button>
            <a href="{{ route('treasurer.fees') }}" class="btn-secondary">Clear</a>
        </form>
    </div>

    <div class="section-card fade-up fade-up-d3">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100 bg-gray-50">
                        <th class="text-left px-5 py-4 text-xs font-bold uppercase tracking-wider text-gray-400">Student</th>
                        <th class="text-left px-4 py-4 text-xs font-bold uppercase tracking-wider text-gray-400">Fee</th>
                        <th class="text-left px-4 py-4 text-xs font-bold uppercase tracking-wider text-gray-400">Period</th>
                        <th class="text-right px-4 py-4 text-xs font-bold uppercase tracking-wider text-gray-400">Amount</th>
                        <th class="text-center px-4 py-4 text-xs font-bold uppercase tracking-wider text-gray-400">Status</th>
                        <th class="text-center px-4 py-4 text-xs font-bold uppercase tracking-wider text-gray-400">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($fees as $fee)
                    <tr class="tbl-row">
                        <td class="px-5 py-3.5">
                            <p class="font-semibold text-gray-800">{{ $fee->student->name ?? '—' }}</p>
                            <p class="text-xs text-gray-400">{{ $fee->student->student_id ?? '' }} · {{ $fee->student->level_group ?? '' }}</p>
                        </td>
                        <td class="px-4 py-3.5">
                            <p class="font-semibold text-gray-800">{{ $fee->fee_name }}</p>
                            @if($fee->description)<p class="text-xs truncate max-w-[200px] text-gray-400">{{ $fee->description }}</p>@endif
                        </td>
                        <td class="px-4 py-3.5">
                            <p class="text-gray-700 font-semibold">{{ $fee->school_year }}</p>
                            <p class="text-xs text-gray-400">{{ match($fee->semester) { '1' => '1st Semester', '2' => '2nd Semester', 'summer' => 'Summer', default => $fee->semester.' Semester' } }}</p>
                        </td>
                        <td class="px-4 py-3.5 text-right font-bold font-mono-num text-indigo-600">₱{{ number_format($fee->amount, 2) }}</td>
                        <td class="px-4 py-3.5 text-center">
                            @if($fee->status === 'active')    <span class="col-badge col-badge-green">Active</span>
                            @elseif($fee->status === 'waived') <span class="col-badge col-badge-amber">Waived</span>
                            @else <span class="col-badge col-badge-red">Cancelled</span>
                            @endif
                        </td>
                        <td class="px-4 py-3.5 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('treasurer.fees.edit', $fee) }}" class="text-xs font-semibold px-3 py-1.5 rounded-lg bg-indigo-50 text-indigo-500 border border-indigo-100 hover:bg-indigo-100 transition">Edit</a>
                                <form method="POST" action="{{ route('treasurer.fees.destroy', $fee) }}" onsubmit="return confirm('Delete this fee?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn-danger text-xs px-3 py-1.5">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-5 py-10 text-center text-sm text-gray-400">
                            No fees found. <a href="{{ route('treasurer.fees.create') }}" class="underline text-indigo-500">Add one →</a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($fees->hasPages())
        <div class="px-5 py-4 border-t border-gray-100 bg-gray-50">{{ $fees->links() }}</div>
        @endif
    </div>
</div>
@endsection