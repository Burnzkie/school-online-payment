{{-- resources/views/treasurer/students.blade.php --}}
@extends('treasurer.layouts.treasurer-app')
@section('title', 'Students')
@section('content')
<div class="space-y-6">

    <div class="fade-up">
        <h1 class="text-2xl font-bold text-gray-800">Student Accounts</h1>
        <p class="text-sm mt-1 text-gray-400">Balances for S.Y. {{ $currentYear }} — {{ $students->total() }} students</p>
    </div>

    <div class="section-card p-4 fade-up fade-up-d1">
        <form method="GET" class="flex flex-wrap gap-3 items-end">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Name, Student ID, Email…" class="form-input flex-1 min-w-[180px]">
            <select name="level_group" class="form-input w-auto">
                <option value="">All Levels</option>
                @foreach($levelGroups as $lg)
                    <option value="{{ $lg }}" {{ request('level_group')==$lg?'selected':'' }}>{{ $lg }}</option>
                @endforeach
            </select>
            <select name="payment_status" class="form-input w-auto">
                <option value="">All Payment Status</option>
                <option value="paid"   {{ request('payment_status')=='paid'?'selected':'' }}>Has Payments</option>
                <option value="unpaid" {{ request('payment_status')=='unpaid'?'selected':'' }}>No Payments Yet</option>
            </select>
            <button type="submit" class="btn-primary">🔍 Filter</button>
            <a href="{{ route('treasurer.students') }}" class="btn-secondary">Clear</a>
        </form>
    </div>

    <div class="section-card fade-up fade-up-d2">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100 bg-gray-50">
                        <th class="text-left px-5 py-4 text-xs font-bold uppercase tracking-wider text-gray-400">Student</th>
                        <th class="text-left px-4 py-4 text-xs font-bold uppercase tracking-wider text-gray-400">Level</th>
                        <th class="text-right px-4 py-4 text-xs font-bold uppercase tracking-wider text-gray-400">Total Fees</th>
                        <th class="text-right px-4 py-4 text-xs font-bold uppercase tracking-wider text-gray-400">Paid</th>
                        <th class="text-right px-4 py-4 text-xs font-bold uppercase tracking-wider text-gray-400">Balance</th>
                        <th class="text-center px-4 py-4 text-xs font-bold uppercase tracking-wider text-gray-400">Progress</th>
                        <th class="text-center px-4 py-4 text-xs font-bold uppercase tracking-wider text-gray-400">Detail</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($students as $student)
                    @php
                        $fees = $feesByStudent[$student->id] ?? 0;
                        $paid = $paidByStudent[$student->id] ?? 0;
                        $bal  = max(0, $fees - $paid);
                        $rate = $fees > 0 ? round(($paid / $fees) * 100) : 0;
                    @endphp
                    <tr class="tbl-row">
                        <td class="px-5 py-3.5">
                            <p class="font-semibold text-gray-800">{{ $student->name }}</p>
                            <p class="text-xs text-gray-400">{{ $student->student_id ?? 'No ID' }} · {{ $student->email }}</p>
                        </td>
                        <td class="px-4 py-3.5">
                            <span class="col-badge col-badge-sky text-xs">{{ $student->level_group ?? '—' }}</span>
                            <p class="text-xs mt-1 text-gray-400">{{ $student->year_level ?? '' }}</p>
                        </td>
                        <td class="px-4 py-3.5 text-right font-mono-num text-gray-700">{{ $fees > 0 ? '₱'.number_format($fees, 2) : '—' }}</td>
                        <td class="px-4 py-3.5 text-right font-mono-num font-bold text-indigo-600">{{ $paid > 0 ? '₱'.number_format($paid, 2) : '—' }}</td>
                        <td class="px-4 py-3.5 text-right font-mono-num font-bold" style="color: {{ $bal > 0 ? '#e11d48' : '#16a34a' }}">
                            {{ $bal > 0 ? '₱'.number_format($bal, 2) : 'Settled' }}
                        </td>
                        <td class="px-4 py-3.5 text-center w-32">
                            @if($fees > 0)
                            <div class="progress-bar-track">
                                <div class="progress-bar-fill" style="width: {{ $rate }}%;"></div>
                            </div>
                            <p class="text-xs mt-1 font-bold text-gray-400">{{ $rate }}%</p>
                            @else
                            <span class="text-xs text-gray-300">No fees</span>
                            @endif
                        </td>
                        <td class="px-4 py-3.5 text-center">
                            <a href="{{ route('treasurer.student.detail', $student) }}"
                               class="text-xs font-semibold px-3 py-1.5 rounded-lg bg-indigo-50 text-indigo-500 border border-indigo-100 hover:bg-indigo-100 transition">
                               View →
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="px-5 py-10 text-center text-sm text-gray-400">No students found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($students->hasPages())
        <div class="px-5 py-4 border-t border-gray-100 bg-gray-50">{{ $students->links() }}</div>
        @endif
    </div>
</div>
@endsection