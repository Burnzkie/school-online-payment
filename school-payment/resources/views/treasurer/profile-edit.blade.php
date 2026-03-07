{{-- resources/views/treasurer/profile-edit.blade.php --}}
@extends('treasurer.layouts.treasurer-app')
@section('title', 'Edit Profile')
@section('content')
<div class="max-w-2xl mx-auto space-y-6 fade-up">
    <div class="flex items-center gap-3">
        <a href="{{ route('treasurer.profile') }}" class="btn-secondary px-3 py-2 rounded-xl text-sm">← Back</a>
        <h1 class="text-2xl font-bold text-gray-800">Edit Profile</h1>
    </div>
    <div class="section-card p-6">
        <form method="POST" action="{{ route('treasurer.profile.update') }}" class="space-y-5">
            @csrf @method('PATCH')
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider mb-2 text-gray-500">First Name *</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" class="form-input" required>
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider mb-2 text-gray-500">Middle Name</label>
                    <input type="text" name="middle_name" value="{{ old('middle_name', $user->middle_name) }}" class="form-input">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider mb-2 text-gray-500">Last Name</label>
                    <input type="text" name="last_name" value="{{ old('last_name', $user->last_name) }}" class="form-input">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider mb-2 text-gray-500">Phone</label>
                    <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" class="form-input">
                </div>
            </div>
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider mb-2 text-gray-500">Email *</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" class="form-input" required>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="btn-primary">💾 Save Changes</button>
                <a href="{{ route('treasurer.profile') }}" class="btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection