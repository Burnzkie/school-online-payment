{{-- resources/views/admin/users/create.blade.php --}}
@extends('admin.layouts.admin-app')
@section('title', 'Create Staff Account')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="a-fade">
        <a href="{{ route('admin.users') }}" class="a-btn-secondary text-xs mb-4 inline-flex">← Back to Users</a>
        <h2 class="text-xl font-bold text-gray-800">Create Staff Account</h2>
        <p class="text-sm mt-0.5 text-gray-400">A temporary password will be set. Ask the user to change it on first login.</p>
    </div>

    <form method="POST" action="{{ route('admin.users.store') }}" class="a-card p-7 space-y-5 a-fade a-d1">
        @csrf
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold mb-2 text-gray-500">First Name *</label>
                <input name="name" value="{{ old('name') }}" required class="a-input" placeholder="Juan">
            </div>
            <div>
                <label class="block text-xs font-bold mb-2 text-gray-500">Last Name</label>
                <input name="last_name" value="{{ old('last_name') }}" class="a-input" placeholder="Dela Cruz">
            </div>
        </div>
        <div>
            <label class="block text-xs font-bold mb-2 text-gray-500">Email Address *</label>
            <input name="email" type="email" value="{{ old('email') }}" required class="a-input" placeholder="staff@school.edu">
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold mb-2 text-gray-500">Role *</label>
                <select name="role" required class="a-input a-select">
                    <option value="">Select role…</option>
                    @foreach(['admin'=>'Administrator','treasurer'=>'Treasurer','cashier'=>'Cashier'] as $v=>$l)
                    <option value="{{ $v }}" {{ old('role')===$v?'selected':'' }}>{{ $l }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold mb-2 text-gray-500">Phone</label>
                <input name="phone" value="{{ old('phone') }}" class="a-input" placeholder="09XX-XXX-XXXX">
            </div>
        </div>
        <div>
            <label class="block text-xs font-bold mb-2 text-gray-500">Notes / Extra Info</label>
            <input name="extra_info" value="{{ old('extra_info') }}" class="a-input" placeholder="e.g. Accounting Office, Window 3">
        </div>

        <div class="pt-2 flex gap-3">
            <button type="submit" class="a-btn-primary flex-1">Create Account</button>
            <a href="{{ route('admin.users') }}" class="a-btn-secondary flex-1 justify-center">Cancel</a>
        </div>
    </form>
</div>
@endsection