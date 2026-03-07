{{-- resources/views/admin/profile/index.blade.php --}}
@extends('admin.layouts.admin-app')
@section('title', 'My Profile')

@section('content')

<div class="max-w-3xl mx-auto space-y-6">

    {{-- Profile Card --}}
    <div class="a-card p-6 a-fade bg-gradient-to-br from-indigo-50 to-white">
        <div class="flex flex-col sm:flex-row items-center gap-6">
            {{-- Avatar + upload --}}
            <div class="relative group" x-data>
                @if($user->profile_picture && Storage::disk('public')->exists($user->profile_picture))
                    <img src="{{ Storage::url($user->profile_picture) }}"
                         class="w-24 h-24 rounded-2xl object-cover ring-4 ring-indigo-200" id="avatar-preview">
                @else
                    <div class="w-24 h-24 rounded-2xl flex items-center justify-center text-4xl font-bold text-white"
                         style="background: linear-gradient(135deg,#4f46e5,#4338ca);" id="avatar-preview-div">
                        {{ strtoupper(substr($user->name,0,1)) }}
                    </div>
                @endif
                <label class="absolute inset-0 flex items-center justify-center rounded-2xl bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity cursor-pointer text-white text-xs font-bold">
                    Change
                    <input type="file" class="hidden" accept="image/*" id="photo-input"
                           onchange="uploadPhoto(this)">
                </label>
            </div>
            <div>
                <h2 class="text-2xl font-bold text-gray-800">{{ $user->name }} {{ $user->last_name }}</h2>
                <p class="mt-1 text-gray-400">{{ $user->email }}</p>
                <div class="flex gap-2 mt-3">
                    <span class="a-badge a-badge-violet">🛡️ Administrator</span>
                    @if($user->phone)<span class="a-badge a-badge-gray">📱 {{ $user->phone }}</span>@endif
                </div>
            </div>
        </div>
    </div>

    {{-- Edit Form --}}
    <form method="POST" action="{{ route('admin.profile.update') }}" class="a-card p-6 space-y-5 a-fade a-d1">
        @csrf @method('PATCH')
        <h3 class="font-bold text-gray-800 border-b border-gray-100 pb-3">Edit Profile</h3>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold mb-2 text-gray-500">First Name *</label>
                <input name="name" value="{{ old('name',$user->name) }}" required class="a-input">
            </div>
            <div>
                <label class="block text-xs font-bold mb-2 text-gray-500">Last Name</label>
                <input name="last_name" value="{{ old('last_name',$user->last_name) }}" class="a-input">
            </div>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold mb-2 text-gray-500">Middle Name</label>
                <input name="middle_name" value="{{ old('middle_name',$user->middle_name) }}" class="a-input">
            </div>
            <div>
                <label class="block text-xs font-bold mb-2 text-gray-500">Phone</label>
                <input name="phone" value="{{ old('phone',$user->phone) }}" class="a-input">
            </div>
        </div>
        <div>
            <label class="block text-xs font-bold mb-2 text-gray-500">Email *</label>
            <input name="email" type="email" value="{{ old('email',$user->email) }}" required class="a-input">
        </div>
        <button type="submit" class="a-btn-primary w-full">Save Profile</button>
    </form>

    {{-- Change Password --}}
    <form method="POST" action="{{ route('admin.profile.change-password') }}" class="a-card p-6 space-y-5 a-fade a-d2">
        @csrf @method('PATCH')
        <h3 class="font-bold text-gray-800 border-b border-gray-100 pb-3">Change Password</h3>
        <div>
            <label class="block text-xs font-bold mb-2 text-gray-500">Current Password *</label>
            <input name="current_password" type="password" required class="a-input">
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold mb-2 text-gray-500">New Password *</label>
                <input name="password" type="password" required class="a-input" minlength="8">
            </div>
            <div>
                <label class="block text-xs font-bold mb-2 text-gray-500">Confirm Password *</label>
                <input name="password_confirmation" type="password" required class="a-input">
            </div>
        </div>
        <button type="submit" class="a-btn-primary w-full">Change Password</button>
    </form>

</div>

@push('scripts')
<script>
function uploadPhoto(input) {
    if (!input.files[0]) return;
    const fd = new FormData();
    fd.append('photo', input.files[0]);
    fd.append('_token', document.querySelector('meta[name=csrf-token]').content);
    fetch('{{ route("admin.profile.update-photo") }}', { method:'POST', body:fd })
        .then(r => r.json())
        .then(d => {
            if (d.success) {
                const img = document.getElementById('avatar-preview');
                if (img) img.src = d.url + '?t=' + Date.now();
            }
        });
}
</script>
@endpush
@endsection