{{-- resources/views/admin/clearances/index.blade.php --}}
@extends('admin.layouts.admin-app')
@section('title', 'Clearance Management')

@section('content')

<div class="flex items-center justify-between a-fade">
    <div>
        <h2 class="text-xl font-bold text-gray-800">Clearance Management</h2>
        <p class="text-sm mt-0.5 text-gray-400">Grant or place holds on student finance clearances · Auto-synced with balances</p>
    </div>
</div>

<div class="grid grid-cols-3 gap-4 a-fade a-d1">
    <div class="a-card px-5 py-4"><p class="text-2xl font-bold text-gray-800">{{ $stats['cleared'] }}</p><p class="text-xs mt-1 font-semibold text-emerald-600">✅ Cleared</p></div>
    <div class="a-card px-5 py-4"><p class="text-2xl font-bold text-gray-800">{{ $stats['on_hold'] }}</p><p class="text-xs mt-1 font-semibold text-red-500">🚫 On Hold</p></div>
    <div class="a-card px-5 py-4"><p class="text-2xl font-bold text-gray-800">{{ $stats['manual'] }}</p><p class="text-xs mt-1 font-semibold text-amber-500">Manual Override</p></div>
</div>

{{-- Filters --}}
<form class="a-card p-4 flex flex-wrap gap-3 a-fade a-d2" method="GET"
      x-data="{
          level: '{{ request('level_group') }}',
          yearLevels: {
              'Kinder':      ['Kinder 1', 'Kinder 2'],
              'Elementary':  ['Grade 1','Grade 2','Grade 3','Grade 4','Grade 5','Grade 6'],
              'Junior High': ['Grade 7','Grade 8','Grade 9','Grade 10'],
              'Senior High': ['Grade 11','Grade 12'],
              'College':     ['1st Year','2nd Year','3rd Year','4th Year','5th Year'],
          }
      }">

    {{-- Search --}}
    <input name="q" value="{{ request('q') }}" placeholder="Search student name or ID…" class="a-input flex-1 min-w-44">

    {{-- School Year --}}
    <select name="school_year" class="a-input a-select w-36">
        <option value="">All Years</option>
        @foreach($schoolYears as $y)
        <option value="{{ $y }}" {{ request('school_year')===$y?'selected':'' }}>{{ $y }}</option>
        @endforeach
    </select>

    {{-- Semester --}}
    <select name="semester" class="a-input a-select w-36">
        <option value="">All Semesters</option>
        @foreach(['1'=>'1st Sem','2'=>'2nd Sem','summer'=>'Summer'] as $v=>$l)
        <option value="{{ $v }}" {{ request('semester')===$v?'selected':'' }}>{{ $l }}</option>
        @endforeach
    </select>

    {{-- Level Group (cascades year level) --}}
    <select name="level_group" class="a-input a-select w-40"
            x-model="level"
            @change="$el.form.querySelector('[name=year_level]').value = ''">
        <option value="">All Levels</option>
        @foreach(['Kinder','Elementary','Junior High','Senior High','College'] as $lg)
        <option value="{{ $lg }}" {{ request('level_group')===$lg?'selected':'' }}>{{ $lg }}</option>
        @endforeach
    </select>

    {{-- Year Level — options change based on selected level --}}
    <select name="year_level" class="a-input a-select w-36">
        <option value="">All Year Levels</option>
        <template x-if="level && yearLevels[level]">
            <template x-for="yl in yearLevels[level]" :key="yl">
                <option :value="yl"
                        :selected="yl === '{{ request('year_level') }}'"
                        x-text="yl"></option>
            </template>
        </template>
        @if(request('year_level') && request('level_group'))
            <option value="{{ request('year_level') }}" selected>{{ request('year_level') }}</option>
        @endif
    </select>

    {{-- Clearance Status --}}
    <select name="status" class="a-input a-select w-32">
        <option value="">All Status</option>
        <option value="cleared" {{ request('status')==='cleared'?'selected':'' }}>Cleared</option>
        <option value="on_hold" {{ request('status')==='on_hold'?'selected':'' }}>On Hold</option>
    </select>

    <button type="submit" class="a-btn-primary px-5">Filter</button>
    <a href="{{ route('admin.clearances', ['school_year'=>$schoolYear,'semester'=>$semester]) }}" class="a-btn-secondary">Reset</a>
</form>

