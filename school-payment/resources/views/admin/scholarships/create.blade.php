{{-- resources/views/admin/scholarships/create.blade.php --}}
@extends('admin.layouts.admin-app')
@section('title', 'Grant Scholarship')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="a-fade">
        <a href="{{ route('admin.scholarships') }}" class="a-btn-secondary text-xs mb-4 inline-flex">← Back</a>
        <h2 class="text-xl font-bold text-gray-800">Grant Scholarship</h2>
    </div>

    <form method="POST" action="{{ route('admin.scholarships.store') }}" class="a-card p-7 space-y-5 a-fade a-d1">
        @csrf
        <div>
            <label class="block text-xs font-bold mb-2 text-gray-500">Student *</label>
            <select name="student_id" required class="a-input a-select">
                <option value="">Select student…</option>
                @foreach($students as $s)
                <option value="{{ $s->id }}" {{ old('student_id')==$s->id?'selected':'' }}>{{ $s->name }} — {{ $s->student_id ?? 'no ID' }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-bold mb-2 text-gray-500">Scholarship Name *</label>
            <input name="scholarship_name" value="{{ old('scholarship_name') }}" required class="a-input" placeholder="e.g. Academic Scholar, Sibling Discount">
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold mb-2 text-gray-500">Discount Type *</label>
                <select name="discount_type" required class="a-input a-select" x-data x-model="dtype">
                    <option value="percent">Percentage (%)</option>
                    <option value="fixed">Fixed Amount (₱)</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold mb-2 text-gray-500">Value *</label>
                <input name="discount_value" type="number" step="0.01" min="0" value="{{ old('discount_value') }}" required class="a-input" placeholder="e.g. 50 or 5000">
            </div>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold mb-2 text-gray-500">Max Discount Cap (₱)</label>
                <input name="max_discount" type="number" step="0.01" min="0" value="{{ old('max_discount') }}" class="a-input" placeholder="Leave blank for no cap">
            </div>
            <div>
                <label class="block text-xs font-bold mb-2 text-gray-500">Applies To Fee</label>
                <select name="applies_to_fee" class="a-input a-select">
                    <option value="">All Fees</option>
                    @foreach($feeNames as $fn)<option value="{{ $fn }}" {{ old('applies_to_fee')===$fn?'selected':'' }}>{{ $fn }}</option>@endforeach
                </select>
            </div>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold mb-2 text-gray-500">School Year *</label>
                <input name="school_year" value="{{ old('school_year', date('Y').'-'.(date('Y')+1)) }}" required class="a-input">
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
            <label class="block text-xs font-bold mb-2 text-gray-500">Remarks</label>
            <textarea name="remarks" rows="2" class="a-input" style="resize:vertical;" placeholder="Optional notes…">{{ old('remarks') }}</textarea>
        </div>
        <div class="flex gap-3 pt-2">
            <button type="submit" class="a-btn-primary flex-1">Grant Scholarship</button>
            <a href="{{ route('admin.scholarships') }}" class="a-btn-secondary flex-1 justify-center">Cancel</a>
        </div>
    </form>
</div>
@endsection