{{-- resources/views/treasurer/scholarships/index.blade.php --}}
@extends('treasurer.layouts.treasurer-app')
@section('title', 'Scholarships & Discounts')
@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 fade-up">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Scholarships & Discounts</h1>
            <p class="text-sm mt-1 text-gray-400">Manage tuition discounts, scholarships, and financial aid</p>
        </div>
        <a href="{{ route('treasurer.scholarships.create') }}" class="btn-primary self-start">➕ Grant Scholarship</a>
    </div>

    <div class="grid grid-cols-3 gap-4 fade-up fade-up-d1">
        <div class="stat-card">
            <p class="text-xs uppercase tracking-wider font-bold mb-2 text-gray-400">Active</p>
            <p class="text-2xl font-bold font-mono-num text-emerald-600">{{ $stats['active'] }}</p>
        </div>
        <div class="stat-card">
            <p class="text-xs uppercase tracking-wider font-bold mb-2 text-gray-400">Revoked</p>
            <p class="text-2xl font-bold font-mono-num text-red-500">{{ $stats['revoked'] }}</p>
        </div>
        <div class="stat-card">
            <p class="text-xs uppercase tracking-wider font-bold mb-2 text-gray-400">Total Fixed Discounts</p>
            <p class="text-2xl font-bold font-mono-num text-amber-600">₱{{ number_format($stats['total_discount'], 0) }}</p>
        </div>
    </div>

    <div class="section-card p-4 fade-up fade-up-d2">
        <form method="GET" class="flex flex-wrap gap-3 items-end">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Student name or ID…" class="form-input flex-1 min-w-[180px]">
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
                <option value="active"  {{ request('status')=='active'?'selected':'' }}>Active</option>
                <option value="revoked" {{ request('status')=='revoked'?'selected':'' }}>Revoked</option>
            </select>
            <button type="submit" class="btn-primary">🔍 Filter</button>
            <a href="{{ route('treasurer.scholarships') }}" class="btn-secondary">Clear</a>
        </form>
    </div>

    <div class="section-card fade-up fade-up-d3">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100 bg-gray-50">
                        <th class="text-left px-5 py-4 text-xs font-bold uppercase tracking-wider text-gray-400">Student</th>
                        <th class="text-left px-4 py-4 text-xs font-bold uppercase tracking-wider text-gray-400">Scholarship</th>
                        <th class="text-left px-4 py-4 text-xs font-bold uppercase tracking-wider text-gray-400">Period</th>
                        <th class="text-center px-4 py-4 text-xs font-bold uppercase tracking-wider text-gray-400">Discount</th>
                        <th class="text-left px-4 py-4 text-xs font-bold uppercase tracking-wider text-gray-400">Applies To</th>
                        <th class="text-center px-4 py-4 text-xs font-bold uppercase tracking-wider text-gray-400">Status</th>
                        <th class="text-center px-4 py-4 text-xs font-bold uppercase tracking-wider text-gray-400">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($scholarships as $s)
                    <tr class="tbl-row">
                        <td class="px-5 py-3.5">
                            <p class="font-semibold text-gray-800">{{ $s->student->name ?? '—' }}</p>
                            <p class="text-xs text-gray-400">{{ $s->student->student_id ?? '' }} · {{ $s->student->level_group ?? '' }}</p>
                        </td>
                        <td class="px-4 py-3.5">
                            <p class="font-semibold text-gray-800">{{ $s->scholarship_name }}</p>
                            @if($s->remarks)<p class="text-xs truncate max-w-[180px] text-gray-400">{{ $s->remarks }}</p>@endif
                        </td>
                        <td class="px-4 py-3.5">
                            <p class="text-gray-700">{{ $s->school_year }}</p>
                            <p class="text-xs text-gray-400">Sem {{ $s->semester }}</p>
                        </td>
                        <td class="px-4 py-3.5 text-center">
                            <span class="font-bold font-mono-num text-amber-600">{{ $s->discount_label }}</span>
                            @if($s->max_discount)<p class="text-xs text-gray-400">max ₱{{ number_format($s->max_discount, 0) }}</p>@endif
                        </td>
                        <td class="px-4 py-3.5 text-xs text-gray-500">{{ $s->applies_to_fee ?? 'All fees' }}</td>
                        <td class="px-4 py-3.5 text-center">
                            @if($s->status === 'active') <span class="col-badge col-badge-green">Active</span>
                            @else <span class="col-badge col-badge-red">Revoked</span>
                            @endif
                        </td>
                        <td class="px-4 py-3.5 text-center">
                            @if($s->status === 'active')
                            <form method="POST" action="{{ route('treasurer.scholarships.revoke', $s) }}" onsubmit="return confirm('Revoke this scholarship?')">
                                @csrf @method('PATCH')
                                <button type="submit" class="btn-danger text-xs px-3 py-1.5">Revoke</button>
                            </form>
                            @else
                            <span class="text-xs text-gray-300">—</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-5 py-10 text-center text-sm text-gray-400">
                            No scholarships found. <a href="{{ route('treasurer.scholarships.create') }}" class="underline text-indigo-500">Grant one →</a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($scholarships->hasPages())
        <div class="px-5 py-4 border-t border-gray-100 bg-gray-50">{{ $scholarships->links() }}</div>
        @endif
    </div>
</div>
@endsection