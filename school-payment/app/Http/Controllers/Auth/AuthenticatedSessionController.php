<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     * Note: We're using register.blade.php which has both login and register tabs.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        // Validate credentials via LoginRequest (throws if wrong email/password)
        $request->authenticate();

        $user = Auth::user();

        // ── Dropped student check ─────────────────────────────────────────────
        // If a student account has been dropped by admin, log them out
        // immediately and show a clear error message on the login form.
        if ($user->role === 'student' && ($user->status ?? 'active') === 'dropped') {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            throw ValidationException::withMessages([
                'email' => 'Your account has been deactivated. You are no longer enrolled '
                         . 'at Philippine Advent College. Please contact the '
                         . 'Registrar\'s Office for further assistance.',
            ]);
        }

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}