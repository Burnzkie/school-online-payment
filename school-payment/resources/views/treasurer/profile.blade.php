{{-- resources/views/treasurer/profile.blade.php --}}
@extends('treasurer.layouts.treasurer-app')
@section('title', 'My Profile')
@section('content')
<div class="max-w-2xl mx-auto space-y-6 fade-up">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-800">My Profile</h1>
        <a href="{{ route('treasurer.profile.edit') }}" class="btn-primary">✏️ Edit Profile</a>
    </div>

    {{-- Avatar + Photo Upload --}}
    <div class="section-card p-6">
        <div class="flex items-center gap-5">
            @if(filled($user->profile_picture) && \Illuminate\Support\Facades\Storage::disk('public')->exists($user->profile_picture))
                <img src="{{ \Illuminate\Support\Facades\Storage::url($user->profile_picture) }}"
                     class="w-14 h-14 rounded-full object-cover flex-shrink-0 ring-2 ring-indigo-200"
                     style="min-width:56px; min-height:56px; max-width:56px; max-height:56px;">
            @else
                <div class="w-14 h-14 rounded-full flex items-center justify-center text-xl font-bold text-white shadow-sm flex-shrink-0"
                     style="min-width:56px; min-height:56px; background: linear-gradient(135deg, #4f46e5, #6366f1);">
                    {{ strtoupper(substr($user->name ?? 'T', 0, 1)) }}
                </div>
            @endif
            <div>
                <p class="text-xl font-bold text-gray-800">
                    {{ trim(($user->name ?? '') . ' ' . ($user->middle_name ?? '') . ' ' . ($user->last_name ?? '')) }}
                </p>
                <p class="text-sm mt-1 text-indigo-500 font-semibold">Treasurer</p>
                <p class="text-xs mt-0.5 text-gray-400">{{ $user->email }}</p>
            </div>
        </div>
        <form method="POST" action="{{ route('treasurer.profile.update-photo') }}"
              enctype="multipart/form-data"
              class="mt-5 pt-5 flex items-center gap-3 border-t border-gray-100">
            @csrf
            <input type="file" name="photo" accept="image/*" class="form-input flex-1 text-sm py-2">
            <button type="submit" class="btn-secondary">📷 Update Photo</button>
        </form>
    </div>

    {{-- Personal Information --}}
    <div class="section-card p-6">
        <h3 class="text-sm font-bold text-gray-800 mb-4">Personal Information</h3>
        <div class="grid grid-cols-2 gap-4 text-sm">
            @foreach([
                ['First Name',   $user->name        ?? '—'],
                ['Middle Name',  $user->middle_name  ?? '—'],
                ['Last Name',    $user->last_name    ?? '—'],
                ['Employee ID',  $user->employee_id  ?? '—'],
                ['Role',         'Treasurer'],
                ['Member Since', $user->created_at->format('F j, Y')],
            ] as [$label, $value])
            <div>
                <p class="text-xs font-bold uppercase tracking-wider mb-1 text-gray-400">{{ $label }}</p>
                <p class="text-gray-800 font-semibold">{{ $value ?: '—' }}</p>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Contact Information --}}
    <div class="section-card p-6">
        <h3 class="text-sm font-bold text-gray-800 mb-4">Contact Information</h3>
        <div class="grid grid-cols-2 gap-4 text-sm">
            @foreach([
                ['Mobile Number', $user->phone      ?? '—'],
                ['Work Email',    $user->work_email ?? '—'],
                ['Login Email',   $user->email],
            ] as [$label, $value])
            <div>
                <p class="text-xs font-bold uppercase tracking-wider mb-1 text-gray-400">{{ $label }}</p>
                <p class="text-gray-800 font-semibold">{{ $value ?: '—' }}</p>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Address --}}
    <div class="section-card p-6">
        <h3 class="text-sm font-bold text-gray-800 mb-4">Address</h3>
        <div class="grid grid-cols-2 gap-4 text-sm">
            @foreach([
                ['Street / Barangay',   $user->street       ?? '—'],
                ['Municipality / City', $user->municipality ?? '—'],
                ['Province',            $user->province     ?? '—'],
                ['ZIP Code',            $user->zip_code     ?? '—'],
                ['Country',             $user->country      ?? '—'],
            ] as [$label, $value])
            <div>
                <p class="text-xs font-bold uppercase tracking-wider mb-1 text-gray-400">{{ $label }}</p>
                <p class="text-gray-800 font-semibold">{{ $value ?: '—' }}</p>
            </div>
            @endforeach
        </div>
    </div>

</div>
@endsection