{{-- resources/views/parent/dashboard.blade.php --}}
@extends('parent.layouts.app')

@section('title', 'Parent Dashboard')
@section('page-title', 'Parent Dashboard')
@section('breadcrumb', 'Welcome back, ' . auth()->user()->name . '!')

@section('content')

{{-- ── Stat Cards ── --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">

    <div class="bg-white border border-gray-100 rounded-2xl shadow-sm p-5">
        <div class="flex items-center justify-between mb-3">
            <div class="w-10 h-10 bg-indigo-50 rounded-xl flex items-center justify-center">
                <i class="fas fa-users text-indigo-500"></i>
            </div>
            <span class="text-xs text-gray-400">Children</span>
        </div>
        <p class="text-3xl font-bold text-gray-800">{{ $students->count() }}</p>
        <p class="text-xs text-gray-400 mt-1">Linked student{{ $students->count() != 1 ? 's' : '' }}</p>
    </div>

    <div class="bg-white border border-gray-100 rounded-2xl shadow-sm p-5">
        <div class="flex items-center justify-between mb-3">
            <div class="w-10 h-10 bg-red-50 rounded-xl flex items-center justify-center">
                <i class="fas fa-peso-sign text-red-500"></i>
            </div>
            <span class="text-xs text-gray-400">Balance</span>
        </div>
        <p class="text-3xl font-bold text-gray-800">₱{{ number_format($totalBalance, 2) }}</p>
        <p class="text-xs text-gray-400 mt-1">Total outstanding</p>
    </div>

    <div class="bg-white border border-gray-100 rounded-2xl shadow-sm p-5">
        <div class="flex items-center justify-between mb-3">
            <div class="w-10 h-10 bg-emerald-50 rounded-xl flex items-center justify-center">
                <i class="fas fa-check-circle text-emerald-500"></i>
            </div>
            <span class="text-xs text-gray-400">Paid</span>
        </div>
        <p class="text-3xl font-bold text-gray-800">₱{{ number_format($totalPaid, 2) }}</p>
        <p class="text-xs text-gray-400 mt-1">This semester</p>
    </div>

    <div class="bg-white border border-gray-100 rounded-2xl shadow-sm p-5">
        <div class="flex items-center justify-between mb-3">
            <div class="w-10 h-10 {{ $overdueCount > 0 ? 'bg-amber-50' : 'bg-gray-50' }} rounded-xl flex items-center justify-center">
                <i class="fas fa-exclamation-triangle {{ $overdueCount > 0 ? 'text-amber-500' : 'text-gray-400' }}"></i>
            </div>
            <span class="text-xs text-gray-400">Overdue</span>
        </div>
        <p class="text-3xl font-bold {{ $overdueCount > 0 ? 'text-amber-500' : 'text-gray-800' }}">{{ $overdueCount }}</p>
        <p class="text-xs text-gray-400 mt-1">Missed installment{{ $overdueCount != 1 ? 's' : '' }}</p>
    </div>

</div>

{{-- ── Upcoming Due Banner ── --}}
@if($upcomingDue)
<div class="bg-indigo-50 border-l-4 border border-indigo-100 rounded-2xl p-5 mb-8 flex flex-col sm:flex-row items-start sm:items-center gap-4"
     style="border-left-color: #4f46e5;">
    <div class="w-12 h-12 bg-indigo-100 rounded-xl flex items-center justify-center flex-shrink-0">
        <i class="fas fa-calendar-alt text-indigo-500 text-xl"></i>
    </div>
    <div class="flex-1">
        <p class="font-semibold text-gray-800">Next Payment Due</p>
        <p class="text-gray-500 text-sm mt-0.5">
            <span class="text-gray-800 font-semibold">₱{{ number_format($upcomingDue->amount_due, 2) }}</span>
            due on
            <span class="text-indigo-600 font-semibold">{{ \Carbon\Carbon::parse($upcomingDue->due_date)->format('F j, Y') }}</span>
        </p>
    </div>
    <span class="text-xs px-3 py-1.5 rounded-full font-semibold {{ \Carbon\Carbon::parse($upcomingDue->due_date)->diffInDays() <= 7 ? 'bg-amber-50 text-amber-600 border border-amber-200' : 'bg-white text-gray-500 border border-gray-200' }}">
        {{ \Carbon\Carbon::parse($upcomingDue->due_date)->diffForHumans() }}
    </span>
</div>
@endif

{{-- ── No students linked ── --}}
@if($students->isEmpty())
<div class="bg-white border border-gray-100 rounded-3xl shadow-sm p-12 text-center">
    <div class="w-20 h-20 bg-indigo-50 rounded-full flex items-center justify-center mx-auto mb-6">
        <i class="fas fa-user-graduate text-4xl text-indigo-400"></i>
    </div>
    <h3 class="text-xl font-bold mb-3 text-gray-800">No Children Linked</h3>
    <p class="text-gray-400 max-w-md mx-auto text-sm leading-relaxed">
        Your parent account is not yet linked to any students. Please contact the school registrar
        to ensure your contact information matches your child's enrollment records.
    </p>
    <div class="mt-6 p-4 bg-gray-50 border border-gray-100 rounded-2xl inline-block text-sm text-gray-500">
        <i class="fas fa-info-circle text-indigo-400 mr-2"></i>
        Make sure your <strong class="text-gray-700">phone number</strong> matches what's on file.
    </div>
</div>
@else

{{-- ── Student Cards ── --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    @foreach($studentSummaries as $summary)
    @php $s = $summary['student']; @endphp
    <div class="bg-white border border-gray-100 rounded-2xl shadow-sm p-6 hover:border-indigo-200 transition-colors">

        {{-- Student header --}}
        <div class="flex items-center gap-4 mb-5">
            @if($s->profile_picture)
                <img src="{{ asset('storage/'.$s->profile_picture) }}" class="w-14 h-14 rounded-2xl object-cover ring-2 ring-indigo-100">
            @else
                <div class="w-14 h-14 rounded-2xl flex items-center justify-center text-xl font-bold text-white"
                     style="background: linear-gradient(135deg, #4f46e5, #6366f1);">
                    {{ strtoupper(substr($s->name, 0, 1)) }}
                </div>
            @endif
            <div class="flex-1 min-w-0">
                <p class="font-bold text-lg truncate text-gray-800">{{ $s->name }} {{ $s->last_name }}</p>
                <p class="text-sm text-gray-400">{{ $s->year_level }} &bull; {{ $s->level_group }}</p>
                @if($s->student_id)
                <p class="text-xs text-gray-400 mt-0.5 font-mono">ID: {{ $s->student_id }}</p>
                @endif
            </div>
            @if($summary['overdue'] > 0)
            <span class="flex-shrink-0 px-2.5 py-1 text-xs font-bold rounded-full bg-amber-50 text-amber-600 border border-amber-200">
                {{ $summary['overdue'] }} overdue
            </span>
            @elseif($summary['balance'] == 0)
            <span class="flex-shrink-0 px-2.5 py-1 text-xs font-bold rounded-full bg-emerald-50 text-emerald-600 border border-emerald-200">
                Fully Paid
            </span>
            @endif
        </div>

        {{-- Progress bar --}}
        <div class="mb-5">
            <div class="flex justify-between text-xs text-gray-400 mb-2">
                <span>Payment Progress</span>
                <span class="font-semibold text-indigo-600">{{ $summary['progress'] }}%</span>
            </div>
            <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                <div class="h-full rounded-full transition-all"
                     style="width: {{ $summary['progress'] }}%; background: linear-gradient(90deg, #4f46e5, #6366f1);"></div>
            </div>
            <div class="flex justify-between text-xs mt-2">
                <span class="text-emerald-600 font-medium">₱{{ number_format($summary['paid'], 2) }} paid</span>
                <span class="text-red-500 font-medium">₱{{ number_format($summary['balance'], 2) }} remaining</span>
            </div>
        </div>

        {{-- Recent payments --}}
        @if($summary['recent_payments']->isNotEmpty())
        <div class="mb-5 space-y-2">
            <p class="text-xs text-gray-400 font-bold uppercase tracking-wider">Recent Payments</p>
            @foreach($summary['recent_payments'] as $pmt)
            <div class="flex items-center justify-between text-sm">
                <div class="flex items-center gap-2">
                    <div class="w-6 h-6 rounded-lg bg-emerald-50 flex items-center justify-center">
                        <i class="fas fa-check text-emerald-500 text-xs"></i>
                    </div>
                    <span class="text-gray-400">{{ \Carbon\Carbon::parse($pmt->payment_date)->format('M d') }}</span>
                    <span class="text-xs text-gray-300">&bull; {{ $pmt->payment_method }}</span>
                </div>
                <span class="font-semibold text-emerald-600">₱{{ number_format($pmt->amount, 2) }}</span>
            </div>
            @endforeach
        </div>
        @endif

        {{-- Next due --}}
        @if($summary['next_due'])
        <div class="mb-5 flex items-center gap-3 bg-indigo-50 border border-indigo-100 rounded-xl px-4 py-3 text-sm">
            <i class="fas fa-calendar text-indigo-400"></i>
            <span class="text-gray-400">Next due:</span>
            <span class="font-semibold text-indigo-600">
                ₱{{ number_format($summary['next_due']->amount_due, 2) }}
            </span>
            <span class="text-gray-400">&bull; {{ \Carbon\Carbon::parse($summary['next_due']->due_date)->format('M d, Y') }}</span>
        </div>
        @endif

        {{-- Actions --}}
        <div class="flex gap-2 flex-wrap">
            <a href="{{ route('parent.student.detail', $s) }}"
               class="flex-1 text-center text-white text-sm font-semibold py-2.5 px-4 rounded-xl transition shadow-sm"
               style="background: linear-gradient(135deg, #4f46e5, #6366f1);">
                <i class="fas fa-file-invoice-dollar mr-1.5"></i> Billing
            </a>
            <a href="{{ route('parent.student.payments', $s) }}"
               class="flex-1 text-center bg-white hover:bg-gray-50 border border-gray-200 text-gray-600 text-sm font-medium py-2.5 px-4 rounded-xl transition">
                <i class="fas fa-history mr-1.5"></i> Payments
            </a>
            <a href="{{ route('parent.student.statement', $s) }}"
               class="flex-1 text-center bg-white hover:bg-gray-50 border border-gray-200 text-gray-600 text-sm font-medium py-2.5 px-4 rounded-xl transition">
                <i class="fas fa-receipt mr-1.5"></i> Statement
            </a>
        </div>

    </div>
    @endforeach
</div>
@endif

@endsection