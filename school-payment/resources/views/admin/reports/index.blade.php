{{-- resources/views/admin/reports/index.blade.php --}}
@extends('admin.layouts.admin-app')
@section('title', 'Reports & Analytics')

@section('content')

<div class="flex items-center justify-between a-fade">
    <div>
        <h2 class="text-xl font-bold text-gray-800">Reports &amp; Analytics</h2>
        <p class="text-sm mt-0.5 text-gray-400">Financial reports, defaulter analysis, and aging</p>
    </div>
    <div class="flex gap-2">
        <button onclick="window.print()" class="a-btn-secondary">🖨️ Print</button>
    </div>
</div>

{{-- Filters --}}
<form class="a-card p-4 flex flex-wrap gap-3 a-fade a-d1" method="GET">
    <select name="school_year" class="a-input a-select w-40">
        <option value="">All Years</option>
        @foreach($schoolYears as $y)<option value="{{ $y }}" {{ $schoolYear===$y?'selected':'' }}>{{ $y }}</option>@endforeach
    </select>
    <select name="semester" class="a-input a-select w-36">
        @foreach(['1'=>'1st Semester','2'=>'2nd Semester','summer'=>'Summer'] as $v=>$l)
        <option value="{{ $v }}" {{ $semester===$v?'selected':'' }}>{{ $l }}</option>
        @endforeach
    </select>
    <button type="submit" class="a-btn-primary px-5">Generate</button>
</form>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 a-fade a-d2">

    {{-- Collection by Method --}}
    <div class="a-card p-6">
        <h3 class="font-bold text-gray-800 mb-4">Collection by Payment Method</h3>
        @php $totalM = $byMethod->sum('total') ?: 1; @endphp
        <div class="space-y-3">
            @forelse($byMethod as $m)
            @php $pct = round($m->total/$totalM*100); @endphp
            <div>
                <div class="flex justify-between text-sm mb-1">
                    <span class="font-semibold text-gray-700">{{ $m->payment_method }}</span>
                    <span class="font-mono-num text-indigo-600">₱{{ number_format($m->total,2) }} ({{ $pct }}%)</span>
                </div>
                <div class="a-progress-track"><div class="a-progress-fill" style="width:{{ $pct }}%"></div></div>
                <p class="text-xs mt-0.5 text-gray-400">{{ $m->count }} transactions</p>
            </div>
            @empty
            <p class="text-sm text-gray-400">No data.</p>
            @endforelse
        </div>
    </div>

    {{-- Collection by Level --}}
    <div class="a-card p-6">
        <h3 class="font-bold text-gray-800 mb-4">Collection by Level Group</h3>
        @php $totalL = $byLevel->sum('total') ?: 1; @endphp
        <div class="space-y-3">
            @forelse($byLevel as $l)
            @php $pct = round($l->total/$totalL*100); @endphp
            <div>
                <div class="flex justify-between text-sm mb-1">
                    <span class="font-semibold text-gray-700">{{ $l->level_group ?: 'Unknown' }}</span>
                    <span class="font-mono-num text-emerald-600">₱{{ number_format($l->total,2) }} ({{ $pct }}%)</span>
                </div>
                <div class="a-progress-track"><div class="a-progress-fill" style="width:{{ $pct }}%;background:linear-gradient(90deg,#059669,#34d399);"></div></div>
            </div>
            @empty
            <p class="text-sm text-gray-400">No data.</p>
            @endforelse
        </div>
    </div>

</div>

