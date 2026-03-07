{{-- resources/views/treasurer/fees/edit.blade.php --}}
@extends('treasurer.layouts.treasurer-app')
@section('title', 'Edit Fee')
@section('content')
<div class="max-w-2xl mx-auto space-y-6 fade-up">
    <div class="flex items-center gap-3">
        <a href="{{ route('treasurer.fees') }}" class="btn-secondary px-3 py-2 rounded-xl text-sm">← Back</a>
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Edit Fee</h1>
            <p class="text-sm text-gray-400">{{ $fee->student->name ?? 'Unknown Student' }} — {{ $fee->fee_name }}</p>
        </div>
    </div>
    <div class="section-card p-6">
        <form method="POST" action="{{ route('treasurer.fees.update', $fee) }}" class="space-y-5">
            @csrf @method('PATCH')
            <div class="p-4 rounded-xl bg-gray-50 border border-gray-100">
                <p class="text-xs font-bold uppercase tracking-wider mb-2 text-gray-400">Student (read-only)</p>
                <p class="text-sm font-semibold text-gray-800">{{ $fee->student->name ?? '—' }}</p>
                <p class="text-xs text-gray-400">{{ $fee->school_year }} · Semester {{ $fee->semester }}</p>
            </div>
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider mb-2 text-gray-500">Fee Name *</label>
                <input type="text" name="fee_name" value="{{ old('fee_name', $fee->fee_name) }}" class="form-input" required>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider mb-2 text-gray-500">Amount (₱) *</label>
                    <input type="number" name="amount" value="{{ old('amount', $fee->amount) }}" step="0.01" min="0" class="form-input" required>
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider mb-2 text-gray-500">Status</label>
                    <select name="status" class="form-input">
                        <option value="active"    {{ old('status',$fee->status)=='active'?'selected':'' }}>Active</option>
                        <option value="waived"    {{ old('status',$fee->status)=='waived'?'selected':'' }}>Waived</option>
                        <option value="cancelled" {{ old('status',$fee->status)=='cancelled'?'selected':'' }}>Cancelled</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider mb-2 text-gray-500">Description</label>
                <textarea name="description" rows="3" class="form-input resize-none">{{ old('description', $fee->description) }}</textarea>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="btn-primary">💾 Save Changes</button>
                <a href="{{ route('treasurer.fees') }}" class="btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection