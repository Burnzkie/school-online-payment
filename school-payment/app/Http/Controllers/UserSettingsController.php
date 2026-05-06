<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserSettingsController extends Controller
{
    /**
     * Toggle dark mode for the authenticated user.
     * Called via AJAX from ALL portal layouts.
     */
    public function toggleDarkMode(Request $request)
    {
        $validated = $request->validate([
            'dark_mode' => ['required', 'boolean'],
        ]);

        $request->user()->update([
            'dark_mode' => $validated['dark_mode'],
        ]);

        return response()->json([
            'success'   => true,
            'dark_mode' => $validated['dark_mode'],
        ]);
    }
}