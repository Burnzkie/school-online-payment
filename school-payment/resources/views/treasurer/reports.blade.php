{{-- resources/views/treasurer/reports.blade.php --}}
@extends('treasurer.layouts.treasurer-app')
@section('title', 'Reports')
@section('content')
<div class="space-y-6">

    <div class="fade-up">
        <h1 class="text-2xl font-bold text-gray-800">Financial Reports</h1>
        <p class="text-sm mt-1 text-gray-400">Collection rates, payment methods, and fee breakdown</p>
    </div>

    {{-- Filters --}}
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

    {{-- Collection Rate by Level Group --}}
    <div class="section-card fade-up fade-up-d2">
        <div class="px-5 py-4 border-b border-gray-100 bg-gray-50">
            <h3 class="text-base font-bold text-gray-800">Collection Rate by Level Group</h3>
            <p class="text-xs mt-0.5 text-gray-400">{{ $schoolYear }} · Semester {{ $semester }}</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100 bg-gray-50">
                        <th class="text-left px-5 py-3 text-xs font-bold uppercase tracking-wider text-gray-400">Level Group</th>
                        <th class="text-right px-4 py-3 text-xs font-bold uppercase tracking-wider text-gray-400">Students</th>
                        <th class="text-right px-4 py-3 text-xs font-bold uppercase tracking-wider text-gray-400">Total Fees</th>
                        <th class="text-right px-4 py-3 text-xs font-bold uppercase tracking-wider text-gray-400">Collected</th>
                        <th class="text-right px-4 py-3 text-xs font-bold uppercase tracking-wider text-gray-400">Outstanding</th>
                        <th class="text-center px-4 py-3 text-xs font-bold uppercase tracking-wider text-gray-400">Rate</th>
                    </tr>
                </thead>
                <tbody>
                    @php $grandTotal = 0; $grandPaid = 0; @endphp
                    @forelse($collectionByLevel as $row)
                    @php $grandTotal += $row['total_fees']; $grandPaid += $row['total_paid']; @endphp
                    <tr class="tbl-row">
                        <td class="px-5 py-3.5 font-semibold text-gray-800">{{ $row['level_group'] ?: 'Unassigned' }}</td>
                        <td class="px-4 py-3.5 text-right text-gray-700">{{ $row['students'] }}</td>
                        <td class="px-4 py-3.5 text-right font-mono-num text-gray-700">₱{{ number_format($row['total_fees'], 2) }}</td>
                        <td class="px-4 py-3.5 text-right font-mono-num font-bold text-indigo-600">₱{{ number_format($row['total_paid'], 2) }}</td>
                        <td class="px-4 py-3.5 text-right font-mono-num font-bold" style="color: {{ $row['outstanding']>0?'#e11d48':'#16a34a' }}">
                            {{ $row['outstanding'] > 0 ? '₱'.number_format($row['outstanding'],2) : 'Settled' }}
                        </td>
                        <td class="px-4 py-3.5 text-center">
                            <div class="flex flex-col items-center gap-1">
                                <span class="text-xs font-bold" style="color: {{ $row['rate']>=80?'#16a34a':($row['rate']>=50?'#d97706':'#e11d48') }}">{{ $row['rate'] }}%</span>
                                <div class="progress-bar-track w-20">
                                    <div class="progress-bar-fill" style="width: {{ $row['rate'] }}%; background: {{ $row['rate']>=80?'linear-gradient(90deg,#16a34a,#22c55e)':($row['rate']>=50?'linear-gradient(90deg,#d97706,#f59e0b)':'linear-gradient(90deg,#e11d48,#f43f5e)') }};"></div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="px-5 py-10 text-center text-sm text-gray-400">No data for this period.</td></tr>
                    @endforelse
                    @if(count($collectionByLevel) > 0)
                    <tr class="border-t-2 border-gray-200 bg-gray-50">
                        <td class="px-5 py-3.5 font-bold text-gray-800">TOTAL</td>
                        <td class="px-4 py-3.5"></td>
                        <td class="px-4 py-3.5 text-right font-bold font-mono-num text-gray-800">₱{{ number_format($grandTotal, 2) }}</td>
                        <td class="px-4 py-3.5 text-right font-bold font-mono-num text-indigo-600">₱{{ number_format($grandPaid, 2) }}</td>
                        <td class="px-4 py-3.5 text-right font-bold font-mono-num text-red-500">₱{{ number_format(max(0,$grandTotal-$grandPaid), 2) }}</td>
                        <td class="px-4 py-3.5 text-center font-bold text-gray-800">{{ $grandTotal > 0 ? round(($grandPaid/$grandTotal)*100,1) : 0 }}%</td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 fade-up fade-up-d3">

        {{-- Monthly Trend --}}
        <div class="section-card p-5">
            <h3 class="text-sm font-bold text-gray-800 mb-4">Monthly Revenue Trend — S.Y. {{ $schoolYear }}</h3>
            @php $maxT = max(max(array_column($trend, 'amount')), 1); @endphp
            <div class="flex items-end gap-1.5 h-36">
                @foreach($trend as $t)
                <div class="flex-1 flex flex-col items-center gap-1">
                    @if($t['amount'] > 0)
                    <span class="text-xs font-mono-num text-gray-400">{{ number_format($t['amount']/1000,0) }}k</span>
                    @endif
                    <div class="w-full rounded-t-md" style="height: {{ max(4, round(($t['amount']/$maxT)*112)) }}px; background: linear-gradient(180deg,#4f46e5,#6366f1); opacity: 0.75;"></div>
                    <span class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($t['label'])->format('M') }}</span>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Payment Methods --}}
        <div class="section-card p-5">
            <h3 class="text-sm font-bold text-gray-800 mb-4">Payment Methods — S.Y. {{ $schoolYear }}</h3>
            <div class="space-y-3">
                @php $maxM = $paymentMethods->max('total') ?: 1; @endphp
                @forelse($paymentMethods as $pm)
                <div>
                    <div class="flex items-center justify-between text-xs mb-1">
                        <span class="font-semibold text-gray-700">{{ $pm->payment_method }}</span>
                        <span class="font-mono-num font-bold text-indigo-600">
                            ₱{{ number_format($pm->total, 0) }}
                            <span class="text-gray-400 font-normal">({{ $pm->count }})</span>
                        </span>
                    </div>
                    <div class="progress-bar-track">
                        <div class="progress-bar-fill" style="width: {{ round(($pm->total/$maxM)*100) }}%;"></div>
                    </div>
                </div>
                @empty
                <p class="text-sm text-gray-400">No payment data yet.</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Fee Type Breakdown --}}
    <div class="section-card fade-up fade-up-d4">
        <div class="px-5 py-4 border-b border-gray-100 bg-gray-50">
            <h3 class="text-base font-bold text-gray-800">Fee Type Breakdown</h3>
            <p class="text-xs mt-0.5 text-gray-400">{{ $schoolYear }} · Semester {{ $semester }}</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100 bg-gray-50">
                        <th class="text-left px-5 py-3 text-xs font-bold uppercase tracking-wider text-gray-400">Fee Type</th>
                        <th class="text-right px-4 py-3 text-xs font-bold uppercase tracking-wider text-gray-400">Students</th>
                        <th class="text-right px-4 py-3 text-xs font-bold uppercase tracking-wider text-gray-400">Total Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($feeTypes as $ft)
                    <tr class="tbl-row">
                        <td class="px-5 py-3.5 font-semibold text-gray-800">{{ $ft->fee_name }}</td>
                        <td class="px-4 py-3.5 text-right text-gray-700">{{ $ft->count }}</td>
                        <td class="px-4 py-3.5 text-right font-bold font-mono-num text-indigo-600">₱{{ number_format($ft->total, 2) }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="3" class="px-5 py-8 text-center text-sm text-gray-400">No fees for this period.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection