<?php
// app/Http/Controllers/Student/HS/HSProfileController.php

namespace App\Http\Controllers\Student\HS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class HSProfileController extends Controller
{
    public function index()
    {
        return view('students.hs.profile');
    }

    public function updateProfilePhoto(Request $request)
    {
        $request->validate([
            'profile_picture' => ['required', 'image', 'max:2048'],
        ]);

        $user = Auth::user();

        // Delete old picture if exists
        if ($user->profile_picture) {
            Storage::disk('public')->delete($user->profile_picture);
        }

        $path = $request->file('profile_picture')->store('profile-pictures', 'public');
        $user->update(['profile_picture' => $path]);

        return back()->with('success', 'Profile photo updated!');
    }

    public function edit()
    {
        return view('students.hs.profile');
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'name'         => ['required', 'string', 'max:255'],
            'middle_name'  => ['nullable', 'string', 'max:100'],
            'last_name'    => ['nullable', 'string', 'max:100'],
            'suffix'       => ['nullable', 'string', 'max:20'],
            'phone'        => ['nullable', 'string', 'max:30'],
            'birth_date'   => ['nullable', 'date'],
            'gender'       => ['nullable', 'string', 'max:20'],
            'street'       => ['nullable', 'string', 'max:255'],
            'barangay'     => ['nullable', 'string', 'max:255'],
            'municipality' => ['nullable', 'string', 'max:255'],
            'city'         => ['nullable', 'string', 'max:255'],
        ]);

        Auth::user()->update($validated);

        return redirect()->route('hs.profile')->with('success', 'Profile updated successfully!');
    }
}