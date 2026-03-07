{{-- resources/views/cashier/profile.blade.php --}}
@extends('cashier.layouts.cashier-app')
@section('title', 'My Profile')

@push('styles')
<style>
    .cp-avatar-wrap { position: relative; display: inline-block; cursor: pointer; }
    .cp-avatar-overlay {
        position: absolute; inset: 0; border-radius: 18px;
        background: rgba(0,0,0,0.42); display: flex; align-items: center; justify-content: center;
        opacity: 0; transition: opacity 0.2s ease;
    }
    .cp-avatar-wrap:hover .cp-avatar-overlay { opacity: 1; }

    .cp-label { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: .07em; color: #9ca3af; display: block; margin-bottom: 4px; }
    .cp-value { font-size: 14px; font-weight: 600; color: #1f2937; }
    .cp-na    { font-size: 13px; font-weight: 400; color: #d1d5db; font-style: italic; }

    .cp-tab {
        padding: 8px 18px; border-radius: 12px; font-size: 13px; font-weight: 700;
        cursor: pointer; transition: all .2s ease; color: #6b7280;
        border: 1.5px solid transparent; white-space: nowrap;
    }
    .cp-tab.active { background: #eef2ff; color: #4f46e5; border-color: #c7d2fe; }
    .cp-tab:not(.active):hover { background: #f5f3ff; color: #4f46e5; }

    .cp-num {
        display: inline-flex; align-items: center; justify-content: center;
        width: 22px; height: 22px; border-radius: 8px;
        background: #eef2ff; color: #4f46e5; font-size: 11px; font-weight: 800; flex-shrink: 0;
    }
</style>
@endpush

@section('content')
@php $user = auth()->user(); @endphp

<div class="space-y-6" x-data="{ tab: 'info' }">

    {{-- ── Page Header ── --}}
    <div class="c-fade">
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider mb-3 bg-indigo-50 text-indigo-500 border border-indigo-100">
            <span class="w-1.5 h-1.5 rounded-full c-pulse bg-indigo-400"></span>
            Cashier · My Profile
        </div>
        <h1 class="font-display text-4xl sm:text-5xl text-gray-800 leading-tight">My Profile</h1>
        <p class="text-sm mt-1.5 text-gray-400">Manage your personal information, contact details, and account settings.</p>
    </div>

    {{-- ── Hero Card ── --}}
    <div class="c-fade rounded-2xl p-6 sm:p-8 border border-indigo-100 shadow-sm"
         style="background: linear-gradient(135deg, #eef2ff 0%, #e0e7ff 60%, #f0f9ff 100%);">
        <div class="flex flex-col sm:flex-row items-center sm:items-start gap-6">

            {{-- Avatar --}}
            <div class="flex-shrink-0 flex flex-col items-center gap-2">
                <form method="POST" action="{{ route('cashier.profile.update-photo') }}"
                      enctype="multipart/form-data" id="cp-photo-form">
                    @csrf
                    <div class="cp-avatar-wrap" onclick="document.getElementById('cp-photo-input').click()" title="Click to change photo">
                        @if(filled($user->profile_picture) && Storage::disk('public')->exists($user->profile_picture))
                            <img src="{{ Storage::url($user->profile_picture) }}"
                                 class="w-24 h-24 rounded-[18px] object-cover"
                                 style="border: 2px solid #c7d2fe; box-shadow: 0 0 0 4px #e0e7ff;">
                        @else
                            <div class="w-24 h-24 rounded-[18px] flex items-center justify-center text-3xl font-bold text-white"
                                 style="background: linear-gradient(135deg, #4f46e5, #6366f1); border: 2px solid #c7d2fe; box-shadow: 0 0 0 4px #e0e7ff;">
                                {{ strtoupper(substr($user->name ?? 'C', 0, 1)) }}
                            </div>
                        @endif
                        <div class="cp-avatar-overlay">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                    </div>
                    <input id="cp-photo-input" type="file" name="profile_picture" class="hidden"
                           accept="image/*" onchange="document.getElementById('cp-photo-form').submit()">
                </form>
                <p class="text-[10px] font-semibold text-indigo-400 text-center">Click to change photo</p>
            </div>

            {{-- Name, email, badges --}}
            <div class="flex-1 text-center sm:text-left">
                <h2 class="font-bold text-gray-800 text-2xl sm:text-3xl leading-tight">
                    {{ trim(implode(' ', array_filter([$user->name, $user->middle_name, $user->last_name, $user->suffix]))) ?: 'Cashier' }}
                </h2>
                <p class="text-sm mt-1 text-indigo-600 font-semibold">{{ $user->email }}</p>
                <div class="flex flex-wrap justify-center sm:justify-start gap-2 mt-3">
                    <span class="c-badge c-badge-violet">💼 Cashier Staff</span>
                    @if($user->position)
                        <span class="c-badge c-badge-gray">{{ $user->position }}</span>
                    @endif
                    @if($user->phone)
                        <span class="c-badge c-badge-cyan">📞 {{ $user->phone }}</span>
                    @endif
                    @if($user->email_verified_at)
                        <span class="c-badge c-badge-green">✓ Verified</span>
                    @else
                        <span class="c-badge c-badge-amber">⚠ Unverified</span>
                    @endif
                </div>
                @if($user->street || $user->barangay || $user->municipality || $user->city)
                    <p class="text-xs mt-3 text-gray-400 flex items-center justify-center sm:justify-start gap-1">
                        <span>📍</span>
                        {{ implode(', ', array_filter([$user->street, $user->barangay, $user->municipality, $user->city])) }}
                    </p>
                @endif
            </div>
        </div>
    </div>

    {{-- ── Tabs ── --}}
    <div class="flex items-center gap-2 flex-wrap c-fade">
        <button @click="tab='info'"     :class="{'active': tab==='info'}"     class="cp-tab">📋 Personal Info</button>
        <button @click="tab='contact'"  :class="{'active': tab==='contact'}"  class="cp-tab">📞 Contact</button>
        <button @click="tab='address'"  :class="{'active': tab==='address'}"  class="cp-tab">📍 Address</button>
        <button @click="tab='security'" :class="{'active': tab==='security'}" class="cp-tab">🔒 Security</button>
        <button @click="tab='edit'"     :class="{'active': tab==='edit'}"     class="cp-tab">✏️ Edit Profile</button>
    </div>

    {{-- ══ TAB: Personal Info ══ --}}
    <div x-show="tab==='info'" x-transition.opacity>
        <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
            <h3 class="font-bold text-gray-800 text-base mb-5">Personal Information</h3>
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-x-6 gap-y-5">
                @foreach([
                    ['First Name',    $user->name],
                    ['Middle Name',   $user->middle_name],
                    ['Last Name',     $user->last_name],
                    ['Suffix',        $user->suffix],
                    ['Date of Birth', $user->birth_date ? \Carbon\Carbon::parse($user->birth_date)->format('M d, Y') : null],
                    ['Gender',        $user->gender],
                ] as [$label, $val])
                <div>
                    <span class="cp-label">{{ $label }}</span>
                    <span class="{{ $val ? 'cp-value' : 'cp-na' }}">{{ $val ?? 'Not set' }}</span>
                </div>
                @endforeach
            </div>
            <div class="mt-6 pt-5 border-t border-gray-100">
                <h4 class="font-bold text-gray-700 text-sm mb-4">Employment</h4>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-x-6 gap-y-5">
                    @foreach([
                        ['Email',       $user->email],
                        ['Position',    $user->position ?? 'Cashier Staff'],
                        ['Employee ID', $user->employee_id],
                    ] as [$label, $val])
                    <div>
                        <span class="cp-label">{{ $label }}</span>
                        <span class="{{ $val ? 'cp-value' : 'cp-na' }}">{{ $val ?? 'Not set' }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- ══ TAB: Contact ══ --}}
    <div x-show="tab==='contact'" x-transition.opacity>
        <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
            <h3 class="font-bold text-gray-800 text-base mb-5">Contact Information</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                @foreach([
                    ['📧', 'Email Address',   $user->email,                  'bg-indigo-50  border-indigo-100'],
                    ['📞', 'Mobile Number',   $user->phone,                  'bg-cyan-50    border-cyan-100'],
                    ['📱', 'Alternate Phone', $user->alternate_phone ?? null, 'bg-emerald-50 border-emerald-100'],
                    ['🏢', 'Position / Role', $user->position ?? 'Cashier Staff', 'bg-violet-50  border-violet-100'],
                ] as [$icon, $label, $val, $colors])
                <div class="p-4 rounded-xl flex items-center gap-4 border {{ $colors }}">
                    <div class="w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0 text-xl bg-white shadow-sm">{{ $icon }}</div>
                    <div class="min-w-0">
                        <span class="cp-label">{{ $label }}</span>
                        <span class="{{ $val ? 'cp-value' : 'cp-na' }} block truncate">{{ $val ?? 'Not set' }}</span>
                    </div>
                </div>
                @endforeach
            </div>
            <div class="mt-5 pt-5 border-t border-gray-100 text-center">
                <button @click="tab='edit'"
                        class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl font-bold text-white text-sm shadow-sm transition-all hover:scale-[1.02]"
                        style="background: linear-gradient(135deg, #4f46e5, #6366f1);">
                    ✏️ Update Contact Info
                </button>
            </div>
        </div>
    </div>

    {{-- ══ TAB: Address ══ --}}
    <div x-show="tab==='address'" x-transition.opacity>
        <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
            <h3 class="font-bold text-gray-800 text-base mb-5">Address</h3>
            @php $hasAddress = $user->street || $user->barangay || $user->municipality || $user->city; @endphp
            @if($hasAddress)
            <div class="p-4 rounded-xl mb-5 flex items-start gap-3"
                 style="background: #eef2ff; border: 1px solid #c7d2fe;">
                <span class="text-base mt-0.5">📍</span>
                <p class="text-sm font-semibold text-indigo-800 leading-relaxed">
                    {{ implode(', ', array_filter([$user->street, $user->barangay, $user->municipality, $user->city])) }}
                </p>
            </div>
            @else
            <div class="p-5 rounded-xl mb-5 bg-gray-50 border border-dashed border-gray-200 text-center">
                <p class="text-sm text-gray-400">No address saved yet. Click "Update Address" to add one.</p>
            </div>
            @endif
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-x-6 gap-y-5">
                @foreach([
                    ['Street / House No.', $user->street],
                    ['Barangay',           $user->barangay],
                    ['Municipality',       $user->municipality],
                    ['City / Province',    $user->city],
                ] as [$label, $val])
                <div>
                    <span class="cp-label">{{ $label }}</span>
                    <span class="{{ $val ? 'cp-value' : 'cp-na' }}">{{ $val ?? 'Not set' }}</span>
                </div>
                @endforeach
            </div>
            <div class="mt-5 pt-5 border-t border-gray-100 text-center">
                <button @click="tab='edit'"
                        class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl font-bold text-white text-sm shadow-sm transition-all hover:scale-[1.02]"
                        style="background: linear-gradient(135deg, #4f46e5, #6366f1);">
                    ✏️ Update Address
                </button>
            </div>
        </div>
    </div>

    {{-- ══ TAB: Security ══ --}}
    <div x-show="tab==='security'" x-transition.opacity>
        <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm space-y-6">
            <h3 class="font-bold text-gray-800 text-base">Security Settings</h3>
            <div class="flex items-center gap-4 p-4 rounded-xl
                {{ $user->email_verified_at ? 'bg-emerald-50 border border-emerald-100' : 'bg-amber-50 border border-amber-100' }}">
                <div class="text-2xl">{{ $user->email_verified_at ? '✅' : '⚠️' }}</div>
                <div>
                    <p class="font-bold text-sm {{ $user->email_verified_at ? 'text-emerald-800' : 'text-amber-800' }}">
                        {{ $user->email_verified_at ? 'Email Verified' : 'Email Not Yet Verified' }}
                    </p>
                    <p class="text-xs mt-0.5 {{ $user->email_verified_at ? 'text-emerald-600' : 'text-amber-600' }}">
                        {{ $user->email_verified_at
                            ? 'Verified on ' . \Carbon\Carbon::parse($user->email_verified_at)->format('F d, Y')
                            : 'A verification link was sent to ' . $user->email }}
                    </p>
                </div>
            </div>
            <form method="POST" action="{{ route('cashier.profile.update-password') }}" class="space-y-4">
                @csrf @method('PATCH')
                <h4 class="font-bold text-gray-700 text-sm">Change Password</h4>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="cp-label">Current Password</label>
                        <input type="password" name="current_password" class="c-input" placeholder="••••••••">
                        @error('current_password') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="cp-label">New Password</label>
                        <input type="password" name="password" class="c-input" placeholder="Min. 8 characters" minlength="8">
                        @error('password') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="cp-label">Confirm New Password</label>
                        <input type="password" name="password_confirmation" class="c-input" placeholder="Re-enter password">
                    </div>
                </div>
                <button type="submit"
                        class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl font-bold text-white text-sm shadow-sm transition-all hover:scale-[1.02]"
                        style="background: linear-gradient(135deg, #4f46e5, #6366f1);">
                    🔒 Update Password
                </button>
            </form>
        </div>
    </div>

    {{-- ══ TAB: Edit Profile ══ --}}
    <div x-show="tab==='edit'" x-transition.opacity>
        <form method="POST" action="{{ route('cashier.profile.update') }}" class="space-y-5">
            @csrf @method('PATCH')

            {{-- 01 Personal --}}
            <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
                <h3 class="font-bold text-gray-800 text-base mb-5 flex items-center gap-2">
                    <span class="cp-num">01</span> Personal Information
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                    <div>
                        <label class="cp-label">First Name *</label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                               class="c-input" placeholder="First name">
                        @error('name') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="cp-label">Middle Name</label>
                        <input type="text" name="middle_name" value="{{ old('middle_name', $user->middle_name) }}"
                               class="c-input" placeholder="Middle name">
                    </div>
                    <div>
                        <label class="cp-label">Last Name *</label>
                        <input type="text" name="last_name" value="{{ old('last_name', $user->last_name) }}" required
                               class="c-input" placeholder="Last name">
                        @error('last_name') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="cp-label">Suffix</label>
                        <input type="text" name="suffix" value="{{ old('suffix', $user->suffix) }}"
                               class="c-input" placeholder="Jr, Sr, III">
                    </div>
                    <div>
                        <label class="cp-label">Date of Birth</label>
                        <input type="date" name="birth_date" value="{{ old('birth_date', $user->birth_date) }}"
                               class="c-input">
                    </div>
                    <div>
                        <label class="cp-label">Gender</label>
                        <select name="gender" class="c-input c-select">
                            <option value="">Select</option>
                            <option value="Male"   {{ old('gender', $user->gender) === 'Male'   ? 'selected' : '' }}>Male</option>
                            <option value="Female" {{ old('gender', $user->gender) === 'Female' ? 'selected' : '' }}>Female</option>
                        </select>
                    </div>
                    <div>
                        <label class="cp-label">Position / Title</label>
                        <input type="text" name="position" value="{{ old('position', $user->position) }}"
                               class="c-input" placeholder="e.g. Head Cashier">
                    </div>
                    <div>
                        <label class="cp-label">Employee ID</label>
                        <input type="text" name="employee_id" value="{{ old('employee_id', $user->employee_id) }}"
                               class="c-input" placeholder="e.g. EMP-2025-001">
                    </div>
                </div>
            </div>

            {{-- 02 Contact --}}
            <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
                <h3 class="font-bold text-gray-800 text-base mb-5 flex items-center gap-2">
                    <span class="cp-num">02</span> Contact Information
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                    <div>
                        <label class="cp-label">Email Address *</label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                               class="c-input" placeholder="email@example.com">
                        @error('email') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="cp-label">Mobile Number</label>
                        <input type="tel" name="phone" value="{{ old('phone', $user->phone) }}"
                               class="c-input" placeholder="09XX XXX XXXX" maxlength="11">
                        @error('phone') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="cp-label">Alternate Phone</label>
                        <input type="tel" name="alternate_phone" value="{{ old('alternate_phone', $user->alternate_phone) }}"
                               class="c-input" placeholder="Landline or secondary number">
                    </div>
                </div>
            </div>

            {{-- 03 Address --}}
            <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
                <h3 class="font-bold text-gray-800 text-base mb-5 flex items-center gap-2">
                    <span class="cp-num">03</span> Address
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div class="sm:col-span-2">
                        <label class="cp-label">Street / House No.</label>
                        <input type="text" name="street" value="{{ old('street', $user->street) }}"
                               class="c-input" placeholder="e.g. 123 Rizal St.">
                    </div>
                    <div>
                        <label class="cp-label">Barangay</label>
                        <input type="text" name="barangay" value="{{ old('barangay', $user->barangay) }}"
                               class="c-input" placeholder="Barangay">
                    </div>
                    <div>
                        <label class="cp-label">Municipality</label>
                        <input type="text" name="municipality" value="{{ old('municipality', $user->municipality) }}"
                               class="c-input" placeholder="Municipality">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="cp-label">City / Province</label>
                        <input type="text" name="city" value="{{ old('city', $user->city) }}"
                               class="c-input" placeholder="City or Province">
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <button type="submit"
                        class="px-8 py-3 rounded-xl font-bold text-white text-sm shadow-sm transition-all hover:scale-[1.02]"
                        style="background: linear-gradient(135deg, #4f46e5, #6366f1);">
                    💾 Save Changes
                </button>
                <button type="button" @click="tab='info'"
                        class="px-6 py-3 rounded-xl font-semibold text-sm text-gray-500 bg-white border border-gray-200 hover:bg-gray-50 transition-all">
                    Cancel
                </button>
            </div>
        </form>
    </div>

</div>
@endsection