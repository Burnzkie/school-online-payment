<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class StudentProfileController extends Controller
{
    /**
     * Display the student profile page
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $user = Auth::user();
        
        return view('students.college.profile', [
            'user' => $user
        ]);
    }

    /**
     * Show the form for editing the profile (optional)
     *
     * @return \Illuminate\View\View
     */
    public function edit()
    {
        $user = Auth::user();
        
        return view('students.profile-edit', [
            'user' => $user
        ]);
    }

    /**
     * Update the profile information (optional)
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        // Add your profile update logic here
        // For now, just redirect back
        return redirect()->route('student.profile')
            ->with('success', 'Profile updated successfully!');
    }

    /**
     * Update student profile picture
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateProfilePhoto(Request $request)
    {
        try {
            Log::info('Profile photo upload started', [
                'user_id' => Auth::id(),
                'has_file' => $request->hasFile('profile_picture')
            ]);

            // Validate the uploaded file
            $validated = $request->validate([
                'profile_picture' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048', // 2MB max
            ]);

            $user = Auth::user();

            // Check if user exists
            if (!$user) {
                Log::error('User not authenticated');
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated.'
                ], 401);
            }

            // Delete old profile picture if exists
            if ($user->profile_picture) {
                $oldPath = $user->profile_picture;
                if (Storage::disk('public')->exists($oldPath)) {
                    Storage::disk('public')->delete($oldPath);
                    Log::info('Deleted old profile picture', ['path' => $oldPath]);
                }
            }

            // Store new profile picture
            $file = $request->file('profile_picture');
            $filename = 'profile_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('profile-pictures', $filename, 'public');

            Log::info('New profile picture stored', [
                'path' => $path,
                'filename' => $filename
            ]);

            // Update user profile picture path in database
            $user->profile_picture = $path;
            $user->save();

            Log::info('Profile picture updated successfully', [
                'user_id' => $user->id,
                'path' => $path
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Profile photo updated successfully!',
                'profile_picture_url' => asset('storage/' . $path)
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('Validation failed for profile photo', [
                'errors' => $e->errors()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Invalid file. Please upload a valid image (JPG, PNG, GIF, WEBP) under 2MB.',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            Log::error('Profile photo upload failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while uploading the photo. Please try again.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
}