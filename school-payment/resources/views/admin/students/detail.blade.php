{{-- resources/views/admin/students/detail.blade.php --}}
@extends('admin.layouts.admin-app')
@section('title', $student->name)

@section('content')

<div class="a-fade flex items-center justify-between">
    <a href="{{ route('admin.students') }}" class="a-btn-secondary text-xs inline-flex">← Back to Students</a>

    {{-- Drop / Reinstate action --}}
    @if($student->status === 'dropped')
        <form method="POST" action="{{ route('admin.students.reinstate', $student) }}"
              onsubmit="return confirm('Reinstate {{ $student->name }} {{ $student->last_name }}? Their account will be restored to active.')">
            @csrf @method('PATCH')
            <button type="submit"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-bold text-white transition-all"
                    style="background: linear-gradient(135deg,#059669,#34d399);">
                ✅ Reinstate Student
            </button>
        </form>
    @else
        <button type="button" onclick="document.getElementById('drop-modal').classList.remove('hidden')"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-bold text-white transition-all"
                style="background: linear-gradient(135deg,#e11d48,#f43f5e);">
            🚫 Drop Student
        </button>
    @endif
</div>

{{-- Drop Student Modal --}}
<div id="drop-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4"
     style="background: rgba(0,0,0,0.5); backdrop-filter: blur(4px);">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-5">
        <div class="flex items-center gap-3 mb-5">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center text-xl flex-shrink-0"
                 style="background:#fff1f2; border:1px solid #fecdd3;">🚫</div>
            <div>
                <h3 class="font-bold text-gray-800">Drop Student</h3>
                <p class="text-xs text-gray-400 mt-0.5">{{ $student->name }} {{ $student->last_name }}</p>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.students.drop', $student) }}">
            @csrf @method('PATCH')
            <div class="mb-5">
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">
                    Reason for Dropping <span class="text-red-500">*</span>
                </label>
                <select name="drop_reason" required
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-200 bg-gray-50 text-sm text-gray-800 focus:outline-none focus:border-red-400 focus:ring-2 focus:ring-red-100 mb-3"
                        onchange="document.getElementById('other-reason').classList.toggle('hidden', this.value !== 'Other')">
                    <option value="">Select reason…</option>
                    <option value="Voluntarily Withdrew">Voluntarily Withdrew</option>
                    <option value="Financial Difficulties">Financial Difficulties</option>
                    <option value="Academic Failure">Academic Failure</option>
                    <option value="Transferred to Another School">Transferred to Another School</option>
                    <option value="Health Reasons">Health Reasons</option>
                    <option value="Disciplinary Action">Disciplinary Action</option>
                    <option value="Other">Other…</option>
                </select>
                <div id="other-reason" class="hidden">
                    <input type="text" name="drop_reason_other" placeholder="Specify reason…"
                           class="w-full px-4 py-2.5 rounded-xl border border-gray-200 bg-gray-50 text-sm text-gray-800 focus:outline-none focus:border-red-400 focus:ring-2 focus:ring-red-100">
                </div>
            </div>
            <div class="mb-6">
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Notes <span class="font-normal normal-case text-gray-400">(optional)</span></label>
                <textarea name="drop_notes" rows="3" placeholder="Additional remarks…"
                          class="w-full px-4 py-2.5 rounded-xl border border-gray-200 bg-gray-50 text-sm text-gray-800 focus:outline-none focus:border-red-400 focus:ring-2 focus:ring-red-100 resize-none"></textarea>
            </div>
            <div class="flex gap-3">
                <button type="submit"
                        class="flex-1 py-2.5 rounded-xl text-sm font-bold text-white transition-all"
                        style="background: linear-gradient(135deg,#e11d48,#f43f5e);">
                    Confirm Drop
                </button>
                <button type="button" onclick="document.getElementById('drop-modal').classList.add('hidden')"
                        class="flex-1 py-2.5 rounded-xl text-sm font-bold text-gray-600 border border-gray-200 hover:bg-gray-50 transition-all">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Student Header --}}
