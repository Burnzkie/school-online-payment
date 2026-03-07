{{-- resources/views/admin/users/edit.blade.php --}}
@extends('admin.layouts.admin-app')
@section('title', 'Edit User')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="a-fade">
        <a href="{{ route('admin.users') }}" class="a-btn-secondary text-xs mb-4 inline-flex">← Back to Users</a>
        <h2 class="text-xl font-bold text-gray-800">Edit User: {{ $user->name }}</h2>
    </div>

    <form method="POST" action="{{ route('admin.users.update', $user) }}" class="a-card p-7 space-y-5 a-fade a-d1">
        @csrf @method('PATCH')
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold mb-2 text-gray-500">First Name *</label>
                <input name="name" value="{{ old('name', $user->name) }}" required class="a-input">
            </div>
            <div>
                <label class="block text-xs font-bold mb-2 text-gray-500">Last Name</label>
                <input name="last_name" value="{{ old('last_name', $user->last_name) }}" class="a-input">
            </div>
        </div>
        <div>
            <label class="block text-xs font-bold mb-2 text-gray-500">Email *</label>
            <input name="email" type="email" value="{{ old('email', $user->email) }}" required class="a-input">
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold mb-2 text-gray-500">Role *</label>
                <select name="role" required class="a-input a-select">
                    @foreach(['admin'=>'Administrator','treasurer'=>'Treasurer','cashier'=>'Cashier','parent'=>'Parent'] as $v=>$l)
                    <option value="{{ $v }}" {{ (old('role',$user->role)===$v)?'selected':'' }}>{{ $l }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold mb-2 text-gray-500">Phone</label>
                <input name="phone" value="{{ old('phone', $user->phone) }}" class="a-input">
            </div>
        </div>
        <div>
            <label class="block text-xs font-bold mb-2 text-gray-500">Extra Info</label>
            <input name="extra_info" value="{{ old('extra_info', $user->extra_info) }}" class="a-input">
        </div>
        <div class="pt-2 flex gap-3">
            <button type="submit" class="a-btn-primary flex-1">Save Changes</button>
            <a href="{{ route('admin.users') }}" class="a-btn-secondary flex-1 justify-center">Cancel</a>
        </div>
    </form>
</div>
@endsection