{{-- Aging Report --}}
<div class="a-card a-fade a-d3">
    <div class="px-6 py-4 border-b border-gray-100">
        <h3 class="font-bold text-gray-800">Accounts Receivable Aging</h3>
        <p class="text-xs mt-0.5 text-gray-400">Based on installment due dates · {{ $schoolYear }} Sem {{ $semester }}</p>
    </div>
    <div class="overflow-x-auto">
        <table class="a-table">
            <thead><tr><th>Age Range</th><th>Amount</th><th>Risk</th><th>%</th></tr></thead>
            <tbody>
                @php $totalAging = array_sum($aging) ?: 1; @endphp
                @foreach([
                    ['Current (Not Yet Due)', 'current', 'a-badge-emerald', 'Low'],
                    ['1-30 Days Overdue',      '1-30',   'a-badge-amber',   'Medium'],
                    ['31-60 Days',             '31-60',  'a-badge-red',     'High'],
                    ['61-90 Days',             '61-90',  'a-badge-red',     'High'],
                    ['90+ Days (Critical)',    '90+',    'a-badge-red',     'Critical'],
                ] as [$label,$key,$badge,$risk])
                @php $pct = $aging[$key] > 0 ? round($aging[$key]/$totalAging*100) : 0; @endphp
                <tr>
                    <td class="font-semibold text-gray-800">{{ $label }}</td>
                    <td class="font-bold font-mono-num text-indigo-600">₱{{ number_format($aging[$key],2) }}</td>
                    <td><span class="a-badge {{ $badge }}">{{ $risk }}</span></td>
                    <td class="w-40">
                        <div class="flex items-center gap-2">
                            <div class="a-progress-track flex-1"><div class="a-progress-fill" style="width:{{ $pct }}%;"></div></div>
                            <span class="text-xs text-gray-400">{{ $pct }}%</span>
                        </div>
                    </td>
                </tr>
                @endforeach
                <tr class="border-t-2 border-gray-200">
                    <td class="font-bold text-gray-800">Total Outstanding</td>
                    <td class="font-bold font-mono-num text-gray-800">₱{{ number_format(array_sum($aging),2) }}</td>
                    <td colspan="2"></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

{{-- Fee Head Breakdown --}}
<div class="a-card a-fade a-d4">
    <div class="px-6 py-4 border-b border-gray-100">
        <h3 class="font-bold text-gray-800">Fee Head Breakdown · {{ $schoolYear }} Sem {{ $semester }}</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="a-table">
            <thead><tr><th>Fee Type</th><th>No. of Students</th><th>Total Assessed</th></tr></thead>
            <tbody>
                @forelse($byFee as $f)
                <tr>
                    <td class="font-semibold text-gray-800">{{ $f->fee_name }}</td>
                    <td class="text-sm text-gray-500">{{ number_format($f->count) }}</td>
                    <td class="font-bold font-mono-num text-indigo-600">₱{{ number_format($f->total,2) }}</td>
                </tr>
                @empty
                <tr><td colspan="3" class="text-center py-10 text-gray-400">No fee data.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Defaulter List --}}
<div class="a-card a-fade a-d5">
    <div class="px-6 py-4 border-b border-red-50">
        <h3 class="font-bold text-gray-800">Defaulter Report <span class="a-badge a-badge-red ml-2">{{ count($defaulters) }} students</span></h3>
        <p class="text-xs mt-0.5 text-gray-400">Students with outstanding balance · {{ $schoolYear }} Sem {{ $semester }} · sorted by highest balance</p>
    </div>
    <div class="overflow-x-auto">
        <table class="a-table">
            <thead><tr><th>Student</th><th>Level</th><th>Outstanding Balance</th></tr></thead>
            <tbody>
                @forelse($defaulters as $d)
                <tr>
                    <td>
                        <p class="font-semibold text-gray-800">{{ $d['student']->name }} {{ $d['student']->last_name }}</p>
                        <p class="text-xs font-mono-num text-gray-400">{{ $d['student']->student_id ?? '—' }}</p>
                    </td>
                    <td class="text-sm text-gray-500">{{ $d['student']->level_group ?? '—' }}</td>
                    <td class="font-bold font-mono-num text-red-500">₱{{ number_format($d['balance'],2) }}</td>
                </tr>
                @empty
                <tr><td colspan="3" class="text-center py-10 text-emerald-600">🎉 No defaulters! All students are paid.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection