{{-- resources/views/admin/online-submissions/index.blade.php --}}
@extends('admin.layouts.admin-app')
@section('title', 'Online Payment Submissions')

@section('content')

<div class="flex items-center justify-between a-fade">
    <div>
        <h2 class="text-xl font-bold text-gray-800">Online Payment Submissions</h2>
        <p class="text-sm mt-0.5 text-gray-400">Review GCash / Maya / Bank Transfer submissions from students</p>
    </div>
</div>

{{-- Stats --}}
<div class="grid grid-cols-3 gap-4 a-fade a-d1">
    <div class="a-card px-5 py-4">
        <p class="text-2xl font-bold text-amber-500">{{ $counts['pending'] }}</p>
        <p class="text-xs mt-1 font-semibold text-gray-500">Pending Review</p>
    </div>
    <div class="a-card px-5 py-4">
        <p class="text-2xl font-bold text-emerald-600">{{ $counts['verified'] }}</p>
        <p class="text-xs mt-1 font-semibold text-gray-500">Approved</p>
    </div>
    <div class="a-card px-5 py-4">
        <p class="text-2xl font-bold text-red-500">{{ $counts['rejected'] }}</p>
        <p class="text-xs mt-1 font-semibold text-gray-500">Rejected</p>
    </div>
</div>

{{-- Status Tabs --}}
<div class="flex gap-2 flex-wrap a-fade a-d2">
    @foreach(['pending' => '⏳ Pending', 'verified' => '✅ Approved', 'rejected' => '❌ Rejected', 'all' => 'All'] as $status => $label)
    <a href="{{ route('admin.online-submissions', ['status' => $status]) }}"
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

{{-- Table --}}
<div class="a-card a-fade a-d3">
    <div class="overflow-x-auto">
        <table class="a-table">
            <thead>
                <tr>
                    <th>Student</th>
                    <th>Method / Reference</th>
                    <th>Period</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Submitted</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($submissions as $sub)
                <tr>
                    <td>
                        <p class="font-semibold text-gray-800 text-sm">{{ $sub->student->name ?? '—' }}</p>
                        <p class="text-xs text-gray-400">{{ $sub->student->student_id ?? '' }} · {{ $sub->student->level_group ?? '' }}</p>
                    </td>
                    <td>
                        <span class="a-badge a-badge-sky">{{ $sub->payment_method }}</span>
                        <p class="text-xs font-mono text-gray-400 mt-1">{{ $sub->reference_number }}</p>
                    </td>
                    <td class="text-xs text-gray-500">
                        {{ $sub->school_year }}<br>
                        {{ match($sub->semester) { '1' => '1st Sem', '2' => '2nd Sem', 'summer' => 'Summer', default => $sub->semester } }}
                    </td>
                    <td class="font-bold font-mono-num text-indigo-600">₱{{ number_format($sub->amount, 2) }}</td>
                    <td>
                        @if($sub->status === 'pending')   <span class="a-badge a-badge-amber">Pending</span>
                        @elseif($sub->status === 'verified') <span class="a-badge a-badge-emerald">Approved</span>
                        @else <span class="a-badge a-badge-red">Rejected</span>
                        @endif
                    </td>
                    <td class="text-xs text-gray-400">{{ $sub->created_at->format('M d, Y') }}</td>
                    <td>
                        <div class="flex gap-2">
                            <a href="{{ route('admin.online-submissions.show', $sub) }}"
                               class="a-btn-secondary text-xs py-1.5 px-3">
                                {{ $sub->isPending() ? 'Review' : 'View' }}
                            </a>
                            @if($sub->isPending())
                            <form method="POST" action="{{ route('admin.online-submissions.approve', $sub) }}"
                                  onsubmit="return confirm('Approve ₱{{ number_format($sub->amount,2) }} for {{ $sub->student->name ?? '' }}?')">
                                @csrf @method('PATCH')
                                <button type="submit" class="a-btn-primary text-xs py-1.5 px-3">Approve</button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center py-12 text-gray-400">No {{ $currentStatus === 'all' ? '' : $currentStatus }} submissions.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($submissions->hasPages())
    <div class="px-6 py-4">{{ $submissions->withQueryString()->links() }}</div>
    @endif
</div>
@endsection