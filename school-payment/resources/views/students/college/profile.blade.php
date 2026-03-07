{{-- resources/views/students/college/profile.blade.php --}}
@extends('students.college.layouts.student-app')
@section('title', 'My Profile')

@push('styles')
<style>
/* ── Avatar ── */
.cp-avatar-wrap {
    position: relative; display: inline-block; cursor: pointer;
}
.cp-avatar-overlay {
    position: absolute; inset: 0; border-radius: 16px;
    background: rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center;
    opacity: 0; transition: opacity 0.2s ease;
}
.cp-avatar-wrap:hover .cp-avatar-overlay { opacity: 1; }

/* ── Field display ── */
.cp-field-row { display: flex; flex-direction: column; gap: 4px; }
.cp-field-label {
    font-size: 10px; font-weight: 700; text-transform: uppercase;
    letter-spacing: .07em; color: #6b7280;
}
.cp-field-value { font-size: 14px; font-weight: 600; color: #1f2937; }
.cp-field-na    { color: #9ca3af; font-style: italic; font-size: 13px; }

/* ── Inputs ── */
.cp-input {
    width: 100%; padding: 10px 14px; border-radius: 12px; font-size: 14px; font-weight: 500;
    font-family: 'Sora', sans-serif;
    background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.14); color: #1f2937;
    transition: all .2s ease;
}
.cp-input:focus {
    outline: none;
    border-color: #3b55e6;
    box-shadow: 0 0 0 3px rgba(59,85,230,0.2);
    background: rgba(59,85,230,0.1);
}
.cp-input::placeholder { color: #9ca3af; }
.cp-input option { background: #ffffff; }

/* ── Tabs ── */
.cp-tab {
    padding: 8px 18px; border-radius: 10px; font-size: 13px; font-weight: 700;
    cursor: pointer; transition: all .2s ease; color: rgba(255,255,255,0.4);
    background: transparent; border: 1px solid transparent;
}
.cp-tab.active {
    background: linear-gradient(135deg, rgba(59,85,230,0.35), rgba(99,102,241,0.2));
    color: #fff;
    border-color: rgba(59,85,230,0.45);
    box-shadow: 0 4px 14px rgba(59,85,230,0.2);
}
.cp-tab:not(.active):hover { color: #4f46e5; background: #f5f3ff; }

/* ── Section card ── */
.cp-card {
    border-radius: 18px; padding: 24px;
    background: rgba(255,255,255,0.06);
    border: 1px solid rgba(255,255,255,0.1);
}
.cp-divider { border-top: 1px solid #f3f4f6; margin-top: 20px; padding-top: 20px; }

/* ── Save button ── */
.cp-save-btn {
    padding: 11px 32px; border-radius: 12px; font-size: 14px; font-weight: 700;
    color: #fff; border: none; cursor: pointer; font-family: 'Sora', sans-serif;
    background: linear-gradient(135deg, #3b55e6, #6366f1);
    box-shadow: 0 4px 16px rgba(59,85,230,0.3);
    transition: all .22s ease;
}
.cp-save-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 28px rgba(79,70,229,0.3);
}

/* ── Upload status ── */
#cp-upload-status {
    font-size: 13px; font-weight: 600; border-radius: 10px;
    padding: 10px 14px; display: none; align-items: center; gap: 8px;
}

/* ── Fade in ── */
@keyframes cp-fadein {
    from { opacity: 0; transform: translateY(12px); }
    to   { opacity: 1; transform: translateY(0); }
}
.cp-fade    { animation: cp-fadein .45s ease both; }
.cp-fade-d1 { animation-delay: .06s; }
.cp-fade-d2 { animation-delay: .12s; }
.cp-fade-d3 { animation-delay: .18s; }
</style>
@endpush

@section('content')
@php $user = auth()->user(); @endphp

<div class="space-y-6" x-data="{ tab: 'info' }">

    {{-- ── Page Header ── --}}
    <div class="cp-fade cp-fade-d1 flex flex-col sm:flex-row sm:items-end justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider mb-3"
                 style="background: #eef2ff; color: #4f46e5; border: 1px solid #c7d2fe;">
                <span class="w-1.5 h-1.5 rounded-full bg-indigo-400 col-pulse"></span>
                College · Profile
            </div>
            <h1 class="font-display text-4xl sm:text-5xl text-gray-800 leading-tight">My Profile</h1>
            <p class="text-sm mt-1.5" class="text-gray-500 text-sm mt-1.5">Your enrollment and personal information on record.</p>
        </div>
    </div>

    {{-- ── Profile Hero ── --}}
    <div class="cp-fade cp-fade-d1 col-shimmer rounded-2xl p-6 sm:p-8"
         style="background: linear-gradient(135deg, #eef2ff, #e0e7ff); border: 1px solid #c7d2fe;">
        <div class="flex flex-col sm:flex-row items-center sm:items-start gap-6">

            {{-- Avatar with upload --}}
            <div class="flex-shrink-0 flex flex-col items-center gap-2">
                <form method="POST" action="{{ route('student.profile.update-photo') }}"
                      enctype="multipart/form-data" id="cp-photo-form">
                    @csrf
                    <div class="cp-avatar-wrap" onclick="document.getElementById('cp-photo-input').click()">
                        @if(filled($user->profile_picture) && Storage::disk('public')->exists($user->profile_picture))
                            <img id="cp-avatar-img"
                                 src="{{ Storage::url($user->profile_picture) }}"
                                 class="w-20 h-20 rounded-2xl object-cover"
                                 style="border: 2px solid #c7d2fe; box-shadow: 0 0 16px rgba(79,70,229,0.15);">
                        @else
                            <div id="cp-avatar-img"
                                 class="w-20 h-20 rounded-2xl flex items-center justify-center text-2xl font-black text-white"
                                 style="background: linear-gradient(135deg, #3b55e6, #6366f1); border: 2px solid #c7d2fe; box-shadow: 0 0 16px rgba(79,70,229,0.15);">
                                {{ strtoupper(substr($user->name ?? 'S', 0, 1)) }}
                            </div>
                        @endif
                        <div class="cp-avatar-overlay rounded-2xl">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                    </div>
                    <input id="cp-photo-input" type="file" name="profile_picture" class="hidden"
                           accept="image/jpeg,image/png,image/jpg,image/gif,image/webp">
                </form>
                <p class="text-[10px] font-semibold text-center" class="text-gray-400">Click to change</p>
                <div id="cp-upload-status"></div>
            </div>

            {{-- Student info --}}
            <div class="flex-1 text-center sm:text-left">
                <h2 class="font-bold text-gray-800 text-2xl leading-tight">
                    {{ trim(($user->name ?? '') . ' ' . ($user->middle_name ? strtoupper(substr($user->middle_name,0,1)).'. ' : '') . ($user->last_name ?? '')) ?: 'Student' }}
                    @if($user->suffix)<span class="text-base font-semibold text-gray-400"> {{ $user->suffix }}</span>@endif
                </h2>
                <p class="text-sm mt-1 font-semibold text-indigo-600">
                    {{ $user->student_id ?? 'ID not set' }}
                </p>
                <div class="flex flex-wrap justify-center sm:justify-start gap-2 mt-3">
                    <span class="col-badge col-badge-indigo">College</span>
                    @if($user->program)
                        <span class="col-badge col-badge-cyan">{{ strtoupper($user->program) }}</span>
                    @endif
                    @if($user->year_level)
                        <span class="col-badge col-badge-amber">{{ $user->year_level }} Year</span>
                    @endif
                    @if($user->email_verified_at)
                        <span class="col-badge col-badge-green">✓ Verified</span>
                    @endif
                </div>

                {{-- Quick stat row --}}
                <div class="flex flex-wrap justify-center sm:justify-start gap-5 mt-5 pt-4"
                     style="border-top: 1px solid #f3f4f6;">
                    <div>
                        <p class="cp-field-label">Email</p>
                        <p class="cp-field-value text-sm">{{ $user->email ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="cp-field-label">Mobile</p>
                        <p class="cp-field-value text-sm">{{ $user->phone ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="cp-field-label">Program</p>
                        <p class="cp-field-value text-sm">{{ strtoupper($user->program ?? '—') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Tabs ── --}}
    <div class="cp-fade cp-fade-d2 flex items-center gap-2 flex-wrap">
        <button @click="tab='info'"       :class="{'active': tab==='info'}"       class="cp-tab">📋 Personal</button>
        <button @click="tab='enrollment'" :class="{'active': tab==='enrollment'}" class="cp-tab">🎓 Enrollment</button>
        <button @click="tab='family'"     :class="{'active': tab==='family'}"     class="cp-tab">👨‍👩‍👧 Family</button>
        <button @click="tab='edit'"       :class="{'active': tab==='edit'}"       class="cp-tab">✏️ Edit Info</button>
    </div>

    {{-- ══════════════════════════════════════════
         TAB: Personal Information
    ══════════════════════════════════════════ --}}
    <div x-show="tab==='info'" x-transition class="cp-fade cp-fade-d3">
        <div class="cp-card">
            <h3 class="font-bold text-gray-800 text-base mb-5 flex items-center gap-2">
                <span class="w-7 h-7 rounded-lg flex items-center justify-center text-sm"
                      class="bg-indigo-50 rounded-lg">📋</span>
                Personal Information
            </h3>
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-5">
                @foreach([
                    ['First Name',   $user->name],
                    ['Middle Name',  $user->middle_name],
                    ['Last Name',    $user->last_name],
                    ['Suffix',       $user->suffix],
                    ['Birthday',     $user->birth_date ? \Carbon\Carbon::parse($user->birth_date)->format('M d, Y') : null],
                    ['Age',          $user->age],
                    ['Gender',       $user->gender],
                    ['Nationality',  $user->nationality ?? 'Filipino'],
                    ['Mobile',       $user->phone],
                ] as [$label, $val])
                <div class="cp-field-row">
                    <span class="cp-field-label">{{ $label }}</span>
                    <span class="{{ $val ? 'cp-field-value' : 'cp-field-na' }}">{{ $val ?? 'Not set' }}</span>
                </div>
                @endforeach
            </div>

            <div class="cp-divider">
                <h4 class="font-bold text-gray-800 text-sm mb-4 flex items-center gap-2">
                    <span class="text-base">📍</span> Address
                </h4>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-5">
                    @foreach([
                        ['Street',       $user->street],
                        ['Barangay',     $user->barangay],
                        ['Municipality', $user->municipality],
                        ['City',         $user->city],
                    ] as [$label, $val])
                    <div class="cp-field-row">
                        <span class="cp-field-label">{{ $label }}</span>
                        <span class="{{ $val ? 'cp-field-value' : 'cp-field-na' }}">{{ $val ?? 'Not set' }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════
         TAB: Enrollment
    ══════════════════════════════════════════ --}}
    <div x-show="tab==='enrollment'" x-transition class="cp-fade cp-fade-d3">
        <div class="cp-card">
            <h3 class="font-bold text-gray-800 text-base mb-5 flex items-center gap-2">
                <span class="w-7 h-7 rounded-lg flex items-center justify-center text-sm"
                      class="bg-indigo-50 rounded-lg">🎓</span>
                Enrollment Details
            </h3>
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-5">
                @foreach([
                    ['Level Group',  $user->level_group],
                    ['Year Level',   $user->year_level ? $user->year_level . ' Year College' : null],
                    ['Program',      $user->program ? strtoupper($user->program) : null],
                    ['Department',   $user->department],
                    ['Student ID',   $user->student_id],
                    ['Email',        $user->email],
                    ['School Year',  date('Y') . '–' . (date('Y')+1)],
                    ['Billing Type', 'Annual'],
                ] as [$label, $val])
                <div class="cp-field-row">
                    <span class="cp-field-label">{{ $label }}</span>
                    <span class="{{ $val ? 'cp-field-value' : 'cp-field-na' }}">{{ $val ?? 'Not set' }}</span>
                </div>
                @endforeach
            </div>

            <div class="cp-divider">
                <h4 class="font-bold text-gray-800 text-sm mb-4 flex items-center gap-2">
                    <span class="text-base">🔒</span> Account Status
                </h4>
                <div class="flex flex-wrap gap-3">
                    <div class="flex items-center gap-2 px-4 py-2.5 rounded-xl"
                         style="background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.08);">
                        <span class="w-2 h-2 rounded-full {{ $user->email_verified_at ? 'bg-emerald-400' : 'bg-amber-400' }}"></span>
                        <span class="text-sm font-semibold text-white/80">
                            Email {{ $user->email_verified_at ? 'Verified' : 'Unverified' }}
                        </span>
                    </div>
                    <div class="flex items-center gap-2 px-4 py-2.5 rounded-xl"
                         style="background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.08);">
                        <span class="w-2 h-2 rounded-full bg-indigo-400 col-pulse"></span>
                        <span class="text-sm font-semibold text-white/80">Active Enrollment</span>
                    </div>
                </div>
                <p class="text-xs mt-4" class="text-gray-400">
                    To update enrollment details, please visit the Registrar's Office.
                </p>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════
         TAB: Family
    ══════════════════════════════════════════ --}}
    <div x-show="tab==='family'" x-transition class="cp-fade cp-fade-d3">
        <div class="space-y-4">
            @foreach([
                ['👨 Father',   $user->father_name,   $user->father_occupation,     $user->father_contact,   'Occupation'],
                ['👩 Mother',   $user->mother_name,   $user->mother_occupation,     $user->mother_contact,   'Occupation'],
                ['🧑 Guardian', $user->guardian_name, $user->guardian_relationship, $user->guardian_contact, 'Relationship'],
            ] as [$title, $name, $occ, $contact, $occLabel])
            <div class="cp-card">
                <h4 class="font-bold text-gray-800 text-sm mb-4">{{ $title }}</h4>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                    <div class="cp-field-row">
                        <span class="cp-field-label">Full Name</span>
                        <span class="{{ $name ? 'cp-field-value' : 'cp-field-na' }}">{{ $name ?? 'Not set' }}</span>
                    </div>
                    <div class="cp-field-row">
                        <span class="cp-field-label">{{ $occLabel }}</span>
                        <span class="{{ $occ ? 'cp-field-value' : 'cp-field-na' }}">{{ $occ ?? 'Not set' }}</span>
                    </div>
                    <div class="cp-field-row">
                        <span class="cp-field-label">Contact Number</span>
                        <span class="{{ $contact ? 'cp-field-value' : 'cp-field-na' }}">{{ $contact ?? 'Not set' }}</span>
                    </div>
                </div>
            </div>
            @endforeach

            {{-- Emergency contact --}}
            <div class="cp-card">
                <h4 class="font-bold text-gray-800 text-sm mb-4">🚨 Emergency Contact</h4>
                <div class="flex flex-wrap gap-3">
                    @foreach(['father' => '👨 Father', 'mother' => '👩 Mother', 'guardian' => '🧑 Guardian'] as $val => $label)
                    @php $isSelected = ($user->emergency_contact ?? 'father') === $val; @endphp
                    <div class="flex items-center gap-2.5 px-4 py-2.5 rounded-xl transition-all"
                         style="{{ $isSelected
                            ? 'background: #eef2ff; border: 1px solid #c7d2fe; box-shadow: 0 0 0 3px rgba(79,70,229,0.08);'
                            : 'background: #f9fafb; border: 1px solid #e5e7eb;' }}">
                        <div class="w-4 h-4 rounded-full flex items-center justify-center flex-shrink-0"
                             style="{{ $isSelected ? 'border: 2px solid #4f46e5;' : 'border: 2px solid #d1d5db;' }}">
                            @if($isSelected)
                                <div class="w-2 h-2 rounded-full bg-indigo-500"></div>
                            @endif
                        </div>
                        <span class="text-sm font-bold {{ $isSelected ? 'text-indigo-700' : 'text-gray-400' }}">{{ $label }}</span>
                        @if($isSelected)
                            <svg class="w-3.5 h-3.5 text-indigo-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                        @endif
                    </div>
                    @endforeach
                </div>
                <p class="text-xs mt-4" class="text-gray-400">
                    To change your emergency contact, please visit the Registrar's Office.
                </p>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════
         TAB: Edit
    ══════════════════════════════════════════ --}}
    <div x-show="tab==='edit'" x-transition class="cp-fade cp-fade-d3">
        <form method="POST" action="{{ route('student.profile.update') }}">
            @csrf
            @method('PATCH')
            <div class="cp-card">
                <h3 class="font-bold text-white text-base mb-5 flex items-center gap-2">
                    <span class="w-7 h-7 rounded-lg flex items-center justify-center text-sm"
                          class="bg-indigo-50 rounded-lg">✏️</span>
                    Edit Personal Information
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                    @foreach([
                        ['name',         'First Name',    $user->name,         'text'],
                        ['middle_name',  'Middle Name',   $user->middle_name,  'text'],
                        ['last_name',    'Last Name',     $user->last_name,    'text'],
                        ['suffix',       'Suffix',        $user->suffix,       'text'],
                        ['phone',        'Mobile Number', $user->phone,        'tel'],
                        ['birth_date',   'Birthday',      $user->birth_date,   'date'],
                        ['nationality',  'Nationality',   $user->nationality,  'text'],
                        ['street',       'Street',        $user->street,       'text'],
                        ['barangay',     'Barangay',      $user->barangay,     'text'],
                        ['municipality', 'Municipality',  $user->municipality, 'text'],
                        ['city',         'City',          $user->city,         'text'],
                    ] as [$fname, $flabel, $fval, $ftype])
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-1.5">{{ $flabel }}</label>
                        <input type="{{ $ftype }}" name="{{ $fname }}"
                               value="{{ old($fname, $fval) }}"
                               placeholder="{{ $flabel }}"
                               class="cp-input">
                    </div>
                    @endforeach

                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-1.5">Gender</label>
                        <select name="gender" class="cp-input">
                            <option value="">Select gender</option>
                            <option value="Male"   {{ ($user->gender ?? '') == 'Male'   ? 'selected' : '' }}>Male</option>
                            <option value="Female" {{ ($user->gender ?? '') == 'Female' ? 'selected' : '' }}>Female</option>
                            <option value="Other"  {{ ($user->gender ?? '') == 'Other'  ? 'selected' : '' }}>Other / Prefer not to say</option>
                        </select>
                    </div>
                </div>

                <div class="cp-divider flex items-center gap-3">
                    <button type="submit" class="cp-save-btn">💾 Save Changes</button>
                    <button type="button" @click="tab='info'"
                            class="px-6 py-2.5 rounded-xl text-sm font-semibold transition-all"
                            style="color: #6b7280; background: #f9fafb; border: 1px solid #e5e7eb;"
                            onmouseover="this.style.color='#fff'" onmouseout="this.style.color='rgba(255,255,255,0.4)'">
                        Cancel
                    </button>
                </div>
            </div>
        </form>
    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const input  = document.getElementById('cp-photo-input');
    const form   = document.getElementById('cp-photo-form');
    const status = document.getElementById('cp-upload-status');
    const FIXED  = 200;

    input.addEventListener('change', function (e) {
        const file = e.target.files[0];
        if (!file) return;

        const validTypes = ['image/jpeg','image/png','image/jpg','image/gif','image/webp'];
        if (!validTypes.includes(file.type)) { showStatus('Invalid file type.', 'error'); input.value = ''; return; }
        if (file.size > 5 * 1024 * 1024)    { showStatus('Max file size is 5MB.', 'error'); input.value = ''; return; }

        const reader = new FileReader();
        reader.onload = function (ev) {
            const img = new Image();
            img.onload = function () {
                // Canvas resize to fixed 200×200 cover crop
                const canvas = document.createElement('canvas');
                canvas.width = canvas.height = FIXED;
                const ctx = canvas.getContext('2d');
                const scale = Math.max(FIXED / img.width, FIXED / img.height);
                const sw = FIXED / scale, sh = FIXED / scale;
                const sx = (img.width - sw) / 2, sy = (img.height - sh) / 2;
                ctx.drawImage(img, sx, sy, sw, sh, 0, 0, FIXED, FIXED);

                // Update avatar preview
                const dataUrl = canvas.toDataURL(file.type === 'image/png' ? 'image/png' : 'image/jpeg', 0.88);
                const avatarEl = document.getElementById('cp-avatar-img');
                if (avatarEl.tagName === 'IMG') {
                    avatarEl.src = dataUrl;
                } else {
                    const newImg = document.createElement('img');
                    newImg.id = 'cp-avatar-img';
                    newImg.src = dataUrl;
                    newImg.className = 'w-20 h-20 rounded-2xl object-cover';
                    newImg.style.cssText = 'border: 2px solid #c7d2fe; box-shadow: 0 0 16px rgba(79,70,229,0.15);';
                    avatarEl.replaceWith(newImg);
                }

                // Upload resized blob
                canvas.toBlob(function (blob) {
                    const resized = new File([blob], file.name, { type: blob.type, lastModified: Date.now() });
                    const fd = new FormData();
                    fd.append('_token', document.querySelector('input[name="_token"]').value);
                    fd.append('profile_picture', resized);
                    uploadPhoto(fd);
                }, file.type === 'image/png' ? 'image/png' : 'image/jpeg', 0.88);
            };
            img.src = ev.target.result;
        };
        reader.readAsDataURL(file);
    });

    function uploadPhoto(fd) {
        showStatus('Uploading…', 'loading');
        fetch(form.action, {
            method: 'POST',
            body: fd,
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                showStatus('Photo updated ✓', 'success');
                setTimeout(() => { status.style.display = 'none'; }, 3000);
            } else {
                showStatus(data.message || 'Upload failed.', 'error');
            }
        })
        .catch(() => showStatus('Network error. Try again.', 'error'));
    }

    function showStatus(msg, type) {
        const styles = {
            success: 'background:rgba(16,185,129,0.15);color:#6ee7b7;border:1px solid rgba(16,185,129,0.3);',
            error:   'background:rgba(244,63,94,0.15);color:#fda4af;border:1px solid rgba(244,63,94,0.3);',
            loading: 'background:rgba(59,85,230,0.15);color:#818cf8;border:1px solid rgba(59,85,230,0.3);',
        };
        const icons = {
            success: '<svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>',
            error:   '<svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>',
            loading: '<svg class="w-4 h-4 flex-shrink-0 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>',
        };
        status.style.cssText = `display:flex;align-items:center;gap:8px;padding:8px 12px;border-radius:10px;font-size:12px;font-weight:600;${styles[type]}`;
        status.innerHTML = `${icons[type]}<span>${msg}</span>`;
    }
});
</script>
@endsection