{{-- Table --}}
<div class="a-card a-fade a-d3">
    <div class="overflow-x-auto">
        <table class="a-table">
            <thead>
                <tr>
                    <th>Student</th>
                    <th>Level</th>
                    <th>Year Level</th>
                    <th>Period</th>
                    <th>Status</th>
                    <th>Hold Reason</th>
                    <th>Override</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($clearances as $c)
                <tr>
                    <td>
                        <p class="font-semibold text-gray-800 text-sm">{{ $c->student->name ?? '—' }} {{ $c->student->last_name ?? '' }}</p>
                        <p class="text-xs font-mono-num text-gray-400">{{ $c->student->student_id ?? '' }}</p>
                    </td>
                    <td>
                        @php
                            $levelColors = [
                                'Kinder'      => 'a-badge-sky',
                                'Elementary'  => 'a-badge-emerald',
                                'Junior High' => 'a-badge-violet',
                                'Senior High' => 'a-badge-amber',
                                'College'     => 'a-badge-gray',
                            ];
                            $lc = $levelColors[$c->student->level_group ?? ''] ?? 'a-badge-gray';
                        @endphp
                        <span class="a-badge {{ $lc }}">{{ $c->student->level_group ?? '—' }}</span>
                    </td>
                    <td class="text-sm text-gray-500">{{ $c->student->year_level ?? '—' }}</td>
                    <td class="text-xs text-gray-400">{{ $c->school_year }} · Sem {{ $c->semester }}</td>
                    <td><span class="a-badge {{ $c->is_cleared ? 'a-badge-emerald' : 'a-badge-red' }}">{{ $c->is_cleared ? '✅ Cleared' : '🚫 Hold' }}</span></td>
                    <td class="text-xs text-red-500 max-w-xs">{{ $c->hold_reason ?? '—' }}</td>
                    <td>
                        @if($c->manual_override)
                            <span class="a-badge a-badge-amber">Manual</span>
                        @else
                            <span class="text-xs text-gray-400">Auto</span>
                        @endif
                    </td>
                    <td>
                        <div class="flex gap-2" x-data="{ showHold: false }">
                            @if(!$c->is_cleared)
                            <form method="POST" action="{{ route('admin.clearances.grant', $c) }}">
                                @csrf @method('PATCH')
                                <button type="submit" class="a-btn-secondary text-xs py-1.5 px-3" style="color:#059669;border-color:#a7f3d0;">Grant</button>
                            </form>
                            @endif

                            @if($c->is_cleared)
                            <button @click="showHold=true" type="button" class="a-btn-danger text-xs py-1.5 px-3">Hold</button>

                            {{-- Hold Modal --}}
                            <div x-show="showHold" x-cloak
                                 class="fixed inset-0 z-50 flex items-center justify-center bg-black/30 backdrop-blur-sm px-4">
                                <form method="POST" action="{{ route('admin.clearances.hold', $c) }}"
                                      class="bg-white rounded-2xl shadow-xl border border-gray-100 p-6 w-full max-w-md space-y-4">
                                    @csrf @method('PATCH')

                                    {{-- Modal header --}}
                                    <div class="flex items-center justify-between">
                                        <h4 class="font-bold text-gray-800">Place Hold</h4>
                                        <button type="button" @click="showHold=false"
                                                class="w-7 h-7 flex items-center justify-center rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-all">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                        </button>
                                    </div>

                                    {{-- Student info pill --}}
                                    <div class="px-3 py-2.5 rounded-xl bg-gray-50 border border-gray-100">
                                        <p class="text-sm font-semibold text-gray-700">{{ $c->student->name ?? '' }} {{ $c->student->last_name ?? '' }}</p>
                                        <div class="flex flex-wrap gap-1.5 mt-1.5">
                                            @php $lc2 = $levelColors[$c->student->level_group ?? ''] ?? 'a-badge-gray'; @endphp
                                            <span class="a-badge {{ $lc2 }} text-[10px]">{{ $c->student->level_group ?? '—' }}</span>
                                            <span class="a-badge a-badge-gray text-[10px]">{{ $c->student->year_level ?? '—' }}</span>
                                            <span class="a-badge a-badge-gray text-[10px] font-mono-num">{{ $c->student->student_id ?? '—' }}</span>
                                        </div>
                                    </div>

                                    {{-- Reason textarea --}}
                                    <div>
                                        <label class="block text-xs font-bold mb-2 text-gray-500">Reason for Hold *</label>
                                        <textarea name="hold_reason" rows="3" required class="a-input"
                                                  placeholder="e.g. Unpaid balance of ₱4,500 for 1st semester…"
                                                  style="resize:vertical;"></textarea>
                                    </div>

                                    <div class="flex gap-3">
                                        <button type="submit" class="a-btn-danger flex-1">Confirm Hold</button>
                                        <button type="button" @click="showHold=false" class="a-btn-secondary flex-1 justify-center">Cancel</button>
                                    </div>
                                </form>
                            </div>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center py-12 text-gray-400">No clearance records found. Sync is triggered automatically on page load.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-6 py-4">{{ $clearances->withQueryString()->links() }}</div>
</div>

@endsection