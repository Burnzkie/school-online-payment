{{-- resources/views/cashier/online-payments/show.blade.php --}}
@extends('cashier.layouts.cashier-app')
@section('title', 'Review Submission')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    {{-- Back --}}
    <div class="c-fade">
        <a href="{{ route('cashier.online-payments') }}"
           class="inline-flex items-center gap-1.5 text-xs font-semibold text-gray-400 hover:text-indigo-500 transition-colors">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
            </svg>
            Back to Online Payments
        </a>
    </div>

    {{-- Status Banner --}}
    @if($submission->isVerified())
    <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm font-semibold c-fade">
        ✅ This submission was approved on {{ $submission->verified_at?->format('M d, Y g:i A') }} by {{ $submission->verifiedBy->name ?? '—' }}.
        @if($submission->payment)
            <a href="{{ route('cashier.receipt', $submission->payment_id) }}" class="underline ml-1">View Receipt →</a>
        @endif
    </div>
    @elseif($submission->isRejected())
    <div class="p-4 rounded-2xl bg-red-50 border border-red-200 text-red-700 text-sm c-fade">
        <p class="font-semibold">❌ This submission was rejected on {{ $submission->verified_at?->format('M d, Y g:i A') }} by {{ $submission->verifiedBy->name ?? '—' }}.</p>
        <p class="mt-1">Reason: {{ $submission->rejection_reason }}</p>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

        {{-- Left: Submission Details --}}
        <div class="space-y-5">

            {{-- Student Info --}}
            <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm c-fade c-d1">
                <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-3">Student</p>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center font-bold text-white flex-shrink-0"
                         style="background: linear-gradient(135deg, #4f46e5, #6366f1);">
                        {{ strtoupper(substr($submission->student->name ?? 'S', 0, 1)) }}
                    </div>
                    <div>
                        <p class="font-bold text-gray-800">{{ $submission->student->name ?? '—' }}</p>
                        <p class="text-xs mt-0.5 text-gray-400">
                            {{ $submission->student->student_id ?? 'No ID' }}
                            · {{ $submission->student->level_group ?? '—' }}
                            · {{ $submission->student->year_level ?? '—' }}
                        </p>
                    </div>
                </div>
                <div class="mt-4 grid grid-cols-3 gap-3 text-center">
                    <div class="rounded-xl p-3 bg-gray-50">
                        <p class="text-xs text-gray-400">Total Fees</p>
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

            {{-- Payment Details --}}
            <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm c-fade c-d2">
                <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-3">Payment Details</p>
                <dl class="space-y-3">
                    @foreach([
                        ['Method',    $submission->payment_method],
                        ['Amount',    '₱' . number_format($submission->amount, 2)],
                        ['Reference', $submission->reference_number],
                        ['Period',    $submission->school_year . ' · ' . match($submission->semester) { '1' => '1st Semester', '2' => '2nd Semester', 'summer' => 'Summer', default => $submission->semester }],
                        ['Submitted', $submission->created_at->format('M d, Y g:i A')],
                    ] as [$k, $v])
                    <div class="flex justify-between items-start gap-4">
                        <span class="text-xs text-gray-400 flex-shrink-0">{{ $k }}</span>
                        <span class="text-sm font-semibold text-gray-700 text-right font-mono-num">{{ $v }}</span>
                    </div>
                    @endforeach
                </dl>
                @if($submission->notes)
                <div class="mt-3 pt-3 border-t border-gray-100">
                    <p class="text-xs text-gray-400">Student Notes</p>
                    <p class="text-sm text-gray-600 mt-1">{{ $submission->notes }}</p>
                </div>
                @endif
            </div>
        </div>

        {{-- Right: Proof of Payment --}}
        <div class="space-y-5">
            <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm c-fade c-d1">
                <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-3">Proof of Payment</p>
                @php $ext = strtolower(pathinfo($submission->proof_of_payment, PATHINFO_EXTENSION)); @endphp
                @if(in_array($ext, ['jpg','jpeg','png','webp','gif']))
                    <a href="{{ Storage::url($submission->proof_of_payment) }}" target="_blank">
                        <img src="{{ Storage::url($submission->proof_of_payment) }}"
                             alt="Proof of Payment"
                             class="w-full rounded-xl border border-gray-200 hover:opacity-90 transition-opacity cursor-zoom-in">
                    </a>
                    <p class="text-xs text-center text-gray-400 mt-2">Click to open full size</p>
                @else
                    <a href="{{ Storage::url($submission->proof_of_payment) }}" target="_blank"
                       class="flex items-center gap-3 p-4 rounded-xl border border-indigo-200 bg-indigo-50 hover:bg-indigo-100 transition-colors">
                        <span class="text-2xl">📄</span>
                        <div>
                            <p class="text-sm font-semibold text-indigo-700">View PDF Receipt</p>
                            <p class="text-xs text-indigo-400">Click to open</p>
                        </div>
                    </a>
                @endif
            </div>

            {{-- Action Panel (only for pending) --}}
            @if($submission->isPending())
            <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm c-fade c-d2">
                <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-4">Action</p>

                {{-- Approve Form --}}
                <form method="POST" action="{{ route('cashier.online-payments.verify', $submission) }}" class="mb-4">
                    @csrf @method('PATCH')
                    <div class="mb-3">
                        <label class="block text-xs font-bold text-gray-500 mb-1.5">OR Number (optional)</label>
                        <input type="text" name="or_number" placeholder="Official Receipt No."
                               class="c-input font-mono-num">
                    </div>
                    <div class="mb-4">
                        <label class="block text-xs font-bold text-gray-500 mb-1.5">Notes (optional)</label>
                        <textarea name="notes" rows="2" placeholder="Additional notes..."
                                  class="c-input resize-none"></textarea>
                    </div>
                    <button type="submit"
                            onclick="return confirm('Approve this payment of ₱{{ number_format($submission->amount, 2) }}? This will post it to the student\'s account.')"
                            class="w-full py-3 rounded-xl font-bold text-white text-sm transition-all shadow-sm hover:opacity-90"
                            style="background: linear-gradient(135deg, #059669, #10b981);">
                        ✅ Approve & Post Payment
                    </button>
                </form>

                <div class="border-t border-gray-100 pt-4">
                    <form method="POST" action="{{ route('cashier.online-payments.reject', $submission) }}"
                          onsubmit="return confirm('Reject this submission? The student will be notified.')">
                        @csrf @method('PATCH')
                        <div class="mb-3">
                            <label class="block text-xs font-bold text-gray-500 mb-1.5">Rejection Reason *</label>
                            <textarea name="rejection_reason" rows="2" required
                                      placeholder="e.g. Reference number not found, screenshot unclear..."
                                      class="c-input resize-none"></textarea>
                        </div>
                        <button type="submit"
                                class="w-full py-2.5 rounded-xl font-bold text-red-600 text-sm border border-red-200 bg-red-50 hover:bg-red-100 transition-colors">
                            ❌ Reject Submission
                        </button>
                    </form>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection