{{-- resources/views/admin/fees/create.blade.php --}}
@extends('admin.layouts.admin-app')
@section('title', 'Add Fee')

@section('content')
<div class="max-w-2xl mx-auto space-y-6 a-fade">

    <div class="flex items-center gap-3">
        <a href="{{ route('admin.fees') }}" class="a-btn-secondary px-3 py-2 rounded-xl text-sm">← Back</a>
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Add Individual Fee</h1>
            <p class="text-sm text-gray-400">Assign a fee to a single student</p>
        </div>
    </div>

    @if ($errors->any())
        <div class="p-4 rounded-xl text-sm bg-red-50 border border-red-100 text-red-700">
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    <div class="a-card p-7 a-d1">
        <form method="POST" action="{{ route('admin.fees.store') }}" class="space-y-5">
            @csrf

            {{-- Student --}}
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider mb-2 text-gray-500">Student *</label>
                <select name="student_id" required class="a-input a-select">
                    <option value="">— Select Student —</option>
                    @foreach($students as $s)
                        <option value="{{ $s->id }}" {{ old('student_id') == $s->id ? 'selected' : '' }}>
                            {{ $s->name }} ({{ $s->student_id ?? 'No ID' }}) — {{ $s->level_group }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Academic Period --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider mb-2 text-gray-500">School Year *</label>
                    <input type="text" name="school_year" value="{{ old('school_year', date('n') >= 8 ? date('Y').'-'.(date('Y')+1) : (date('Y')-1).'-'.date('Y')) }}" placeholder="e.g. 2025-2026" class="a-input" required>
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider mb-2 text-gray-500">Semester *</label>
                    <select name="semester" required class="a-input a-select">
                        <option value="1"      {{ old('semester')==='1'      ? 'selected' : '' }}>1st Semester</option>
                        <option value="2"      {{ old('semester')==='2'      ? 'selected' : '' }}>2nd Semester</option>
                        <option value="summer" {{ old('semester')==='summer' ? 'selected' : '' }}>Summer</option>
                    </select>
                </div>
            </div>

            {{-- Fee Name --}}
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider mb-2 text-gray-500">Fee Name *</label>
                <input type="text" name="fee_name" value="{{ old('fee_name') }}" placeholder="e.g. Tuition Fee, Lab Fee, Library Fee" class="a-input" required>
            </div>

            {{-- Amount & Status --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider mb-2 text-gray-500">Amount (₱) *</label>
                    <input type="number" name="amount" value="{{ old('amount') }}" step="0.01" min="0" placeholder="0.00" class="a-input" required>
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider mb-2 text-gray-500">Status</label>
                    <select name="status" required class="a-input a-select">
                        <option value="active"    {{ old('status', 'active')==='active'    ? 'selected' : '' }}>Active</option>
                        <option value="waived"    {{ old('status')==='waived'              ? 'selected' : '' }}>Waived</option>
                        <option value="cancelled" {{ old('status')==='cancelled'           ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
            </div>

            {{-- Description --}}
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider mb-2 text-gray-500">Description</label>
                <textarea name="description" rows="3" placeholder="Optional notes about this fee…" class="a-input resize-none">{{ old('description') }}</textarea>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="a-btn-primary flex-1">💾 Save Fee</button>
                <a href="{{ route('admin.fees') }}" class="a-btn-secondary flex-1 justify-center">Cancel</a>
            </div>
        </form>
    </div>

    <div class="p-4 rounded-xl bg-indigo-50 border border-indigo-100">
        <p class="text-sm text-gray-700">
            💡 Need to assign the same fee to multiple students?
            <a href="{{ route('admin.fees.bulk-create') }}" class="font-bold underline text-indigo-500">Use Bulk Assign →</a>
        </p>
    </div>
</div>
@endsection