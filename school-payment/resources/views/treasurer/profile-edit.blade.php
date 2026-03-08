{{-- resources/views/treasurer/profile-edit.blade.php --}}
@extends('treasurer.layouts.treasurer-app')
@section('title', 'Edit Profile')
@section('content')
<div class="max-w-2xl mx-auto space-y-6 fade-up">
    <div class="flex items-center gap-3">
        <a href="{{ route('treasurer.profile') }}" class="btn-secondary px-3 py-2 rounded-xl text-sm">← Back</a>
        <h1 class="text-2xl font-bold text-gray-800">Edit Profile</h1>
    </div>

    <form method="POST" action="{{ route('treasurer.profile.update') }}" class="space-y-6">
        @csrf @method('PATCH')

        @if ($errors->any())
        <div class="section-card p-4 border border-red-100 bg-red-50">
            <ul class="list-disc pl-5 space-y-1 text-sm text-red-600">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        {{-- Personal Information --}}
        <div class="section-card p-6">
            <h3 class="text-sm font-bold text-gray-800 mb-5">Personal Information</h3>
            <div class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider mb-2 text-gray-500">First Name *</label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" class="form-input" required>
                        @error('name')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider mb-2 text-gray-500">Middle Name</label>
                        <input type="text" name="middle_name" value="{{ old('middle_name', $user->middle_name) }}" class="form-input">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider mb-2 text-gray-500">Last Name *</label>
                        <input type="text" name="last_name" value="{{ old('last_name', $user->last_name) }}" class="form-input" required>
                        @error('last_name')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider mb-2 text-gray-500">Employee ID</label>
                        <input type="text" name="employee_id" value="{{ old('employee_id', $user->employee_id) }}" class="form-input" placeholder="TR-2025-001">
                    </div>
                </div>
            </div>
        </div>

        {{-- Contact Information --}}
        <div class="section-card p-6">
            <h3 class="text-sm font-bold text-gray-800 mb-5">Contact Information</h3>
            <div class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider mb-2 text-gray-500">Mobile Number *</label>
                        <input type="tel" name="phone" value="{{ old('phone', $user->phone) }}" class="form-input" placeholder="09XX-XXX-XXXX" required>
                        @error('phone')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider mb-2 text-gray-500">Work Email *</label>
                        <input type="email" name="work_email" value="{{ old('work_email', $user->work_email) }}" class="form-input" placeholder="treasurer@pac.edu.ph" required>
                        @error('work_email')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider mb-2 text-gray-500">Login Email *</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" class="form-input" required>
                    @error('email')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        {{-- Address --}}
        <div class="section-card p-6">
            <h3 class="text-sm font-bold text-gray-800 mb-5">Address</h3>
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider mb-2 text-gray-500">Street / Barangay *</label>
                    <input type="text" name="street" value="{{ old('street', $user->street) }}" class="form-input" placeholder="House No., Street, Barangay" required>
                    @error('street')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider mb-2 text-gray-500">Municipality / City *</label>
                        <input type="text" name="municipality" value="{{ old('municipality', $user->municipality) }}" class="form-input" placeholder="Municipality or City" required>
                        @error('municipality')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider mb-2 text-gray-500">Province *</label>
                        <input type="text" name="province" value="{{ old('province', $user->province) }}" class="form-input" placeholder="Province" required>
                        @error('province')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider mb-2 text-gray-500">ZIP Code</label>
                        <input type="text" name="zip_code" value="{{ old('zip_code', $user->zip_code) }}" class="form-input" placeholder="4-digit ZIP" maxlength="4">
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider mb-2 text-gray-500">Country</label>
                        <input type="text" name="country" value="{{ old('country', $user->country ?? 'Philippines') }}" class="form-input" placeholder="Philippines">
                    </div>
                </div>
            </div>
        </div>

        <div class="flex gap-3">
            <button type="submit" class="btn-primary">💾 Save Changes</button>
            <a href="{{ route('treasurer.profile') }}" class="btn-secondary">Cancel</a>
        </div>

    </form>
</div>
@endsection