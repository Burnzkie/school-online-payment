{{-- resources/views/admin/dashboard.blade.php --}}
@extends('admin.layouts.admin-app')
@section('title', 'Dashboard')

@section('content')
@php
    $rate = min(100, $collectionRate);
@endphp

{{-- ── Hero ── --}}
<div class="relative overflow-hidden rounded-3xl a-fade bg-white border border-gray-100 shadow-sm">
    <div class="absolute -top-20 -right-20 w-80 h-80 rounded-full pointer-events-none"
         style="background: radial-gradient(circle, rgba(79,70,229,0.08) 0%, transparent 65%)"></div>
    <div class="absolute -bottom-16 -left-16 w-56 h-56 rounded-full pointer-events-none"
         style="background: radial-gradient(circle, rgba(99,102,241,0.06) 0%, transparent 65%)"></div>

    <div class="relative px-7 py-8 sm:px-10">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-5">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-semibold mb-4 bg-indigo-50 text-indigo-600 border border-indigo-200">
                    <span class="w-1.5 h-1.5 rounded-full a-pulse bg-indigo-500"></span>
                    {{ now()->format('l, F j, Y') }}
                </div>
                <h1 class="font-bold text-3xl sm:text-4xl text-gray-800 leading-tight">
                    Welcome back,<br>
                    <span class="text-indigo-600">{{ explode(' ', auth()->user()->name)[0] }}!</span>
                </h1>
                <p class="mt-2 text-sm text-gray-400">Administrator &nbsp;·&nbsp; Philippine Advent College</p>
            </div>

            {{-- Collection ring --}}
            <div class="flex-shrink-0 flex flex-col items-center">
                <svg width="110" height="110" viewBox="0 0 110 110">
                    <circle cx="55" cy="55" r="46" fill="none" stroke="#e0e7ff" stroke-width="9"/>
                    <circle cx="55" cy="55" r="46" fill="none" stroke="url(#ag)" stroke-width="9"
                            stroke-dasharray="{{ 2*M_PI*46 }}"
                            stroke-dashoffset="{{ 2*M_PI*46 * (1 - $rate/100) }}"
                            stroke-linecap="round"
                            transform="rotate(-90 55 55)"/>
                    <defs>
                        <linearGradient id="ag" x1="0" y1="0" x2="1" y2="1">
                            <stop offset="0%" stop-color="#4f46e5"/>
                            <stop offset="100%" stop-color="#6366f1"/>
                        </linearGradient>
                    </defs>
                    <text x="55" y="50" text-anchor="middle" fill="#4f46e5" font-size="16" font-weight="800" font-family="JetBrains Mono, monospace">{{ $rate }}%</text>
                    <text x="55" y="66" text-anchor="middle" fill="#9ca3af" font-size="9" font-weight="600">COLLECTED</text>
                </svg>
                <p class="text-xs text-gray-400 mt-1">SY {{ $currentYear }}</p>
            </div>
        </div>
    </div>
</div>

