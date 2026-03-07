{{-- resources/views/cashier/students.blade.php --}}
@extends('cashier.layouts.cashier-app')
@section('title', 'Student Lookup')

@section('content')
<div class="space-y-6">

    {{-- ── Page Header ── --}}
    <div class="c-fade flex flex-col sm:flex-row sm:items-end justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider mb-3 bg-emerald-50 text-emerald-600 border border-emerald-200">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                Student Management
            </div>
            <h1 class="font-display text-4xl sm:text-5xl text-gray-800 leading-tight">Student Lookup</h1>
            <p class="text-sm mt-1.5 text-gray-400">Search students and manage their fees and payments.</p>
        </div>
        <a href="{{ route('cashier.receive-payment') }}"
           class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl font-bold text-white text-sm transition-all hover:scale-[1.02] shadow-sm"
           style="background: linear-gradient(135deg, #4f46e5, #6366f1);">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
            </svg>
            Receive Payment
        </a>
    </div>

    {{-- ── Search & Filters ── --}}
    <form method="GET" action="{{ route('cashier.students') }}" class="c-fade c-d1">
        <div class="bg-white rounded-2xl p-4 flex flex-col sm:flex-row gap-3 border border-gray-100 shadow-sm">
            <div class="flex-1 relative">
                <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 pointer-events-none text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" name="q" value="{{ request('q') }}"
                       placeholder="Search by name, student ID, or email..."
                       class="c-input pl-10">
            </div>
            <select name="level_group" class="c-input c-select" style="width: auto; min-width: 160px;">
                <option value="">All Levels</option>
                <option value="Kinder"             {{ request('level_group') === 'Kinder'             ? 'selected' : '' }}>Kinder</option>
                <option value="Elementary"         {{ request('level_group') === 'Elementary'         ? 'selected' : '' }}>Elementary</option>
                <option value="Junior High School" {{ request('level_group') === 'Junior High School' ? 'selected' : '' }}>Junior High</option>
                <option value="Senior High School" {{ request('level_group') === 'Senior High School' ? 'selected' : '' }}>Senior High</option>
                <option value="College"            {{ request('level_group') === 'College'            ? 'selected' : '' }}>College</option>
            </select>
            <select name="balance_filter" class="c-input c-select" style="width: auto; min-width: 160px;">
                <option value="">All Students</option>
                <option value="with_balance" {{ request('balance_filter') === 'with_balance' ? 'selected' : '' }}>With Balance</option>
                <option value="fully_paid"   {{ request('balance_filter') === 'fully_paid'   ? 'selected' : '' }}>Fully Paid</option>
            </select>
            <button type="submit"
                    class="px-5 py-2.5 rounded-xl font-bold text-white text-sm hover:opacity-90 transition-all shadow-sm whitespace-nowrap"
                    style="background: linear-gradient(135deg, #4f46e5, #6366f1);">
                Search
            </button>
        </div>
    </form>

    {{-- ── Student Table ── --}}
    <div class="bg-white rounded-2xl overflow-hidden border border-gray-100 shadow-sm c-fade c-d2">
        <div class="px-5 py-4 border-b border-gray-100 bg-gray-50 flex items-center justify-between">
            <h2 class="font-bold text-gray-800">
                Students
                <span class="ml-2 font-mono-num text-sm font-normal text-gray-400">
                    ({{ $students->total() ?? count($students ?? []) }})
                </span>
            </h2>
            @if(request('q') || request('level_group') || request('balance_filter'))
            <a href="{{ route('cashier.students') }}" class="text-xs font-semibold text-gray-400 hover:text-gray-600">Clear filters</a>
            @endif
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[700px]">
                <thead>
                    <tr class="border-b border-gray-100 bg-gray-50">
                        <th class="px-5 py-3 text-left text-[10px] font-bold uppercase tracking-widest text-gray-400">Student</th>
                        <th class="px-5 py-3 text-left text-[10px] font-bold uppercase tracking-widest text-gray-400">ID</th>
                        <th class="px-5 py-3 text-left text-[10px] font-bold uppercase tracking-widest text-gray-400">Level / Year</th>
                        <th class="px-5 py-3 text-right text-[10px] font-bold uppercase tracking-widest text-gray-400">Total Fees</th>
                        <th class="px-5 py-3 text-right text-[10px] font-bold uppercase tracking-widest text-gray-400">Total Paid</th>
                        <th class="px-5 py-3 text-right text-[10px] font-bold uppercase tracking-widest text-gray-400">Balance</th>
                        <th class="px-5 py-3 text-right text-[10px] font-bold uppercase tracking-widest text-gray-400">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($students ?? [] as $student)
                    @php
                        $sy = date('Y').'-'.(date('Y')+1);
                        $totalFees = $student->fees()->where('school_year', $sy)->where('status', 'active')->sum('amount');
                        $totalPaid = $student->payments()->where('school_year', $sy)->where('status', 'completed')->sum('amount');
                        $balance   = max(0, $totalFees - $totalPaid);
                    @endphp
                    <tr class="c-row border-b border-gray-50">
                        <td class="px-5 py-3.5">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg flex items-center justify-center text-xs font-bold text-white flex-shrink-0"
                                     style="background: linear-gradient(135deg, #4f46e5, #6366f1);">
                                    {{ strtoupper(substr($student->name, 0, 1)) }}
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-gray-800">{{ $student->name }}</p>
                                    <p class="text-xs mt-0.5 text-gray-400">{{ $student->email }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-3.5 font-mono-num text-sm text-gray-500">
                            {{ $student->student_id ?? '—' }}
                        </td>
                        <td class="px-5 py-3.5">
                            <p class="text-sm text-gray-700">{{ $student->level_group ?? '—' }}</p>
                            <p class="text-xs mt-0.5 text-gray-400">{{ $student->year_level ?? '' }}</p>
                        </td>
                        <td class="px-5 py-3.5 text-right font-mono-num text-sm text-gray-600">
                            ₱{{ number_format($totalFees, 2) }}
                        </td>
                        <td class="px-5 py-3.5 text-right font-mono-num text-sm text-emerald-600">
                            ₱{{ number_format($totalPaid, 2) }}
                        </td>
                        <td class="px-5 py-3.5 text-right">
                            <span class="font-mono-num font-bold text-sm {{ $balance > 0 ? 'text-red-500' : 'text-emerald-500' }}">
                                ₱{{ number_format($balance, 2) }}
                            </span>
                        </td>
                        <td class="px-5 py-3.5 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('cashier.student-ledger', $student->id) }}"
                                   class="text-xs font-semibold px-3 py-1.5 rounded-lg bg-indigo-50 text-indigo-500 border border-indigo-100 hover:bg-indigo-100 transition-colors">
                                    Ledger
                                </a>
                                @if($balance > 0)
                                <a href="{{ route('cashier.receive-payment', ['student_id' => $student->id]) }}"
                                   class="text-xs font-semibold px-3 py-1.5 rounded-lg text-white shadow-sm transition-all hover:opacity-90"
                                   style="background: linear-gradient(135deg, #4f46e5, #6366f1);">
                                    Pay
                                </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-5 py-16 text-center">
                            <div class="text-4xl mb-3">👥</div>
                            <p class="text-sm font-semibold text-gray-400">No students found</p>
                            <p class="text-xs mt-1 text-gray-300">Try adjusting your search or filters.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(method_exists($students ?? collect(), 'links') && ($students->hasPages() ?? false))
        <div class="px-5 py-4 bg-gray-50 border-t border-gray-100">
            {{ $students->withQueryString()->links() }}
        </div>
        @endif
    </div>

</div>
@endsection