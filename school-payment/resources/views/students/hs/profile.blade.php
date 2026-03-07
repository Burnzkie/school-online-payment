{{-- resources/views/students/hs/profile.blade.php --}}
@extends('students.hs.layouts.hs-app')
@section('title', 'My Profile')

@push('styles')
<style>
.hp-avatar-wrap {
    position: relative; display: inline-block; cursor: pointer;
}
.hp-avatar-overlay {
    position: absolute; inset: 0; border-radius: 50%;
    background: rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center;
    opacity: 0; transition: opacity 0.2s ease;
}
.hp-avatar-wrap:hover .hp-avatar-overlay { opacity: 1; }

.hp-field-row { display: flex; flex-direction: column; gap: 4px; }
.hp-field-label {
    font-size: 10px; font-weight: 700; text-transform: uppercase;
    letter-spacing: .07em; color: #6b7280;
}
.hp-field-value { font-size: 14px; font-weight: 600; color: #1f2937; }
.hp-field-na { color: #9ca3af; font-style: italic; font-size: 13px; }

.hp-input {
    width: 100%; padding: 10px 14px; border-radius: 12px; font-size: 14px; font-weight: 500;
    background: #ffffff; border: 1px solid #d1d5db; color: #1f2937;
    transition: all .2s ease;
}
.hp-input:focus { outline: none; border-color: #4f46e5; box-shadow: 0 0 0 3px rgba(79,70,229,0.15); background: #eef2ff; }
.hp-input::placeholder { color: #9ca3af; }

.hp-tab { padding: 8px 18px; border-radius: 10px; font-size: 13px; font-weight: 700; cursor: pointer; transition: all .2s ease; color: #6b7280; background: transparent; border: 1.5px solid transparent; }
.hp-tab.active { background: #eef2ff; color: #4f46e5; border: 1.5px solid #c7d2fe; }
.hp-tab:not(.active):hover { background: #f5f3ff; color: #4f46e5; }

@keyframes hp-fadein {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}
.hp-fade { animation: hp-fadein .45s ease both; }
</style>
@endpush

@section('content')
@php
    $user = auth()->user();
    $isJHS = str_contains(strtolower($user->level_group ?? ''), 'junior');
    $isSHS = str_contains(strtolower($user->level_group ?? ''), 'senior');
    $levelLabel = $isJHS ? 'Junior High' : ($isSHS ? 'Senior High' : 'High School');
    $accentColor = $isJHS ? '#06b6d4' : '#ec4899';
@endphp

<div class="space-y-6" x-data="{ tab: 'info', editing: false }">

    {{-- ── Page Header ── --}}
    <div class="hp-fade flex flex-col sm:flex-row sm:items-end justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider mb-3"
                 style="background: #ede9fe; color: #5b21b6; border: 1px solid #ddd6fe;">
                <span class="w-1.5 h-1.5 rounded-full" style="background: {{ $accentColor }};"></span>
                {{ $levelLabel }} · Profile
            </div>
            <h1 class="font-display text-4xl sm:text-5xl text-gray-800 leading-tight">My Profile</h1>
        </div>
    </div>

    {{-- ── Profile Hero ── --}}
    <div class="hp-fade rounded-2xl p-6 sm:p-8"
         style="background: linear-gradient(135deg, #eef2ff, #e0e7ff); border: 1px solid #c7d2fe;">
        <div class="flex flex-col sm:flex-row items-center sm:items-start gap-6">

            {{-- Avatar --}}
            <div class="flex-shrink-0">
                <form method="POST" action="{{ route('hs.profile.update-photo') }}" enctype="multipart/form-data" id="hp-photo-form">
                    @csrf
                    <div class="hp-avatar-wrap" onclick="document.getElementById('hp-photo-input').click()">
                        @if(filled($user->profile_picture) && Storage::disk('public')->exists($user->profile_picture))
                            <img src="{{ Storage::url($user->profile_picture) }}"
                                 class="w-14 h-14 rounded-2xl object-cover"
                                 style="border: 2px solid #c7d2fe; box-shadow: 0 0 12px rgba(79,70,229,0.15);">
                        @else
                            <div class="w-14 h-14 rounded-2xl flex items-center justify-center text-lg font-bold text-white"
                                 style="background: linear-gradient(135deg, #4f46e5, #7c3aed); border: 2px solid #c7d2fe; box-shadow: 0 0 12px rgba(79,70,229,0.15);">
                                {{ strtoupper(substr($user->name ?? 'S', 0, 1)) }}
                            </div>
                        @endif
                        <div class="hp-avatar-overlay rounded-2xl">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                    </div>
                    <input id="hp-photo-input" type="file" name="profile_picture" class="hidden" accept="image/*"
                           onchange="document.getElementById('hp-photo-form').submit()">
                </form>
                <p class="text-center text-[10px] mt-2 font-semibold" class="text-gray-400">Click to change</p>
            </div>

            {{-- Info --}}
            <div class="flex-1 text-center sm:text-left">
                <h2 class="font-bold text-gray-800 text-2xl">{{ $user->name ?? '' }} {{ $user->last_name ?? '' }}</h2>
                <p class="text-sm mt-1 font-semibold text-indigo-600">
                    {{ $user->student_id ?? 'Student' }}
                </p>
                <div class="flex flex-wrap justify-center sm:justify-start gap-2 mt-3">
                    <span class="hs-badge {{ $isJHS ? 'hs-badge-cyan' : 'hs-badge-violet' }}">{{ $levelLabel }}</span>
                    @if($user->year_level)
                    <span class="hs-badge hs-badge-amber">{{ $user->year_level }}</span>
                    @endif
                    @if($isSHS && $user->strand)
                    <span class="hs-badge hs-badge-violet">{{ $user->strand }}</span>
                    @endif
                    @if($user->email_verified_at)
                    <span class="hs-badge hs-badge-green">✓ Verified</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- ── Tabs ── --}}
    <div class="flex items-center gap-2 flex-wrap hp-fade">
        <button @click="tab='info'; editing=false" :class="{'active': tab==='info'}" class="hp-tab">📋 Info</button>
        <button @click="tab='enrollment'; editing=false" :class="{'active': tab==='enrollment'}" class="hp-tab">🎓 Enrollment</button>
        <button @click="tab='family'; editing=false" :class="{'active': tab==='family'}" class="hp-tab">👨‍👩‍👧 Family</button>
        <button @click="tab='edit'; editing=true" :class="{'active': tab==='edit'}" class="hp-tab">✏️ Edit Info</button>
    </div>

    {{-- ── Tab: Personal Info ── --}}
    <div x-show="tab==='info'" x-transition>
        <div class="rounded-2xl p-6" style="background: #ffffff; border: 1px solid #e5e7eb;">
            <h3 class="font-bold text-gray-800 mb-5">Personal Information</h3>
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-5">
                @foreach([
                    ['First Name', $user->name],
                    ['Middle Name', $user->middle_name],
                    ['Last Name', $user->last_name],
                    ['Suffix', $user->suffix],
                    ['Birthday', $user->birth_date ? \Carbon\Carbon::parse($user->birth_date)->format('M d, Y') : null],
                    ['Age', $user->age],
                    ['Gender', $user->gender],
                    ['Nationality', $user->nationality ?? 'Filipino'],
                    ['Mobile', $user->phone],
                ] as [$label, $val])
                <div class="hp-field-row">
                    <span class="hp-field-label">{{ $label }}</span>
                    <span class="{{ $val ? 'hp-field-value' : 'hp-field-na' }}">{{ $val ?? 'Not set' }}</span>
                </div>
                @endforeach
            </div>
            <div class="mt-5 pt-5" style="border-top: 1px solid #f3f4f6;">
                <h4 class="font-bold text-gray-800 text-sm mb-3">Address</h4>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-5">
                    @foreach([['Street', $user->street], ['Barangay', $user->barangay], ['Municipality', $user->municipality], ['City', $user->city]] as [$l, $v])
                    <div class="hp-field-row">
                        <span class="hp-field-label">{{ $l }}</span>
                        <span class="{{ $v ? 'hp-field-value' : 'hp-field-na' }}">{{ $v ?? 'Not set' }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- ── Tab: Enrollment ── --}}
    <div x-show="tab==='enrollment'" x-transition>
        <div class="rounded-2xl p-6" style="background: #ffffff; border: 1px solid #e5e7eb;">
            <h3 class="font-bold text-gray-800 mb-5">Enrollment Details</h3>
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-5">
                @foreach([
                    ['Level Group', $user->level_group],
                    ['Year Level', $user->year_level],
                    ['Strand', $isSHS ? ($user->strand ?? 'Not set') : 'N/A (JHS)'],
                    ['Student ID', $user->student_id],
                    ['Email', $user->email],
                ] as [$label, $val])
                <div class="hp-field-row">
                    <span class="hp-field-label">{{ $label }}</span>
                    <span class="{{ $val && $val !== 'Not set' && $val !== 'N/A (JHS)' ? 'hp-field-value' : 'hp-field-na' }}">{{ $val ?? 'Not set' }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- ── Tab: Family ── --}}
    <div x-show="tab==='family'" x-transition>
        <div class="space-y-4">
            @foreach([
                ['👨 Father', $user->father_name, $user->father_occupation, $user->father_contact],
                ['👩 Mother', $user->mother_name, $user->mother_occupation, $user->mother_contact],
                ['🧑 Guardian', $user->guardian_name, $user->guardian_relationship, $user->guardian_contact],
            ] as [$title, $name, $occ, $contact])
            <div class="rounded-2xl p-5" style="background: #ffffff; border: 1px solid #e5e7eb;">
                <h4 class="font-bold text-gray-800 text-sm mb-4">{{ $title }}</h4>
                <div class="grid grid-cols-3 gap-5">
                    <div class="hp-field-row">
                        <span class="hp-field-label">Name</span>
                        <span class="{{ $name ? 'hp-field-value' : 'hp-field-na' }}">{{ $name ?? 'Not set' }}</span>
                    </div>
                    <div class="hp-field-row">
                        <span class="hp-field-label">{{ $title === '🧑 Guardian' ? 'Relationship' : 'Occupation' }}</span>
                        <span class="{{ $occ ? 'hp-field-value' : 'hp-field-na' }}">{{ $occ ?? 'Not set' }}</span>
                    </div>
                    <div class="hp-field-row">
                        <span class="hp-field-label">Contact</span>
                        <span class="{{ $contact ? 'hp-field-value' : 'hp-field-na' }}">{{ $contact ?? 'Not set' }}</span>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- ── Tab: Edit ── --}}
    <div x-show="tab==='edit'" x-transition>
        <form method="POST" action="{{ route('hs.profile.update') }}">
            @csrf
            @method('PATCH')
            <div class="rounded-2xl p-6" style="background: #ffffff; border: 1px solid #e5e7eb;">
                <h3 class="font-bold text-gray-800 mb-5">Edit Personal Information</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                    @foreach([
                        ['name', 'First Name', $user->name, 'text'],
                        ['middle_name', 'Middle Name', $user->middle_name, 'text'],
                        ['last_name', 'Last Name', $user->last_name, 'text'],
                        ['suffix', 'Suffix', $user->suffix, 'text'],
                        ['phone', 'Mobile Number', $user->phone, 'tel'],
                        ['birth_date', 'Birthday', $user->birth_date, 'date'],
                        ['street', 'Street', $user->street, 'text'],
                        ['barangay', 'Barangay', $user->barangay, 'text'],
                        ['municipality', 'Municipality', $user->municipality, 'text'],
                        ['city', 'City', $user->city, 'text'],
                    ] as [$fname, $flabel, $fval, $ftype])
                    <div>
                        <label class="hp-field-label block mb-2">{{ $flabel }}</label>
                        <input type="{{ $ftype }}" name="{{ $fname }}" value="{{ old($fname, $fval) }}"
                               class="hp-input" placeholder="{{ $flabel }}">
                    </div>
                    @endforeach

                    <div>
                        <label class="hp-field-label block mb-2">Gender</label>
                        <select name="gender" class="hp-input"
                                style="background: #ffffff; appearance: none;">
                            <option value="">Select</option>
                            <option value="Male" {{ $user->gender == 'Male' ? 'selected' : '' }} style="background: #ffffff;">Male</option>
                            <option value="Female" {{ $user->gender == 'Female' ? 'selected' : '' }} style="background: #ffffff;">Female</option>
                        </select>
                    </div>
                </div>

                <div class="mt-6 pt-5" style="border-top: 1px solid #f3f4f6;">
                    <button type="submit"
                            class="px-8 py-3 rounded-xl font-bold text-white text-sm transition-all"
                            style="background: linear-gradient(135deg, #4f46e5, #7c3aed);"
                            onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 12px 28px rgba(79,70,229,0.3)'"
                            onmouseout="this.style.transform=''; this.style.boxShadow=''">
                        💾 Save Changes
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection