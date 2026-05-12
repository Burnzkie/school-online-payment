{{-- resources/views/admin/online-submissions/show.blade.php --}}
@extends('admin.layouts.admin-app')
@section('title', 'Review Submission')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <div class="a-fade">
        <a href="{{ route('admin.online-submissions') }}" class="a-btn-secondary text-xs mb-4 inline-flex">← Back</a>
        <h2 class="text-xl font-bold text-gray-800">Online Payment Submission</h2>
    </div>

    @if($submission->isVerified())
    <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm font-semibold a-fade">
        ✅ Approved on {{ $submission->verified_at?->format('M d, Y g:i A') }} by {{ $submission->verifiedBy->name ?? '—' }}.
        @if($submission->payment)
            <a href="{{ route('admin.payments.show', $submission->payment_id) }}" class="underline ml-1">View Payment →</a>
        @endif
    </div>
    @elseif($submission->isRejected())
    <div class="p-4 rounded-xl bg-red-50 border border-red-200 text-red-700 text-sm a-fade">
        <p class="font-semibold">❌ Rejected on {{ $submission->verified_at?->format('M d, Y g:i A') }} by {{ $submission->verifiedBy->name ?? '—' }}.</p>
        <p class="mt-1">Reason: {{ $submission->rejection_reason }}</p>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

        <div class="space-y-5">
            <div class="a-card p-6 a-fade a-d1">
                <p class="text-xs font-bold uppercase tracking-wider text-gray-400 mb-3">Student</p>
                <p class="font-bold text-gray-800">{{ $submission->student->name ?? '—' }}</p>
                <p class="text-xs mt-1 text-gray-400">{{ $submission->student->student_id ?? '' }} · {{ $submission->student->level_group ?? '' }}</p>
                <div class="mt-4 grid grid-cols-3 gap-2 text-center">
                    <div class="rounded-xl p-3 bg-gray-50">
                        <p class="text-xs text-gray-400">Fees</p>
                        <p class="font-mono-num font-bold text-gray-800 text-sm">₱{{ number_format($totalFees, 2) }}</p>
                    </div>
                    <div class="rounded-xl p-3 bg-indigo-50">
                        <p class="text-xs text-indigo-400">Paid</p>
                        <p class="font-mono-num font-bold text-indigo-700 text-sm">₱{{ number_format($totalPaid, 2) }}</p>
                    </div>
                    <div class="rounded-xl p-3 {{ $balance > 0 ? 'bg-red-50' : 'bg-emerald-50' }}">
                        <p class="text-xs {{ $balance > 0 ? 'text-red-400' : 'text-emerald-400' }}">Balance</p>
                        <p class="font-mono-num font-bold text-sm {{ $balance > 0 ? 'text-red-600' : 'text-emerald-600' }}">₱{{ number_format($balance, 2) }}</p>
                    </div>
                </div>
            </div>

            <div class="a-card p-6 a-fade a-d2">
                <p class="text-xs font-bold uppercase tracking-wider text-gray-400 mb-3">Payment Details</p>
                <dl class="space-y-2.5">
                    @foreach([
                        ['Method',    $submission->payment_method],
                        ['Amount',    '₱' . number_format($submission->amount, 2)],
                        ['Reference', $submission->reference_number],
                        ['Period',    $submission->school_year . ' · ' . match($submission->semester) { '1' => '1st Semester', '2' => '2nd Semester', 'summer' => 'Summer', default => $submission->semester }],
                        ['Submitted', $submission->created_at->format('M d, Y g:i A')],
                    ] as [$k, $v])
                    <div class="flex justify-between">
                        <span class="text-xs text-gray-400">{{ $k }}</span>
                        <span class="text-sm font-semibold text-gray-700 font-mono-num">{{ $v }}</span>
                    </div>
                    @endforeach
                </dl>
            </div>
        </div>

        <div class="space-y-5">
            <div class="a-card p-6 a-fade a-d1">
                <p class="text-xs font-bold uppercase tracking-wider text-gray-400 mb-3">Proof of Payment</p>
                @php $ext = strtolower(pathinfo($submission->proof_of_payment, PATHINFO_EXTENSION)); @endphp
                @if(in_array($ext, ['jpg','jpeg','png','webp','gif']))
                    <a href="{{ Storage::url($submission->proof_of_payment) }}" target="_blank">
                        <img src="{{ Storage::url($submission->proof_of_payment) }}"
                             alt="Proof" class="w-full rounded-xl border border-gray-200 hover:opacity-90 transition-opacity">
                    </a>
                @else
                    <a href="{{ Storage::url($submission->proof_of_payment) }}" target="_blank"
                       class="flex items-center gap-3 p-4 rounded-xl border border-indigo-200 bg-indigo-50 hover:bg-indigo-100 transition-colors">
                        <span class="text-2xl">📄</span>
                        <p class="text-sm font-semibold text-indigo-700">View PDF</p>
                    </a>
                @endif
            </div>

            @if($submission->isPending())
            <div class="a-card p-6 a-fade a-d2">
                <p class="text-xs font-bold uppercase tracking-wider text-gray-400 mb-4">Action</p>

                <form method="POST" action="{{ route('admin.online-submissions.approve', $submission) }}" class="mb-4">
                    @csrf @method('PATCH')
                    <div class="mb-3">
                        <label class="block text-xs font-bold mb-1.5 text-gray-500">OR Number (optional)</label>
                        <input type="text" name="or_number" placeholder="Official Receipt No." class="a-input font-mono-num">
                    </div>
                    <div class="mb-4">
                        <label class="block text-xs font-bold mb-1.5 text-gray-500">Notes (optional)</label>
                        <textarea name="notes" rows="2" class="a-input" style="resize:vertical;"></textarea>
                    </div>
                    <button type="submit"
                            onclick="return confirm('Approve ₱{{ number_format($submission->amount, 2) }} for {{ $submission->student->name ?? '' }}?')"
                            class="a-btn-primary w-full justify-center py-2.5">
                        ✅ Approve & Post Payment
                    </button>
                </form>

                <div class="border-t border-gray-100 pt-4">
                    <form method="POST" action="{{ route('admin.online-submissions.reject', $submission) }}"
                          onsubmit="return confirm('Reject this submission?')">
                        @csrf @method('PATCH')
                        <div class="mb-3">
                            <label class="block text-xs font-bold mb-1.5 text-gray-500">Rejection Reason *</label>
                            <textarea name="rejection_reason" rows="2" required class="a-input" style="resize:vertical;"
                                      placeholder="e.g. Reference not found, unclear screenshot..."></textarea>
                        </div>
                        <button type="submit" class="a-btn-danger w-full py-2.5">❌ Reject</button>
                    </form>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection