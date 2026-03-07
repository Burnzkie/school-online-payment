{{-- resources/views/treasurer/aging/index.blade.php --}}
@extends('treasurer.layouts.treasurer-app')
@section('title', 'Aging Report')
@section('content')
<div class="space-y-6">

    <div class="fade-up">
        <h1 class="text-2xl font-bold text-gray-800">Receivables Aging Report</h1>
        <p class="text-sm mt-1 text-gray-400">Outstanding balances by overdue bracket — {{ $schoolYear }} · Semester {{ $semester }}</p>
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
            <button type="submit" class="btn-primary">📊 Generate</button>
        </form>
    </div>

    @php
        $bucketMeta = [
            'current' => ['label' => 'Current',     'color' => '#16a34a', 'bg' => '#f0fdf4', 'border' => '#bbf7d0'],
            '1-30'    => ['label' => '1–30 Days',    'color' => '#d97706', 'bg' => '#fffbeb', 'border' => '#fde68a'],
            '31-60'   => ['label' => '31–60 Days',   'color' => '#ea580c', 'bg' => '#fff7ed', 'border' => '#fed7aa'],
            '61-90'   => ['label' => '61–90 Days',   'color' => '#dc2626', 'bg' => '#fef2f2', 'border' => '#fecaca'],
            '90+'     => ['label' => 'Over 90 Days', 'color' => '#e11d48', 'bg' => '#fff1f2', 'border' => '#fecdd3'],
        ];
        $grandTotal = array_sum($bucketTotals);
    @endphp

    <div class="grid grid-cols-2 sm:grid-cols-5 gap-3 fade-up fade-up-d1">
        @foreach($bucketMeta as $key => $meta)
        <div class="stat-card" style="border-color: {{ $meta['border'] }}; background: {{ $meta['bg'] }};">
            <p class="text-xs font-bold uppercase tracking-wider mb-2 text-gray-500">{{ $meta['label'] }}</p>
            <p class="text-xl font-bold font-mono-num" style="color: {{ $meta['color'] }}">₱{{ number_format($bucketTotals[$key] ?? 0, 0) }}</p>
            <p class="text-xs mt-1 text-gray-400">{{ $grandTotal > 0 ? round((($bucketTotals[$key] ?? 0) / $grandTotal) * 100, 1) : 0 }}% of total</p>
        </div>
        @endforeach
    </div>

    <div class="section-card p-5 fade-up fade-up-d2">
        <div class="flex items-center justify-between mb-3">
            <h3 class="text-sm font-bold text-gray-800">Aging Distribution</h3>
            <span class="text-sm font-bold font-mono-num text-red-500">Total: ₱{{ number_format($grandTotal, 2) }}</span>
        </div>
        <div class="flex h-5 rounded-full overflow-hidden gap-0.5">
            @foreach($bucketMeta as $key => $meta)
                @php $pct = $grandTotal > 0 ? round((($bucketTotals[$key] ?? 0) / $grandTotal) * 100) : 0; @endphp
                @if($pct > 0)
                <div style="width: {{ $pct }}%; background: {{ $meta['color'] }}; transition: width 1s;" title="{{ $meta['label'] }}: ₱{{ number_format($bucketTotals[$key] ?? 0, 2) }}"></div>
                @endif
            @endforeach
        </div>
        <div class="flex flex-wrap gap-4 mt-3">
            @foreach($bucketMeta as $key => $meta)
            <div class="flex items-center gap-1.5 text-xs">
                <span class="w-3 h-3 rounded-full flex-shrink-0" style="background: {{ $meta['color'] }};"></span>
                <span class="text-gray-500">{{ $meta['label'] }}</span>
            </div>
            @endforeach
        </div>
    </div>

    @foreach($bucketMeta as $bucketKey => $meta)
        @php $bucketRows = $rows->where('bucket', $bucketKey)->values(); @endphp
        @if($bucketRows->count())
        <div class="section-card fade-up fade-up-d3" style="border-color: {{ $meta['border'] }};">
            <div class="px-5 py-4 flex items-center justify-between" style="border-bottom: 1px solid {{ $meta['border'] }}; background: {{ $meta['bg'] }};">
                <h3 class="text-sm font-bold" style="color: {{ $meta['color'] }}">{{ $meta['label'] }} — {{ $bucketRows->count() }} student{{ $bucketRows->count()!==1?'s':'' }}</h3>
                <span class="text-sm font-bold font-mono-num" style="color: {{ $meta['color'] }}">₱{{ number_format($bucketRows->sum('balance'), 2) }}</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100 bg-gray-50">
                            <th class="text-left px-5 py-3 text-xs font-bold uppercase tracking-wider text-gray-400">Student</th>
                            <th class="text-left px-4 py-3 text-xs font-bold uppercase tracking-wider text-gray-400">Level</th>
                            <th class="text-center px-4 py-3 text-xs font-bold uppercase tracking-wider text-gray-400">Days Overdue</th>
                            <th class="text-center px-4 py-3 text-xs font-bold uppercase tracking-wider text-gray-400">Earliest Due</th>
                            <th class="text-right px-4 py-3 text-xs font-bold uppercase tracking-wider text-gray-400">Balance</th>
                            <th class="text-center px-4 py-3 text-xs font-bold uppercase tracking-wider text-gray-400">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($bucketRows as $row)
                        <tr class="tbl-row">
                            <td class="px-5 py-3.5">
                                <p class="font-semibold text-gray-800">{{ $row['student']->name }}</p>
                                <p class="text-xs text-gray-400">{{ $row['student']->student_id ?? 'No ID' }}</p>
                            </td>
                            <td class="px-4 py-3.5"><span class="col-badge col-badge-indigo text-xs">{{ $row['student']->level_group ?? '—' }}</span></td>
                            <td class="px-4 py-3.5 text-center font-bold font-mono-num" style="color: {{ $meta['color'] }}">{{ $row['days_overdue'] > 0 ? $row['days_overdue'].' days' : 'Current' }}</td>
                            <td class="px-4 py-3.5 text-center text-gray-700">{{ $row['earliest_due'] ? $row['earliest_due']->format('M j, Y') : '—' }}</td>
                            <td class="px-4 py-3.5 text-right font-bold font-mono-num" style="color: {{ $meta['color'] }}">₱{{ number_format($row['balance'], 2) }}</td>
                            <td class="px-4 py-3.5 text-center">
                                <a href="{{ route('treasurer.soa', $row['student']) }}" class="text-xs font-semibold px-3 py-1.5 rounded-lg bg-indigo-50 text-indigo-500 border border-indigo-100 hover:bg-indigo-100 transition">SOA →</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    @endforeach

    @if($rows->isEmpty())
    <div class="section-card p-10 text-center fade-up">
        <p class="text-4xl mb-3">🎉</p>
        <p class="text-gray-800 font-bold text-lg">No outstanding balances</p>
        <p class="text-sm mt-1 text-gray-400">All students are fully settled for this period.</p>
    </div>
    @endif

</div>
@endsection