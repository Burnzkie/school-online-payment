{{-- resources/views/treasurer/clearances/index.blade.php --}}
@extends('treasurer.layouts.treasurer-app')
@section('title', 'Clearance Management')
@section('content')
<div class="space-y-6">

    <div class="fade-up">
        <h1 class="text-2xl font-bold text-gray-800">Finance Clearance Management</h1>
        <p class="text-sm mt-1 text-gray-400">Control which students are cleared for exams, enrollment, and document release</p>
    </div>

    <div class="section-card p-4 fade-up fade-up-d1">
        <form method="GET" class="flex flex-wrap gap-3 items-end">
            <div>
                <label class="block text-xs font-bold mb-1 text-gray-500">School Year</label>
                <select name="school_year" class="form-input">
                    @foreach($schoolYears as $sy)
                        <option value="{{ $sy }}" {{ $schoolYear==$sy?'selected':'' }}>{{ $sy }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold mb-1 text-gray-500">Semester</label>
                <select name="semester" class="form-input">
                    <option value="1"      {{ $semester=='1'?'selected':'' }}>1st Semester</option>
                    <option value="2"      {{ $semester=='2'?'selected':'' }}>2nd Semester</option>
                    <option value="summer" {{ $semester=='summer'?'selected':'' }}>Summer</option>
                </select>
            </div>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Student name or ID…" class="form-input flex-1 min-w-[160px]">
            <select name="level_group" class="form-input w-auto">
                <option value="">All Levels</option>
                @foreach($levelGroups as $lg)
                    <option value="{{ $lg }}" {{ request('level_group')==$lg?'selected':'' }}>{{ $lg }}</option>
                @endforeach
            </select>
            <select name="status" class="form-input w-auto">
                <option value="">All Status</option>
                <option value="cleared" {{ request('status')=='cleared'?'selected':'' }}>Cleared</option>
                <option value="on_hold" {{ request('status')=='on_hold'?'selected':'' }}>On Hold</option>
            </select>
            <button type="submit" class="btn-primary">🔍 Filter</button>
        </form>
    </div>

    <div class="grid grid-cols-3 gap-4 fade-up fade-up-d1">
        <div class="stat-card border-emerald-100 bg-emerald-50">
            <p class="text-xs uppercase tracking-wider font-bold mb-2 text-gray-400">✅ Cleared</p>
            <p class="text-3xl font-bold font-mono-num text-emerald-600">{{ $stats['cleared'] }}</p>
        </div>
        <div class="stat-card border-red-100 bg-red-50">
            <p class="text-xs uppercase tracking-wider font-bold mb-2 text-gray-400">🚫 On Hold</p>
            <p class="text-3xl font-bold font-mono-num text-red-500">{{ $stats['on_hold'] }}</p>
        </div>
        <div class="stat-card">
            <p class="text-xs uppercase tracking-wider font-bold mb-2 text-gray-400">⚡ Manual Override</p>
            <p class="text-3xl font-bold font-mono-num text-amber-600">{{ $stats['manual'] }}</p>
        </div>
    </div>

    <div class="p-4 rounded-xl text-sm fade-up bg-indigo-50 border border-indigo-100 text-gray-600">
        💡 Clearances are <strong class="text-gray-800">auto-synced</strong> from each student's current balance every time this page loads.
        Students with zero balance are auto-cleared. You can still <strong class="text-gray-800">manually override</strong> individual cases below.
    </div>

    <div class="section-card fade-up fade-up-d2">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100 bg-gray-50">
                        <th class="text-left px-5 py-4 text-xs font-bold uppercase tracking-wider text-gray-400">Student</th>
                        <th class="text-left px-4 py-4 text-xs font-bold uppercase tracking-wider text-gray-400">Level</th>
                        <th class="text-center px-4 py-4 text-xs font-bold uppercase tracking-wider text-gray-400">Status</th>
                        <th class="text-left px-4 py-4 text-xs font-bold uppercase tracking-wider text-gray-400">Reason / Note</th>
                        <th class="text-center px-4 py-4 text-xs font-bold uppercase tracking-wider text-gray-400">Override</th>
                        <th class="text-center px-4 py-4 text-xs font-bold uppercase tracking-wider text-gray-400">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($clearances as $c)
                    <tr class="tbl-row" x-data="{ showGrant: false, showHold: false }">
                        <td class="px-5 py-3.5">
                            <p class="font-semibold text-gray-800">{{ $c->student->name ?? '—' }}</p>
                            <p class="text-xs text-gray-400">{{ $c->student->student_id ?? 'No ID' }}</p>
                        </td>
                        <td class="px-4 py-3.5"><span class="col-badge col-badge-indigo text-xs">{{ $c->student->level_group ?? '—' }}</span></td>
                        <td class="px-4 py-3.5 text-center">
                            @if($c->is_cleared) <span class="col-badge col-badge-green">✅ Cleared</span>
                            @else <span class="col-badge col-badge-red">🚫 On Hold</span>
                            @endif
                        </td>
                        <td class="px-4 py-3.5 text-xs text-gray-500 max-w-[200px]">
                            @if($c->manual_override)<span class="col-badge col-badge-amber mr-1">Manual</span>{{ $c->override_note ?? $c->hold_reason ?? '—' }}
                            @else{{ $c->hold_reason ?? ($c->is_cleared ? 'Balance settled' : '—') }}
                            @endif
                        </td>
                        <td class="px-4 py-3.5 text-center">
                            @if($c->manual_override) <span class="col-badge col-badge-amber">⚡ Yes</span>
                            @else <span class="text-xs text-gray-300">Auto</span>
                            @endif
                        </td>
                        <td class="px-4 py-3.5">
                            <div class="flex items-center justify-center gap-2">
                                @if(!$c->is_cleared)
                                <button @click="showGrant = !showGrant" class="text-xs px-3 py-1.5 rounded-lg font-semibold bg-emerald-50 text-emerald-600 border border-emerald-100 hover:bg-emerald-100 transition">Clear ✅</button>
                                <div x-show="showGrant" x-cloak class="absolute z-20 mt-1 p-3 rounded-xl shadow-2xl w-64 bg-white border border-emerald-100">
                                    <form method="POST" action="{{ route('treasurer.clearances.grant', $c) }}">
                                        @csrf @method('PATCH')
                                        <label class="block text-xs font-bold mb-1 text-gray-700">Override Note</label>
                                        <input type="text" name="override_note" placeholder="Reason for manual clear…" class="form-input text-xs mb-2">
                                        <button type="submit" class="btn-primary text-xs py-1.5 w-full justify-center">Confirm Clear</button>
                                    </form>
                                </div>
                                @endif
                                @if($c->is_cleared)
                                <button @click="showHold = !showHold" class="text-xs px-3 py-1.5 rounded-lg font-semibold bg-red-50 text-red-500 border border-red-100 hover:bg-red-100 transition">Hold 🚫</button>
                                <div x-show="showHold" x-cloak class="absolute z-20 mt-1 p-3 rounded-xl shadow-2xl w-64 bg-white border border-red-100">
                                    <form method="POST" action="{{ route('treasurer.clearances.hold', $c) }}">
                                        @csrf @method('PATCH')
                                        <label class="block text-xs font-bold mb-1 text-gray-700">Hold Reason *</label>
                                        <input type="text" name="hold_reason" placeholder="e.g. Promissory note expired" class="form-input text-xs mb-2" required>
                                        <button type="submit" class="w-full text-xs py-1.5 rounded-lg font-semibold bg-red-50 text-red-500 border border-red-100 hover:bg-red-100 transition">Confirm Hold</button>
                                    </form>
                                </div>
                                @endif
                                <a href="{{ route('treasurer.soa', $c->student) }}" class="text-xs px-2 py-1.5 rounded-lg bg-indigo-50 text-indigo-500 border border-indigo-100 hover:bg-indigo-100 transition">SOA</a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="px-5 py-10 text-center text-sm text-gray-400">No clearance records found for this period.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($clearances->hasPages())
        <div class="px-5 py-4 border-t border-gray-100 bg-gray-50">{{ $clearances->links() }}</div>
        @endif
    </div>
</div>
@endsection