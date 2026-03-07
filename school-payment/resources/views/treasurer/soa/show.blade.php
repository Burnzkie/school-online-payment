{{-- resources/views/treasurer/soa/show.blade.php --}}
@extends('treasurer.layouts.treasurer-app')
@section('title', 'SOA – '.$student->name)
@section('content')
<div class="max-w-4xl mx-auto space-y-6 fade-up">

    <div class="flex flex-col sm:flex-row sm:items-center gap-4">
        <a href="{{ route('treasurer.student.detail', $student) }}" class="btn-secondary px-3 py-2 rounded-xl text-sm self-start">← Student</a>
        <div class="flex-1">
            <h1 class="text-2xl font-bold text-gray-800">Statement of Account</h1>
            <p class="text-sm text-gray-400">{{ $student->name }} · {{ $schoolYear }} · Sem {{ $semester }}</p>
        </div>
        <button onclick="window.print()" class="btn-primary self-start">🖨️ Print SOA</button>
    </div>

    <div class="section-card p-4">
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
            <button type="submit" class="btn-primary">🔍 Load</button>
        </form>
    </div>

    @if($clearance)
    <div class="flex items-center gap-3 px-5 py-3 rounded-2xl"
         style="background: {{ $clearance->is_cleared ? '#f0fdf4' : '#fff1f2' }}; border: 1px solid {{ $clearance->is_cleared ? '#bbf7d0' : '#fecdd3' }};">
        <span class="text-2xl">{{ $clearance->is_cleared ? '✅' : '🚫' }}</span>
        <div>
            <p class="text-sm font-bold" style="color: {{ $clearance->is_cleared ? '#16a34a' : '#e11d48' }}">
                {{ $clearance->is_cleared ? 'Finance Cleared' : 'ON HOLD – Finance Clearance Withheld' }}
            </p>
            @if(!$clearance->is_cleared && $clearance->hold_reason)
                <p class="text-xs text-gray-500">{{ $clearance->hold_reason }}</p>
            @endif
        </div>
    </div>
    @endif

    <div class="section-card p-5">
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-sm">
            <div><p class="text-xs font-bold uppercase tracking-wider mb-1 text-gray-400">Student Name</p><p class="text-gray-800 font-semibold">{{ $student->name }} {{ $student->last_name }}</p></div>
            <div><p class="text-xs font-bold uppercase tracking-wider mb-1 text-gray-400">Student ID</p><p class="text-gray-800 font-semibold font-mono-num">{{ $student->student_id ?? '—' }}</p></div>
            <div><p class="text-xs font-bold uppercase tracking-wider mb-1 text-gray-400">Level / Year</p><p class="text-gray-800 font-semibold">{{ $student->level_group }} {{ $student->year_level }}</p></div>
            <div><p class="text-xs font-bold uppercase tracking-wider mb-1 text-gray-400">Program / Strand</p><p class="text-gray-800 font-semibold">{{ $student->program ?? $student->strand ?? '—' }}</p></div>
        </div>
    </div>

    <div class="section-card">
        <div class="px-5 py-4 border-b border-gray-100 bg-gray-50">
            <h3 class="text-base font-bold text-gray-800">Fee Assessment</h3>
            <p class="text-xs mt-0.5 text-gray-400">{{ $schoolYear }} · Semester {{ $semester }}</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100 bg-gray-50">
                        <th class="text-left px-5 py-3 text-xs font-bold uppercase tracking-wider text-gray-400">Particulars</th>
                        <th class="text-right px-4 py-3 text-xs font-bold uppercase tracking-wider text-gray-400">Gross Amount</th>
                        <th class="text-right px-4 py-3 text-xs font-bold uppercase tracking-wider text-gray-400">Discount</th>
                        <th class="text-right px-4 py-3 text-xs font-bold uppercase tracking-wider text-gray-400">Net Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($feeRows as $row)
                    <tr class="tbl-row">
                        <td class="px-5 py-3.5">
                            <p class="font-semibold text-gray-800">{{ $row['fee']->fee_name }}</p>
                            @if($row['fee']->description)<p class="text-xs text-gray-400">{{ $row['fee']->description }}</p>@endif
                        </td>
                        <td class="px-4 py-3.5 text-right font-mono-num text-gray-700">₱{{ number_format($row['gross'], 2) }}</td>
                        <td class="px-4 py-3.5 text-right font-mono-num text-amber-600">{{ $row['discount'] > 0 ? '–₱'.number_format($row['discount'], 2) : '—' }}</td>
                        <td class="px-4 py-3.5 text-right font-bold font-mono-num text-indigo-600">₱{{ number_format($row['net'], 2) }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="px-5 py-8 text-center text-sm text-gray-400">No fees assessed for this period.</td></tr>
                    @endforelse
                </tbody>
                <tfoot class="border-t-2 border-gray-200 bg-gray-50">
                    <tr>
                        <td class="px-5 py-3 font-bold text-gray-800">TOTAL ASSESSMENT</td>
                        <td class="px-4 py-3 text-right font-bold font-mono-num text-gray-800">₱{{ number_format($totalGross, 2) }}</td>
                        <td class="px-4 py-3 text-right font-bold font-mono-num text-amber-600">–₱{{ number_format($totalDiscount, 2) }}</td>
                        <td class="px-4 py-3 text-right font-bold font-mono-num text-indigo-700">₱{{ number_format($totalNet, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    @if($scholarships->count())
    <div class="section-card p-5">
        <h3 class="text-sm font-bold text-gray-800 mb-3">Scholarships / Discounts Applied</h3>
        <div class="space-y-2">
            @foreach($scholarships as $s)
            <div class="flex items-center justify-between py-2 px-3 rounded-xl bg-amber-50 border border-amber-100">
                <div>
                    <p class="text-sm font-semibold text-gray-800">{{ $s->scholarship_name }}</p>
                    <p class="text-xs text-gray-400">{{ $s->discount_label }} off {{ $s->applies_to_fee ? '· applies to "'.$s->applies_to_fee.'"' : '· all fees' }}</p>
                </div>
                <span class="col-badge col-badge-amber">Active</span>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <div class="section-card">
        <div class="px-5 py-4 border-b border-gray-100 bg-gray-50">
            <h3 class="text-base font-bold text-gray-800">Payment History</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100 bg-gray-50">
                        <th class="text-left px-5 py-3 text-xs font-bold uppercase tracking-wider text-gray-400">Date</th>
                        <th class="text-left px-4 py-3 text-xs font-bold uppercase tracking-wider text-gray-400">OR #</th>
                        <th class="text-left px-4 py-3 text-xs font-bold uppercase tracking-wider text-gray-400">Method</th>
                        <th class="text-right px-4 py-3 text-xs font-bold uppercase tracking-wider text-gray-400">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $p)
                    <tr class="tbl-row">
                        <td class="px-5 py-3 text-gray-800">{{ \Carbon\Carbon::parse($p->payment_date)->format('M j, Y') }}</td>
                        <td class="px-4 py-3 font-mono text-xs text-gray-700">{{ $p->or_number ?? '—' }}</td>
                        <td class="px-4 py-3"><span class="col-badge col-badge-indigo">{{ $p->payment_method }}</span></td>
                        <td class="px-4 py-3 text-right font-bold font-mono-num text-emerald-600">₱{{ number_format($p->amount, 2) }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="px-5 py-6 text-center text-sm text-gray-400">No payments recorded.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-5 py-4 space-y-2 border-t-2 border-gray-200 bg-gray-50">
            <div class="flex justify-between text-sm">
                <span class="text-gray-500">Total Assessment (net)</span>
                <span class="font-mono-num font-bold text-gray-800">₱{{ number_format($totalNet, 2) }}</span>
            </div>
            <div class="flex justify-between text-sm">
                <span class="text-gray-500">Total Payments</span>
                <span class="font-mono-num font-bold text-emerald-600">– ₱{{ number_format($totalPaid, 2) }}</span>
            </div>
            <div class="flex justify-between text-base pt-2 border-t border-gray-200">
                <span class="font-bold text-gray-800">BALANCE DUE</span>
                <span class="font-bold font-mono-num text-xl" style="color: {{ $balance > 0 ? '#e11d48' : '#16a34a' }}">
                    {{ $balance > 0 ? '₱'.number_format($balance, 2) : 'FULLY PAID ✓' }}
                </span>
            </div>
        </div>
    </div>

    @if($installmentPlan)
    <div class="section-card">
        <div class="px-5 py-4 border-b border-gray-100 bg-gray-50">
            <h3 class="text-sm font-bold text-gray-800">Installment Schedule — {{ $installmentPlan->plan_type == 'full' ? 'Full Payment' : $installmentPlan->plan_type.'-Installment' }}</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100 bg-gray-50">
                        <th class="text-left px-5 py-3 text-xs font-bold uppercase tracking-wider text-gray-400">#</th>
                        <th class="text-left px-4 py-3 text-xs font-bold uppercase tracking-wider text-gray-400">Due Date</th>
                        <th class="text-right px-4 py-3 text-xs font-bold uppercase tracking-wider text-gray-400">Amount Due</th>
                        <th class="text-center px-4 py-3 text-xs font-bold uppercase tracking-wider text-gray-400">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($installmentPlan->schedules as $sched)
                    <tr class="tbl-row">
                        <td class="px-5 py-3 font-bold text-gray-800">{{ $sched->installment_number }}</td>
                        <td class="px-4 py-3 text-gray-700">{{ \Carbon\Carbon::parse($sched->due_date)->format('M j, Y') }}</td>
                        <td class="px-4 py-3 text-right font-mono-num text-gray-700">₱{{ number_format($sched->amount_due, 2) }}</td>
                        <td class="px-4 py-3 text-center">
                            @if($sched->is_paid) <span class="col-badge col-badge-green">Paid</span>
                            @elseif($sched->is_overdue) <span class="col-badge col-badge-red">Overdue</span>
                            @else <span class="col-badge col-badge-amber">Pending</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

</div>
<style>
@media print {
    aside, header, footer, .btn-primary, .btn-secondary, form { display: none !important; }
    body { background: white !important; }
    .section-card { border: 1px solid #e5e7eb !important; box-shadow: none !important; }
}
</style>
@endsection