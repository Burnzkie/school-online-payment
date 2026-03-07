{{-- resources/views/admin/fees/create.blade.php --}}
@extends('admin.layouts.admin-app')
@section('title', 'Add Fee')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="a-fade">
        <a href="{{ route('admin.fees') }}" class="a-btn-secondary text-xs mb-4 inline-flex">← Back</a>
        <h2 class="text-xl font-bold text-gray-800">Add Fee for Student</h2>
        <p class="text-sm mt-0.5 text-gray-400">To apply the same fee to multiple students, use Bulk Assign.</p>
    </div>

    <form method="POST" action="{{ route('admin.fees.store') }}" class="a-card p-7 space-y-5 a-fade a-d1">
        @csrf
        <div>
            <label class="block text-xs font-bold mb-2 text-gray-500">Student *</label>
            <select name="student_id" required class="a-input a-select">
                <option value="">Select student…</option>
                @foreach($students as $s)
                <option value="{{ $s->id }}" {{ old('student_id')==$s->id?'selected':'' }}>
                    {{ $s->name }} — {{ $s->student_id ?? 'no ID' }} ({{ $s->level_group }})
                </option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-bold mb-2 text-gray-500">Fee Name *</label>
            <input name="fee_name" value="{{ old('fee_name') }}" required class="a-input" placeholder="e.g. Tuition Fee">
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold mb-2 text-gray-500">Amount (₱) *</label>
                <input name="amount" type="number" step="0.01" min="0" value="{{ old('amount') }}" required class="a-input" placeholder="0.00">
            </div>
            <div>
                <label class="block text-xs font-bold mb-2 text-gray-500">Status *</label>
                <select name="status" required class="a-input a-select">
                    @foreach(['active','waived','cancelled'] as $s)
                    <option value="{{ $s }}" {{ old('status','active')===$s?'selected':'' }}>{{ ucfirst($s) }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold mb-2 text-gray-500">School Year *</label>
                <input name="school_year" value="{{ old('school_year', date('Y').'-'.(date('Y')+1)) }}" required class="a-input" placeholder="2025-2026">
            </div>
            <div>
                <label class="block text-xs font-bold mb-2 text-gray-500">Semester *</label>
                <select name="semester" required class="a-input a-select">
                    @foreach(['1'=>'1st Semester','2'=>'2nd Semester','summer'=>'Summer'] as $v=>$l)
                    <option value="{{ $v }}" {{ old('semester','1')===$v?'selected':'' }}>{{ $l }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div>
            <label class="block text-xs font-bold mb-2 text-gray-500">Description</label>
            <textarea name="description" rows="2" class="a-input" placeholder="Optional description…" style="resize:vertical;">{{ old('description') }}</textarea>
        </div>
        <div class="flex gap-3 pt-2">
            <button type="submit" class="a-btn-primary flex-1">Create Fee</button>
            <a href="{{ route('admin.fees') }}" class="a-btn-secondary flex-1 justify-center">Cancel</a>
        </div>
    </form>
</div>
@endsection