<div class="a-card p-6 a-fade a-d1 bg-gradient-to-br from-indigo-50 to-white">
    <div class="flex flex-col sm:flex-row gap-5">
        <div class="flex items-center gap-4">
            @if($student->profile_picture && Storage::disk('public')->exists($student->profile_picture))
                <img src="{{ Storage::url($student->profile_picture) }}" class="w-16 h-16 rounded-2xl object-cover ring-2 ring-indigo-200">
            @else
                <div class="w-16 h-16 rounded-2xl flex items-center justify-center text-2xl font-bold text-white"
                     style="background: linear-gradient(135deg,#4f46e5,#4338ca);">
                    {{ strtoupper(substr($student->name,0,1)) }}
                </div>
            @endif
            <div>
                <h2 class="text-xl font-bold text-gray-800">{{ $student->name }} {{ $student->last_name }}</h2>
                <p class="text-sm mt-0.5 text-gray-400">{{ $student->email }}</p>
                <div class="flex flex-wrap gap-2 mt-2">
                    <span class="a-badge a-badge-sky">{{ $student->level_group ?? 'N/A' }}</span>
                    <span class="a-badge a-badge-gray">{{ $student->year_level ?? 'N/A' }}</span>
                    @if($student->program || $student->strand)
                    <span class="a-badge a-badge-violet">{{ $student->program ?? $student->strand }}</span>
                    @endif
                </div>
            </div>
        </div>
        <div class="sm:ml-auto flex flex-col gap-2 text-right">
            <div>
                <p class="text-xs text-gray-400">Student ID</p>
                <p class="font-bold font-mono-num text-gray-800">{{ $student->student_id ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400">Outstanding Balance</p>
                <p class="text-2xl font-bold font-mono-num {{ $balance > 0 ? 'text-red-500' : 'text-emerald-600' }}">
                    ₱{{ number_format($balance,2) }}
                </p>
            </div>
        </div>
    </div>
</div>

{{-- Dropout Banner --}}
@if($student->status === 'dropped')
<div class="flex items-start gap-4 px-6 py-5 rounded-2xl a-fade a-d1"
     style="background:#fff1f2; border:1px solid #fecdd3;">
    <span class="text-2xl flex-shrink-0">🚫</span>
    <div class="flex-1">
        <p class="font-bold text-red-700 text-sm">This student has been dropped from the school.</p>
        <p class="text-xs text-red-500 mt-0.5">
            Reason: <span class="font-semibold">{{ $student->drop_reason ?? '—' }}</span>
            @if($student->dropped_at)
                &nbsp;·&nbsp; Dropped on {{ \Carbon\Carbon::parse($student->dropped_at)->format('M d, Y') }}
            @endif
            @if($student->dropped_by_name)
                &nbsp;·&nbsp; By {{ $student->dropped_by_name }}
            @endif
        </p>
        @if($student->drop_notes)
            <p class="text-xs text-red-400 mt-1 italic">{{ $student->drop_notes }}</p>
        @endif
    </div>
</div>
@endif

{{-- Summary Cards --}}
<div class="grid grid-cols-3 gap-4 a-fade a-d2">
    @foreach([
        ['Total Fees','₱'.number_format($totalFees,2),'#4f46e5'],
        ['Total Paid','₱'.number_format($totalPaid,2),'#059669'],
        ['Balance','₱'.number_format($balance,2),$balance>0?'#e11d48':'#059669'],
    ] as [$l,$v,$c])
    <div class="a-card px-5 py-4 text-center">
        <p class="text-xl font-bold font-mono-num" style="color:{{ $c }}">{{ $v }}</p>
        <p class="text-xs mt-1 text-gray-400">{{ $l }}</p>
    </div>
    @endforeach
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 a-fade a-d3">

    {{-- Personal Info --}}
    <div class="a-card p-6">
        <h3 class="font-bold text-gray-800 mb-4">Personal Information</h3>
        <dl class="space-y-2.5">
            @foreach([
                ['Birth Date',    $student->birth_date ? \Carbon\Carbon::parse($student->birth_date)->format('M d, Y') : '—'],
                ['Age',           $student->age ?? '—'],
                ['Gender',        $student->gender ?? '—'],
                ['Nationality',   $student->nationality ?? '—'],
                ['Phone',         $student->phone ?? '—'],
                ['Address',       collect([$student->street,$student->barangay,$student->municipality,$student->city])->filter()->join(', ') ?: '—'],
            ] as [$k,$v])
            <div class="flex justify-between gap-3">
                <dt class="text-xs text-gray-400">{{ $k }}</dt>
                <dd class="text-xs font-semibold text-gray-700 text-right">{{ $v }}</dd>
            </div>
            @endforeach
        </dl>

        <div class="mt-5 pt-4 border-t border-gray-100">
            <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Parent / Guardian</h4>
            <dl class="space-y-2">
                @foreach([
                    ['Father',   $student->father_name,   $student->father_contact],
                    ['Mother',   $student->mother_name,   $student->mother_contact],
                    ['Guardian', $student->guardian_name, $student->guardian_contact],
                ] as [$label,$name,$contact])
                @if($name)
                <div>
                    <p class="text-xs font-semibold text-gray-700">{{ $label }}: {{ $name }}</p>
                    @if($contact)<p class="text-xs font-mono-num text-gray-400">{{ $contact }}</p>@endif
                </div>
                @endif
                @endforeach
            </dl>
        </div>

        {{-- Linked Parents --}}
        @if($linkedParents->count())
        <div class="mt-4 pt-4 border-t border-gray-100">
            <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Linked Portal Parents</h4>
            @foreach($linkedParents as $p)
            <div class="flex items-center gap-2 mb-1.5">
                <span class="a-badge a-badge-{{ $p->link_method==='auto_phone'?'emerald':'amber' }} text-[10px]">
                    {{ $p->link_method==='auto_phone' ? 'Auto' : 'Manual' }}
                </span>
                <span class="text-xs text-gray-600">{{ $p->name }} · {{ $p->phone ?? $p->email }}</span>
            </div>
            @endforeach
        </div>
        @endif
    </div>

    {{-- Fees --}}
    <div class="a-card p-6 lg:col-span-2">
        <h3 class="font-bold text-gray-800 mb-4">Fee Breakdown · {{ $currentYear }}</h3>
        <div class="space-y-2">
            @forelse($fees as $fee)
            @php
                $sf  = \App\Models\StudentFee::where('student_id',$student->id)->where('fee_id',$fee->id)->first();
                $paidAmt = $sf->amount_paid ?? 0;
                $pct = $fee->amount > 0 ? min(100,round($paidAmt/$fee->amount*100)) : 0;
            @endphp
            <div class="px-4 py-3 rounded-xl bg-gray-50 border border-gray-100">
                <div class="flex items-center justify-between mb-2">
                    <div>
                        <p class="text-sm font-semibold text-gray-800">{{ $fee->fee_name }}</p>
                        @if($fee->description)<p class="text-xs text-gray-400">{{ $fee->description }}</p>@endif
                    </div>
                    <div class="text-right">
                        <p class="font-bold font-mono-num text-gray-800 text-sm">₱{{ number_format($fee->amount,2) }}</p>
                        <span class="a-badge {{ $fee->status==='active'?'a-badge-emerald':($fee->status==='waived'?'a-badge-sky':'a-badge-gray') }} text-[10px]">
                            {{ ucfirst($fee->status) }}
                        </span>
                    </div>
                </div>
                <div class="a-progress-track">
                    <div class="a-progress-fill" style="width:{{ $pct }}%;background:{{ $pct>=100?'linear-gradient(90deg,#059669,#34d399)':'linear-gradient(90deg,#4f46e5,#6366f1)' }};"></div>
                </div>
                <div class="flex justify-between text-xs mt-1 text-gray-400">
                    <span>Paid: ₱{{ number_format($paidAmt,2) }}</span>
                    <span>{{ $pct }}%</span>
                </div>
            </div>
            @empty
            <p class="text-sm text-center py-6 text-gray-400">No fees assigned for this year.</p>
            @endforelse
        </div>

        {{-- Scholarships --}}
        @if($scholarships->count())
        <div class="mt-5 pt-4 border-t border-gray-100">
            <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Active Scholarships</h4>
            @foreach($scholarships as $sc)
            <div class="flex items-center justify-between text-sm mb-2">
                <span class="text-gray-700">{{ $sc->scholarship_name }}</span>
                <span class="a-badge a-badge-violet">
                    {{ $sc->discount_type==='percent' ? $sc->discount_value.'%' : '₱'.number_format($sc->discount_value,2) }} off
                </span>
            </div>
            @endforeach
        </div>
        @endif

        {{-- Clearance --}}
        <div class="mt-5 pt-4 border-t border-gray-100">
            <div class="flex items-center justify-between">
                <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider">Finance Clearance</h4>
                @if($clearance)
                    <span class="a-badge {{ $clearance->is_cleared ? 'a-badge-emerald' : 'a-badge-red' }}">
                        {{ $clearance->is_cleared ? '✅ Cleared' : '🚫 On Hold' }}
                    </span>
                @else
                    <span class="a-badge a-badge-gray">Not synced</span>
                @endif
            </div>
            @if($clearance && $clearance->hold_reason)
                <p class="text-xs mt-2 text-red-500">{{ $clearance->hold_reason }}</p>
            @endif
        </div>
    </div>
</div>

{{-- Payment History --}}
<div class="a-card a-fade a-d4">
    <div class="px-6 py-4 border-b border-gray-100">
        <h3 class="font-bold text-gray-800">Payment History</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="a-table">
            <thead>
                <tr><th>Date</th><th>Amount</th><th>Method</th><th>OR Number</th><th>Semester</th><th>Status</th></tr>
            </thead>
            <tbody>
                @forelse($payments as $p)
                <tr>
                    <td class="text-sm text-gray-600">{{ \Carbon\Carbon::parse($p->payment_date)->format('M d, Y') }}</td>
                    <td class="font-bold font-mono-num text-indigo-600">₱{{ number_format($p->amount,2) }}</td>
                    <td><span class="a-badge a-badge-sky">{{ $p->payment_method }}</span></td>
                    <td class="font-mono-num text-xs text-gray-400">{{ $p->or_number ?? '—' }}</td>
                    <td class="text-xs text-gray-400">{{ $p->school_year }} · Sem {{ $p->semester }}</td>
                    <td><span class="a-badge {{ $p->status==='completed'?'a-badge-emerald':($p->status==='pending'?'a-badge-amber':'a-badge-red') }}">{{ ucfirst($p->status) }}</span></td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center py-10 text-gray-400">No payments recorded.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection