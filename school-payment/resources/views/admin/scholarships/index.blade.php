{{-- resources/views/admin/scholarships/index.blade.php --}}
@extends('admin.layouts.admin-app')
@section('title', 'Scholarships')

@section('content')

<div class="flex items-center justify-between a-fade">
    <div>
        <h2 class="text-xl font-bold text-gray-800">Scholarships &amp; Discounts</h2>
        <p class="text-sm mt-0.5 text-gray-400">Manage student fee waivers, discounts and scholarships</p>
    </div>
    <a href="{{ route('admin.scholarships.create') }}" class="a-btn-primary flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Grant Scholarship
    </a>
</div>

<div class="grid grid-cols-3 gap-4 a-fade a-d1">
    <div class="a-card px-5 py-4"><p class="text-2xl font-bold text-gray-800">{{ $stats['active'] }}</p><p class="text-xs mt-1 font-semibold text-indigo-600">Active</p></div>
    <div class="a-card px-5 py-4"><p class="text-2xl font-bold text-gray-800">{{ $stats['revoked'] }}</p><p class="text-xs mt-1 font-semibold text-red-500">Revoked</p></div>
    <div class="a-card px-5 py-4"><p class="text-2xl font-bold text-gray-800">₱{{ number_format($stats['total_discount'],2) }}</p><p class="text-xs mt-1 font-semibold text-emerald-600">Fixed Discounts Given</p></div>
</div>

<form class="a-card p-4 flex flex-wrap gap-3 a-fade a-d2" method="GET">
    <input name="q" value="{{ request('q') }}" placeholder="Search student…" class="a-input flex-1 min-w-44">
    <select name="school_year" class="a-input a-select w-36"><option value="">All Years</option>@foreach($schoolYears as $y)<option value="{{ $y }}" {{ request('school_year')===$y?'selected':'' }}>{{ $y }}</option>@endforeach</select>
    <select name="semester" class="a-input a-select w-36"><option value="">All Semesters</option>@foreach(['1'=>'1st','2'=>'2nd','summer'=>'Summer'] as $v=>$l)<option value="{{ $v }}" {{ request('semester')===$v?'selected':'' }}>{{ $l }}</option>@endforeach</select>
    <select name="status" class="a-input a-select w-32"><option value="">All Status</option>@foreach(['active','revoked'] as $s)<option value="{{ $s }}" {{ request('status')===$s?'selected':'' }}>{{ ucfirst($s) }}</option>@endforeach</select>
    <button type="submit" class="a-btn-primary px-5">Filter</button>
    <a href="{{ route('admin.scholarships') }}" class="a-btn-secondary">Reset</a>
</form>

<div class="a-card a-fade a-d3">
    <div class="overflow-x-auto">
        <table class="a-table">
            <thead><tr><th>Student</th><th>Scholarship</th><th>Discount</th><th>Applies To</th><th>Period</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody>
                @forelse($scholarships as $sc)
                <tr>
                    <td>
                        <p class="font-semibold text-gray-800 text-sm">{{ $sc->student->name ?? '—' }}</p>
                        <p class="text-xs font-mono-num text-gray-400">{{ $sc->student->student_id ?? '' }}</p>
                    </td>
                    <td>
                        <p class="font-semibold text-gray-800">{{ $sc->scholarship_name }}</p>
                        @if($sc->remarks)<p class="text-xs text-gray-400">{{ Str::limit($sc->remarks,40) }}</p>@endif
                    </td>
                    <td class="font-bold font-mono-num text-indigo-600">
                        {{ $sc->discount_type==='percent' ? $sc->discount_value.'%' : '₱'.number_format($sc->discount_value,2) }}
                        @if($sc->max_discount)<span class="text-xs text-gray-400">(max ₱{{ number_format($sc->max_discount,2) }})</span>@endif
                    </td>
                    <td class="text-xs text-gray-500">{{ $sc->applies_to_fee ?? 'All Fees' }}</td>
                    <td class="text-xs text-gray-400">{{ $sc->school_year }} · Sem {{ $sc->semester }}</td>
                    <td><span class="a-badge {{ $sc->status==='active'?'a-badge-emerald':'a-badge-gray' }}">{{ ucfirst($sc->status) }}</span></td>
                    <td>
                        @if($sc->status==='active')
                        <form method="POST" action="{{ route('admin.scholarships.revoke', $sc) }}" onsubmit="return confirm('Revoke this scholarship?')">
                            @csrf @method('PATCH')
                            <button type="submit" class="a-btn-danger text-xs py-1.5 px-3">Revoke</button>
                        </form>
                        @else
                        <span class="text-xs text-gray-400">Revoked</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center py-12 text-gray-400">No scholarships found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-6 py-4">{{ $scholarships->withQueryString()->links() }}</div>
</div>
@endsection