{{-- ── Key Stats ── --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 a-fade a-d1">
    @foreach([
        ['label'=>"Today's Revenue",  'value'=>'₱'.number_format($todayRevenue,2),   'sub'=>'Collected today',             'icon'=>'💵', 'color'=>'#4f46e5', 'bg'=>'#eef2ff'],
        ['label'=>'Monthly Revenue',  'value'=>'₱'.number_format($monthlyRevenue,2),'sub'=>now()->format('F Y'),           'icon'=>'📈', 'color'=>'#059669', 'bg'=>'#ecfdf5'],
        ['label'=>'Total Students',   'value'=>number_format($totalStudents),        'sub'=>'All enrolled',                 'icon'=>'🎓', 'color'=>'#0ea5e9', 'bg'=>'#f0f9ff'],
        ['label'=>'Outstanding',      'value'=>'₱'.number_format($outstanding,2),   'sub'=>'Uncollected fees',             'icon'=>'⚠️', 'color'=>'#d97706', 'bg'=>'#fffbeb'],
    ] as $s)
    <div class="a-stat-card a-fade">
        <div class="flex items-start gap-3">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center text-lg flex-shrink-0"
                 style="background: {{ $s['bg'] }}; border: 1px solid {{ $s['color'] }}22;">{{ $s['icon'] }}</div>
            <div class="min-w-0">
                <p class="text-xl font-bold text-gray-800 font-mono-num leading-tight">{{ $s['value'] }}</p>
                <p class="text-xs font-semibold mt-0.5 text-gray-500">{{ $s['label'] }}</p>
                <p class="text-[11px] mt-0.5 text-gray-400">{{ $s['sub'] }}</p>
            </div>
        </div>
    </div>
    @endforeach
</div>

{{-- ── Secondary Stats ── --}}
<div class="grid grid-cols-2 sm:grid-cols-4 gap-4 a-fade a-d2">
    @foreach([
        ['l'=>'Pending Payments',    'v'=>$pendingPayments,    'c'=>'#d97706'],
        ['l'=>'On Hold Students',    'v'=>$onHoldCount,        'c'=>'#e11d48'],
        ['l'=>'Active Scholarships', 'v'=>$activeScholarships, 'c'=>'#4f46e5'],
    ] as $s)
    <div class="a-card px-5 py-4">
        <p class="text-2xl font-bold text-gray-800 font-mono-num">{{ $s['v'] }}</p>
        <p class="text-xs mt-1 font-semibold" style="color: {{ $s['c'] }};">{{ $s['l'] }}</p>
    </div>
    @endforeach
</div>

{{-- ── Chart + Payment Methods ── --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 a-fade a-d3">

    {{-- Monthly Bar Chart --}}
    <div class="a-card p-6 lg:col-span-2">
        <div class="flex items-center justify-between mb-5">
            <div>
                <h3 class="font-bold text-gray-800 text-base">Monthly Collections</h3>
                <p class="text-xs mt-0.5 text-gray-400">Last 7 months · completed payments</p>
            </div>
            <span class="a-badge a-badge-violet">SY {{ $currentYear }}</span>
        </div>
        @php $maxChart = max(array_column($monthlyChart, 'amount')) ?: 1; @endphp
        <div class="flex items-end gap-2 h-36">
            @foreach($monthlyChart as $m)
            @php $h = max(4, round(($m['amount']/$maxChart)*100)); @endphp
            <div class="flex-1 flex flex-col items-center gap-1.5">
                <div class="w-full relative flex items-end" style="height:100px;">
                    <div class="w-full rounded-t-lg transition-all duration-500"
                         style="height:{{ $h }}%; background: linear-gradient(180deg, #4f46e5, rgba(79,70,229,0.3));"
                         title="₱{{ number_format($m['amount'],2) }}"></div>
                </div>
                <span class="text-[10px] text-gray-400">{{ $m['month'] }}</span>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Payment Methods --}}
    <div class="a-card p-6">
        <h3 class="font-bold text-gray-800 text-base mb-4">Payment Methods</h3>
        @php $totalMethods = $paymentMethods->sum('total') ?: 1; @endphp
        <div class="space-y-4">
            @forelse($paymentMethods as $m)
            @php $pct = round(($m->total / $totalMethods) * 100); @endphp
            <div>
                <div class="flex justify-between text-sm mb-1.5">
                    <span class="font-semibold text-gray-700">{{ $m->payment_method }}</span>
                    <span class="text-gray-400">{{ $pct }}%</span>
                </div>
                <div class="a-progress-track">
                    <div class="a-progress-fill" style="width:{{ $pct }}%"></div>
                </div>
                <p class="text-xs mt-1 text-gray-400">{{ $m->count }} txns · ₱{{ number_format($m->total,2) }}</p>
            </div>
            @empty
            <p class="text-sm text-gray-400">No payment data yet.</p>
            @endforelse
        </div>

        {{-- Revenue by level --}}
        <div class="mt-5 pt-4 border-t border-gray-100">
            <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">By Level Group</h4>
            @foreach($revenueByLevel as $level => $r)
            <div class="flex justify-between text-xs mb-1.5">
                <span class="text-gray-500">{{ $level ?: 'Unknown' }}</span>
                <span class="font-semibold text-gray-800">₱{{ number_format($r->total,0) }}</span>
            </div>
            @endforeach
        </div>
    </div>
</div>

{{-- ── Clearance + User Roles ── --}}
<div class="grid grid-cols-1 sm:grid-cols-2 gap-6 a-fade a-d4">
    <div class="a-card p-6">
        <h3 class="font-bold text-gray-800 text-base mb-4">Finance Clearances</h3>
        <div class="space-y-3">
            <div class="flex items-center justify-between">
                <span class="text-sm text-gray-600">Cleared Students</span>
                <span class="font-bold text-gray-800">{{ $clearedCount }}</span>
            </div>
            <div class="a-progress-track">
                @php $total = max(1,$clearedCount+$onHoldCount); @endphp
                <div class="a-progress-fill" style="width:{{ round($clearedCount/$total*100) }}%;background:linear-gradient(90deg,#059669,#34d399);"></div>
            </div>
            <div class="flex items-center justify-between text-sm">
                <span class="text-gray-500">On Hold: <span class="text-red-500 font-bold">{{ $onHoldCount }}</span></span>
                <span class="text-gray-400">{{ round($clearedCount/$total*100) }}% cleared</span>
            </div>
        </div>
    </div>

    <div class="a-card p-6">
        <h3 class="font-bold text-gray-800 text-base mb-4">User Distribution</h3>
        <div class="space-y-2.5">
            @foreach(['student'=>'🎓','cashier'=>'💵','treasurer'=>'📋','parent'=>'👨‍👩‍👧','admin'=>'🛡️'] as $role=>$icon)
            <div class="flex items-center justify-between">
                <span class="text-sm flex items-center gap-2 text-gray-600">
                    {{ $icon }} {{ ucfirst($role) }}
                </span>
                <span class="font-bold text-gray-800">{{ $roleCounts[$role]->total ?? 0 }}</span>
            </div>
            @endforeach
        </div>
    </div>
</div>

{{-- ── Recent Payments ── --}}
<div class="a-card a-fade a-d5">
    <div class="px-6 py-4 flex items-center justify-between border-b border-gray-100">
        <div>
            <h3 class="font-bold text-gray-800">Recent Transactions</h3>
            <p class="text-xs mt-0.5 text-gray-400">Latest completed payments across all portals</p>
        </div>
        <a href="{{ route('admin.payments') }}" class="a-btn-secondary text-xs">View all →</a>
    </div>
    <div class="overflow-x-auto">
        <table class="a-table">
            <thead>
                <tr>
                    <th>Student</th><th>Amount</th><th>Method</th><th>OR Number</th><th>Date</th><th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentPayments as $p)
                <tr>
                    <td>
                        <p class="font-semibold text-gray-800">{{ $p->student->name ?? '—' }}</p>
                        <p class="text-xs text-gray-400">{{ $p->student->student_id ?? '' }}</p>
                    </td>
                    <td class="font-bold font-mono-num text-indigo-600">₱{{ number_format($p->amount,2) }}</td>
                    <td><span class="a-badge a-badge-sky">{{ $p->payment_method }}</span></td>
                    <td class="font-mono-num text-xs text-gray-400">{{ $p->or_number ?? '—' }}</td>
                    <td class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($p->payment_date)->format('M d, Y') }}</td>
                    <td>
                        <span class="a-badge {{ $p->status==='completed' ? 'a-badge-emerald' : ($p->status==='pending' ? 'a-badge-amber' : 'a-badge-red') }}">
                            {{ ucfirst($p->status) }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center py-10 text-gray-400">No transactions yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection