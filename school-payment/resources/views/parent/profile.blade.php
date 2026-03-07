{{-- resources/views/parent/profile.blade.php --}}
@extends('parent.layouts.app')

@section('title', 'My Profile')
@section('page-title', 'My Profile')

@section('content')

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- ── Avatar Card ── --}}
    <div class="bg-white border border-gray-100 rounded-2xl shadow-sm p-6 flex flex-col items-center text-center">
        <div class="relative group mb-4">
            @if($parent->profile_picture)
                <img src="{{ asset('storage/'.$parent->profile_picture) }}"
                     class="w-28 h-28 rounded-full object-cover ring-4 ring-indigo-100">
            @else
                <div class="w-28 h-28 rounded-full flex items-center justify-center text-4xl font-bold text-white ring-4 ring-indigo-100"
                     style="background: linear-gradient(135deg, #4f46e5, #6366f1);">
                    {{ strtoupper(substr($parent->name, 0, 1)) }}
                </div>
            @endif
            <label for="photo-upload"
                   class="absolute inset-0 rounded-full flex items-center justify-center bg-black/40
                          opacity-0 group-hover:opacity-100 transition cursor-pointer">
                <i class="fas fa-camera text-white text-xl"></i>
            </label>
        </div>

        <form method="POST" action="{{ route('parent.profile.update-photo') }}" enctype="multipart/form-data" id="photo-form">
            @csrf
            <input type="file" id="photo-upload" name="photo" accept="image/*" class="hidden"
                   onchange="document.getElementById('photo-form').submit()">
        </form>

        <h3 class="font-bold text-lg text-gray-800">{{ $parent->name }} {{ $parent->last_name }}</h3>
        <p class="text-gray-400 text-sm mt-1">{{ $parent->email }}</p>
        <span class="mt-3 px-3 py-1 bg-indigo-50 text-indigo-600 text-xs rounded-full font-semibold border border-indigo-100">
            Parent
        </span>

        <a href="{{ route('parent.profile.edit') }}"
           class="mt-6 w-full text-white text-sm font-semibold py-2.5 rounded-xl transition shadow-sm text-center block"
           style="background: linear-gradient(135deg, #4f46e5, #6366f1);">
            <i class="fas fa-edit mr-1.5"></i> Edit Profile
        </a>
    </div>

    {{-- ── Info ── --}}
    <div class="lg:col-span-2 bg-white border border-gray-100 rounded-2xl shadow-sm p-6">
        <h3 class="font-bold text-lg mb-5 flex items-center gap-2 text-gray-800">
            <i class="fas fa-user text-indigo-400"></i> Personal Information
        </h3>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
            @php
            $fields = [
                ['label' => 'First Name', 'value' => $parent->name],
                ['label' => 'Middle Name', 'value' => $parent->middle_name],
                ['label' => 'Last Name', 'value' => $parent->last_name],
                ['label' => 'Email', 'value' => $parent->email],
                ['label' => 'Phone / Mobile', 'value' => $parent->phone],
                ['label' => 'Gender', 'value' => $parent->gender],
                ['label' => 'Birth Date', 'value' => $parent->birth_date ? \Carbon\Carbon::parse($parent->birth_date)->format('F j, Y') : null],
                ['label' => 'Nationality', 'value' => $parent->nationality],
                ['label' => 'Street', 'value' => $parent->street],
                ['label' => 'Barangay', 'value' => $parent->barangay],
                ['label' => 'Municipality', 'value' => $parent->municipality],
                ['label' => 'City / Province', 'value' => $parent->city],
            ];
            @endphp
            @foreach($fields as $field)
            <div>
                <p class="text-xs text-gray-400 font-bold uppercase tracking-wider mb-1">{{ $field['label'] }}</p>
                <p class="text-sm font-medium {{ $field['value'] ? 'text-gray-800' : 'text-gray-300 italic' }}">
                    {{ $field['value'] ?? 'Not provided' }}
                </p>
            </div>
            @endforeach
        </div>

        @if($parent->extra_info)
        <div class="mt-5 pt-5 border-t border-gray-100">
            <p class="text-xs text-gray-400 font-bold uppercase tracking-wider mb-1">Additional Info</p>
            <p class="text-sm text-gray-700">{{ $parent->extra_info }}</p>
        </div>
        @endif
    </div>

</div>

@endsection