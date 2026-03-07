{{-- resources/views/treasurer/scholarships/create.blade.php --}}
@extends('treasurer.layouts.treasurer-app')
@section('title', 'Grant Scholarship')
@section('content')
<div class="max-w-2xl mx-auto space-y-6 fade-up">
    <div class="flex items-center gap-3">
        <a href="{{ route('treasurer.scholarships') }}" class="btn-secondary px-3 py-2 rounded-xl text-sm">← Back</a>
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Grant Scholarship / Discount</h1>
            <p class="text-sm text-gray-400">Assign a fee discount or scholarship to a student</p>
        </div>
    </div>
    <div class="section-card p-6">
        <form method="POST" action="{{ route('treasurer.scholarships.store') }}" class="space-y-5">
            @csrf
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider mb-2 text-gray-500">Student *</label>
                <select name="student_id" class="form-input" required>
                    <option value="">— Select Student —</option>
                    @foreach($students as $s)
                        <option value="{{ $s->id }}" {{ old('student_id')==$s->id?'selected':'' }}>{{ $s->name }} ({{ $s->student_id ?? 'No ID' }}) — {{ $s->level_group }}</option>
                    @endforeach
                </select>
                @error('student_id')<p class="text-xs mt-1 text-red-500">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider mb-2 text-gray-500">Scholarship / Discount Name *</label>
                <input type="text" name="scholarship_name" value="{{ old('scholarship_name') }}" placeholder="e.g. Academic Scholar, Sibling Discount, Financial Aid" class="form-input" required>
                @error('scholarship_name')<p class="text-xs mt-1 text-red-500">{{ $message }}</p>@enderror
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
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider mb-2 text-gray-500">Discount Type *</label>
                    <select name="discount_type" id="discountType" class="form-input" required onchange="toggleMaxDiscount(this.value)">
                        <option value="percent" {{ old('discount_type')=='percent'?'selected':'' }}>Percentage (%)</option>
                        <option value="fixed"   {{ old('discount_type')=='fixed'?'selected':'' }}>Fixed Amount (₱)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider mb-2 text-gray-500">Discount Value * <span id="discountUnit" class="normal-case font-normal text-gray-400">(% of fee)</span></label>
                    <input type="number" name="discount_value" value="{{ old('discount_value') }}" step="0.01" min="0" placeholder="0.00" class="form-input" required>
                </div>
            </div>
            <div id="maxDiscountWrap">
                <label class="block text-xs font-bold uppercase tracking-wider mb-2 text-gray-500">Maximum Discount Cap (₱) <span class="normal-case font-normal text-gray-400">optional</span></label>
                <input type="number" name="max_discount" value="{{ old('max_discount') }}" step="0.01" min="0" placeholder="e.g. 5000.00" class="form-input">
            </div>
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider mb-2 text-gray-500">Applies To Fee <span class="normal-case font-normal text-gray-400">(leave blank = all active fees)</span></label>
                <select name="applies_to_fee" class="form-input">
                    <option value="">— All Fees —</option>
                    @foreach($feeNames as $fn)
                        <option value="{{ $fn }}" {{ old('applies_to_fee')==$fn?'selected':'' }}>{{ $fn }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider mb-2 text-gray-500">Remarks</label>
                <textarea name="remarks" rows="2" placeholder="Optional notes…" class="form-input resize-none">{{ old('remarks') }}</textarea>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="btn-primary">🎓 Grant Scholarship</button>
                <a href="{{ route('treasurer.scholarships') }}" class="btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
<script>
function toggleMaxDiscount(type) {
    document.getElementById('discountUnit').textContent = type === 'percent' ? '(% of fee)' : '(₱ fixed amount)';
    document.getElementById('maxDiscountWrap').style.display = type === 'percent' ? 'block' : 'none';
}
toggleMaxDiscount(document.getElementById('discountType').value);
</script>
@endsection