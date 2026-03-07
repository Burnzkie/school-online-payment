{{-- resources/views/treasurer/fees/create.blade.php --}}
@extends('treasurer.layouts.treasurer-app')
@section('title', 'Add Fee')
@section('content')
<div class="max-w-2xl mx-auto space-y-6 fade-up">
    <div class="flex items-center gap-3">
        <a href="{{ route('treasurer.fees') }}" class="btn-secondary px-3 py-2 rounded-xl text-sm">← Back</a>
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Add Individual Fee</h1>
            <p class="text-sm text-gray-400">Assign a fee to a single student</p>
        </div>
    </div>
    <div class="section-card p-6">
        <form method="POST" action="{{ route('treasurer.fees.store') }}" class="space-y-5">
            @csrf
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider mb-2 text-gray-500">Student *</label>
                <select name="student_id" class="form-input" required>
                    <option value="">— Select Student —</option>
                    @foreach($students as $s)
                        <option value="{{ $s->id }}" {{ old('student_id')==$s->id?'selected':'' }}>{{ $s->name }} ({{ $s->student_id ?? 'No ID' }}) — {{ $s->level_group }}</option>
                    @endforeach
                </select>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider mb-2 text-gray-500">School Year *</label>
                    <input type="text" name="school_year" value="{{ old('school_year', date('n')>=8 ? date('Y').'-'.(date('Y')+1) : (date('Y')-1).'-'.date('Y')) }}" placeholder="e.g. 2025-2026" class="form-input" required>
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider mb-2 text-gray-500">Semester *</label>
                    <select name="semester" class="form-input" required>
                        <option value="1"      {{ old('semester')=='1'?'selected':'' }}>1st Semester</option>
                        <option value="2"      {{ old('semester')=='2'?'selected':'' }}>2nd Semester</option>
                        <option value="summer" {{ old('semester')=='summer'?'selected':'' }}>Summer</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider mb-2 text-gray-500">Fee Name *</label>
                <input type="text" name="fee_name" value="{{ old('fee_name') }}" placeholder="e.g. Tuition Fee, Lab Fee, Library Fee" class="form-input" required>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider mb-2 text-gray-500">Amount (₱) *</label>
                    <input type="number" name="amount" value="{{ old('amount') }}" step="0.01" min="0" placeholder="0.00" class="form-input" required>
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider mb-2 text-gray-500">Status</label>
                    <select name="status" class="form-input">
                        <option value="active"    {{ old('status','active')=='active'?'selected':'' }}>Active</option>
                        <option value="waived"    {{ old('status')=='waived'?'selected':'' }}>Waived</option>
                        <option value="cancelled" {{ old('status')=='cancelled'?'selected':'' }}>Cancelled</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider mb-2 text-gray-500">Description</label>
                <textarea name="description" rows="3" placeholder="Optional notes about this fee…" class="form-input resize-none">{{ old('description') }}</textarea>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="btn-primary">💾 Save Fee</button>
                <a href="{{ route('treasurer.fees') }}" class="btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
    <div class="p-4 rounded-xl bg-indigo-50 border border-indigo-100">
        <p class="text-sm text-gray-700">
            💡 Need to assign the same fee to multiple students?
            <a href="{{ route('treasurer.fees.bulk-create') }}" class="font-bold underline text-indigo-500">Use Bulk Assign →</a>
        </p>
    </div>
</div>
@endsection