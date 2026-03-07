{{-- resources/views/parent/profile-edit.blade.php --}}
@extends('parent.layouts.app')

@section('title', 'Edit Profile')
@section('page-title', 'Edit Profile')
@section('breadcrumb', 'Profile › Edit')

@section('content')

<div class="max-w-2xl">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('parent.profile') }}"
           class="w-9 h-9 rounded-xl flex items-center justify-center text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition">
            <i class="fas fa-arrow-left text-sm"></i>
        </a>
        <h2 class="font-bold text-lg text-gray-800">Edit Profile</h2>
    </div>

    <form method="POST" action="{{ route('parent.profile.update') }}" class="space-y-6">
        @csrf
        @method('PATCH')

        @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-600 px-5 py-4 rounded-2xl text-sm">
            <ul class="list-disc pl-4 space-y-1">
                @foreach($errors->all() as $e)
                <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        {{-- ── Personal Information ── --}}
        <div class="bg-white border border-gray-100 rounded-2xl shadow-sm p-6">
            <h3 class="font-bold text-xs text-gray-400 uppercase tracking-wider mb-5 flex items-center gap-2">
                <i class="fas fa-user text-indigo-400"></i> Personal Information
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1.5">First Name <span class="text-red-400">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $parent->name) }}" required
                           class="w-full bg-white border border-gray-200 rounded-xl px-4 py-3 text-sm text-gray-800
                                  focus:outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 transition">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1.5">Middle Name</label>
                    <input type="text" name="middle_name" value="{{ old('middle_name', $parent->middle_name) }}"
                           class="w-full bg-white border border-gray-200 rounded-xl px-4 py-3 text-sm text-gray-800
                                  focus:outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 transition">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1.5">Last Name</label>
                    <input type="text" name="last_name" value="{{ old('last_name', $parent->last_name) }}"
                           class="w-full bg-white border border-gray-200 rounded-xl px-4 py-3 text-sm text-gray-800
                                  focus:outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 transition">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1.5">Phone / Mobile</label>
                    <input type="tel" name="phone" value="{{ old('phone', $parent->phone) }}"
                           class="w-full bg-white border border-gray-200 rounded-xl px-4 py-3 text-sm text-gray-800
                                  focus:outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 transition"
                           placeholder="09XX XXX XXXX" maxlength="11">
                    <p class="text-xs text-gray-400 mt-1">
                        <i class="fas fa-info-circle text-indigo-400 mr-1"></i>
                        Used to link you with your children's enrollment records.
                    </p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1.5">Gender</label>
                    <select name="gender"
                            class="w-full bg-white border border-gray-200 rounded-xl px-4 py-3 text-sm text-gray-800
                                   focus:outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 transition">
                        <option value="">Not specified</option>
                        <option value="MALE"   {{ old('gender', $parent->gender) === 'MALE'   ? 'selected' : '' }}>Male</option>
                        <option value="FEMALE" {{ old('gender', $parent->gender) === 'FEMALE' ? 'selected' : '' }}>Female</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1.5">Birth Date</label>
                    <input type="date" name="birth_date"
                           value="{{ old('birth_date', $parent->birth_date ? \Carbon\Carbon::parse($parent->birth_date)->format('Y-m-d') : '') }}"
                           class="w-full bg-white border border-gray-200 rounded-xl px-4 py-3 text-sm text-gray-800
                                  focus:outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 transition">
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-600 mb-1.5">Nationality</label>
                    <input type="text" name="nationality" value="{{ old('nationality', $parent->nationality) }}"
                           class="w-full bg-white border border-gray-200 rounded-xl px-4 py-3 text-sm text-gray-800
                                  focus:outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 transition"
                           placeholder="e.g., Filipino">
                </div>
            </div>
        </div>

        {{-- ── Address ── --}}
        <div class="bg-white border border-gray-100 rounded-2xl shadow-sm p-6">
            <h3 class="font-bold text-xs text-gray-400 uppercase tracking-wider mb-5 flex items-center gap-2">
                <i class="fas fa-map-marker-alt text-indigo-400"></i> Address
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-600 mb-1.5">Street / House No.</label>
                    <input type="text" name="street" value="{{ old('street', $parent->street) }}"
                           class="w-full bg-white border border-gray-200 rounded-xl px-4 py-3 text-sm text-gray-800
                                  focus:outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 transition"
                           placeholder="e.g., 123 Rizal St.">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1.5">Barangay</label>
                    <input type="text" name="barangay" value="{{ old('barangay', $parent->barangay) }}"
                           class="w-full bg-white border border-gray-200 rounded-xl px-4 py-3 text-sm text-gray-800
                                  focus:outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 transition">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1.5">Municipality</label>
                    <input type="text" name="municipality" value="{{ old('municipality', $parent->municipality) }}"
                           class="w-full bg-white border border-gray-200 rounded-xl px-4 py-3 text-sm text-gray-800
                                  focus:outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 transition">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-600 mb-1.5">City / Province</label>
                    <input type="text" name="city" value="{{ old('city', $parent->city) }}"
                           class="w-full bg-white border border-gray-200 rounded-xl px-4 py-3 text-sm text-gray-800
                                  focus:outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 transition">
                </div>
            </div>
        </div>

        {{-- ── Additional Info ── --}}
        <div class="bg-white border border-gray-100 rounded-2xl shadow-sm p-6">
            <h3 class="font-bold text-xs text-gray-400 uppercase tracking-wider mb-5 flex items-center gap-2">
                <i class="fas fa-sticky-note text-indigo-400"></i> Additional Info
            </h3>
            <textarea name="extra_info" rows="3"
                      class="w-full bg-white border border-gray-200 rounded-xl px-4 py-3 text-sm text-gray-800 resize-none
                             focus:outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 transition"
                      placeholder="Any other relevant information…">{{ old('extra_info', $parent->extra_info) }}</textarea>
        </div>

        <div class="flex gap-3 pt-2">
            <button type="submit"
                    class="text-white font-semibold px-6 py-3 rounded-xl transition shadow-sm"
                    style="background: linear-gradient(135deg, #4f46e5, #6366f1);">
                <i class="fas fa-save mr-1.5"></i> Save Changes
            </button>
            <a href="{{ route('parent.profile') }}"
               class="bg-white hover:bg-gray-50 border border-gray-200 text-gray-600 font-medium px-6 py-3 rounded-xl transition">
                Cancel
            </a>
        </div>

    </form>
</div>

@endsection