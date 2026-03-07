{{-- resources/views/admin/fees/edit.blade.php --}}
@extends('admin.layouts.admin-app')
@section('title', 'Edit Fee')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="a-fade">
        <a href="{{ route('admin.fees') }}" class="a-btn-secondary text-xs mb-4 inline-flex">← Back</a>
        <h2 class="text-xl font-bold text-gray-800">Edit Fee</h2>
    </div>

    <form method="POST" action="{{ route('admin.fees.update', $fee) }}" class="a-card p-7 space-y-5 a-fade a-d1">
        @csrf @method('PATCH')
        <div>
            <label class="block text-xs font-bold mb-2 text-gray-500">Fee Name *</label>
            <input name="fee_name" value="{{ old('fee_name',$fee->fee_name) }}" required class="a-input">
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold mb-2 text-gray-500">Amount (₱) *</label>
                <input name="amount" type="number" step="0.01" value="{{ old('amount',$fee->amount) }}" required class="a-input">
            </div>
            <div>
                <label class="block text-xs font-bold mb-2 text-gray-500">Status *</label>
                <select name="status" required class="a-input a-select">
                    @foreach(['active','waived','cancelled'] as $s)
                    <option value="{{ $s }}" {{ old('status',$fee->status)===$s?'selected':'' }}>{{ ucfirst($s) }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold mb-2 text-gray-500">School Year *</label>
                <input name="school_year" value="{{ old('school_year',$fee->school_year) }}" required class="a-input">
            </div>
            <div>
                <label class="block text-xs font-bold mb-2 text-gray-500">Semester *</label>
                <select name="semester" required class="a-input a-select">
                    @foreach(['1'=>'1st Semester','2'=>'2nd Semester','summer'=>'Summer'] as $v=>$l)
                    <option value="{{ $v }}" {{ old('semester',$fee->semester)===$v?'selected':'' }}>{{ $l }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div>
            <label class="block text-xs font-bold mb-2 text-gray-500">Description</label>
            <textarea name="description" rows="2" class="a-input" style="resize:vertical;">{{ old('description',$fee->description) }}</textarea>
        </div>
        <div class="flex gap-3 pt-2">
            <button type="submit" class="a-btn-primary flex-1">Save Changes</button>
            <a href="{{ route('admin.fees') }}" class="a-btn-secondary flex-1 justify-center">Cancel</a>
        </div>
    </form>
</div>
@endsection