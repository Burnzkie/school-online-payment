{{-- resources/views/admin/students/index.blade.php --}}
@extends('admin.layouts.admin-app')
@section('title', 'Students')

@section('content')

<div class="flex items-center justify-between a-fade">
    <div>
        <h2 class="text-xl font-bold text-gray-800">All Students</h2>
        <p class="text-sm mt-0.5 text-gray-400">{{ number_format($students->total()) }} enrolled students</p>
    </div>
</div>

<form class="a-card p-4 flex flex-wrap gap-3 a-fade a-d1" method="GET">
    <input name="q" value="{{ request('q') }}" placeholder="Search name, ID, email…" class="a-input flex-1 min-w-52">
    <select name="level_group" class="a-input a-select w-48">
        <option value="">All Level Groups</option>
        @foreach($levelGroups as $lg)
        <option value="{{ $lg }}" {{ request('level_group')===$lg?'selected':'' }}>{{ $lg }}</option>
        @endforeach
    </select>
    <button type="submit" class="a-btn-primary px-5">Filter</button>
    <a href="{{ route('admin.students') }}" class="a-btn-secondary">Reset</a>
</form>

<div class="a-card a-fade a-d2">
    <div class="overflow-x-auto">
        <table class="a-table">
            <thead>
                <tr><th>Student</th><th>Level / Program</th><th>Year Level</th><th>Student ID</th><th>Balance</th><th>Actions</th></tr>
            </thead>
            <tbody>
                @forelse($students as $s)
                @php
                    $fees = \App\Models\Fee::where('student_id',$s->id)->where('school_year',$currentYear)->where('status','active')->sum('amount');
                    $paid = \App\Models\Payment::where('student_id',$s->id)->where('school_year',$currentYear)->where('status','completed')->sum('amount');
                    $bal  = max(0,$fees-$paid);
                @endphp
                <tr>
                    <td>
                        <div class="flex items-center gap-3">
                            @if($s->profile_picture && Storage::disk('public')->exists($s->profile_picture))
                                <img src="{{ Storage::url($s->profile_picture) }}" class="rounded-full object-cover ring-1 ring-indigo-200 flex-shrink-0" style="width:2rem;height:2rem;min-width:2rem;">
                            @else
                                <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold text-white flex-shrink-0"
                                     style="background: linear-gradient(135deg,#0ea5e9,#06b6d4);">
                                    {{ strtoupper(substr($s->name,0,1)) }}
                                </div>
                            @endif
                            <div>
                                <p class="font-semibold text-gray-800 text-sm">{{ $s->name }} {{ $s->last_name }}</p>
                                <p class="text-xs text-gray-400">{{ $s->email }}</p>
                            </div>
                        </div>
                    </td>
                    <td>
                        <p class="text-sm text-gray-700">{{ $s->level_group ?? '—' }}</p>
                        <p class="text-xs text-gray-400">{{ $s->program ?? $s->strand ?? '—' }}</p>
                    </td>
                    <td class="text-sm text-gray-500">{{ $s->year_level ?? '—' }}</td>
                    <td class="font-mono-num text-xs text-gray-400">{{ $s->student_id ?? '—' }}</td>
                    <td>
                        @if($bal > 0)
                            <span class="font-bold font-mono-num text-red-500">₱{{ number_format($bal,2) }}</span>
                        @else
                            <span class="a-badge a-badge-emerald">Paid</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('admin.students.detail', $s) }}" class="a-btn-secondary text-xs py-1.5 px-3">View</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center py-12 text-gray-400">No students found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-6 py-4">{{ $students->withQueryString()->links() }}</div>
</div>
@endsection