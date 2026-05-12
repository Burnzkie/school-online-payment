{{-- resources/views/treasurer/online-submissions/index.blade.php --}}
@extends('treasurer.layouts.treasurer-app')
@section('title', 'Online Payment Submissions')
@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Online Payment Submissions</h1>
            <p class="text-sm mt-1 text-gray-400">Review GCash / Maya / Bank Transfer submissions from students</p>
        </div>
    </div>

    <div class="grid grid-cols-3 gap-4">
        <div class="stat-card">
            <p class="text-xs uppercase tracking-wider font-bold mb-2 text-gray-400">Pending Review</p>
            <p class="text-xl font-bold font-mono-num text-amber-600">{{ $counts['pending'] }}</p>
        </div>
        <div class="stat-card">
            <p class="text-xs uppercase tracking-wider font-bold mb-2 text-gray-400">Approved</p>
            <p class="text-xl font-bold font-mono-num text-emerald-600">{{ $counts['verified'] }}</p>
        </div>
        <div class="stat-card">
            <p class="text-xs uppercase tracking-wider font-bold mb-2 text-gray-400">Rejected</p>
            <p class="text-xl font-bold font-mono-num text-red-500">{{ $counts['rejected'] }}</p>
        </div>
    </div>

    {{-- Status Tabs --}}
    <div class="flex gap-2 flex-wrap">
        @foreach(['pending' => '⏳ Pending', 'verified' => '✅ Approved', 'rejected' => '❌ Rejected', 'all' => 'All'] as $status => $label)
        <a href="{{ route('treasurer.online-submissions', ['status' => $status]) }}"
           class="px-4 py-2 rounded-xl text-sm font-semibold transition-all border
                  {{ $currentStatus === $status
                     ? 'bg-indigo-600 text-white border-indigo-600'
                     : 'bg-white text-gray-500 border-gray-200 hover:border-indigo-300' }}">
            {{ $label }}
            @if($status !== 'all')
                <span class="ml-1 px-1.5 py-0.5 rounded-full text-xs
                      {{ $currentStatus === $status ? 'bg-white/20 text-white' : 'bg-gray-100 text-gray-500' }}">
                    {{ $counts[$status] ?? 0 }}
                </span>
            @endif
        </a>
        @endforeach
    </div>

    <div class="section-card">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100 bg-gray-50">
                        <th class="text-left px-5 py-4 text-xs font-bold uppercase tracking-wider text-gray-400">Student</th>
                        <th class="text-left px-4 py-4 text-xs font-bold uppercase tracking-wider text-gray-400">Method / Ref</th>
                        <th class="text-left px-4 py-4 text-xs font-bold uppercase tracking-wider text-gray-400">Period</th>
                        <th class="text-right px-4 py-4 text-xs font-bold uppercase tracking-wider text-gray-400">Amount</th>
                        <th class="text-center px-4 py-4 text-xs font-bold uppercase tracking-wider text-gray-400">Status</th>
                        <th class="text-center px-4 py-4 text-xs font-bold uppercase tracking-wider text-gray-400">Submitted</th>
                        <th class="text-center px-4 py-4 text-xs font-bold uppercase tracking-wider text-gray-400">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($submissions as $sub)
                    <tr class="tbl-row">
                        <td class="px-5 py-3.5">
                            <p class="font-semibold text-gray-800">{{ $sub->student->name ?? '—' }}</p>
                            <p class="text-xs text-gray-400">{{ $sub->student->student_id ?? '' }} · {{ $sub->student->level_group ?? '' }}</p>
                        </td>
                        <td class="px-4 py-3.5">
                            <span class="col-badge col-badge-indigo">{{ $sub->payment_method }}</span>
                            <p class="text-xs font-mono text-gray-400 mt-1">{{ $sub->reference_number }}</p>
                        </td>
                        <td class="px-4 py-3.5 text-xs text-gray-500">
                            {{ $sub->school_year }} ·
                            {{ match($sub->semester) { '1' => '1st Sem', '2' => '2nd Sem', 'summer' => 'Summer', default => $sub->semester } }}
                        </td>
                        <td class="px-4 py-3.5 text-right font-bold font-mono-num text-indigo-600">₱{{ number_format($sub->amount, 2) }}</td>
                        <td class="px-4 py-3.5 text-center">
                            @if($sub->status === 'pending')   <span class="col-badge col-badge-amber">Pending</span>
                            @elseif($sub->status === 'verified') <span class="col-badge col-badge-green">Approved</span>
                            @else <span class="col-badge col-badge-red">Rejected</span>
                            @endif
                        </td>
                        <td class="px-4 py-3.5 text-center text-xs text-gray-400">{{ $sub->created_at->format('M d, Y') }}</td>
                        <td class="px-4 py-3.5 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('treasurer.online-submissions.show', $sub) }}"
                                   class="text-xs font-semibold px-3 py-1.5 rounded-lg bg-indigo-50 text-indigo-500 border border-indigo-100 hover:bg-indigo-100 transition">
                                    {{ $sub->isPending() ? 'Review' : 'View' }}
                                </a>
                                @if($sub->isPending())
                                <form method="POST" action="{{ route('treasurer.online-submissions.approve', $sub) }}"
                                      onsubmit="return confirm('Approve ₱{{ number_format($sub->amount,2) }}?')">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="btn-primary text-xs px-3 py-1.5">Approve</button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="px-5 py-10 text-center text-sm text-gray-400">No {{ $currentStatus === 'all' ? '' : $currentStatus }} submissions.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($submissions->hasPages())
        <div class="px-5 py-4 border-t border-gray-100 bg-gray-50">{{ $submissions->withQueryString()->links() }}</div>
        @endif
    </div>
</div>
@endsection