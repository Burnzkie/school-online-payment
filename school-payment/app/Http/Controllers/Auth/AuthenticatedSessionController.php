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
     * Note: register.blade.php contains both login and register tabs.
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
        // Validate credentials (throws ValidationException if wrong)
        $request->authenticate();

        $user = Auth::user();

        // ── Dropped student check ─────────────────────────────────────────────
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

        // ── Regenerate session ONCE before any redirect ───────────────────────
        $request->session()->regenerate();

        // ── Route directly to each role's portal ─────────────────────────────
        // This avoids a double-redirect through /dashboard which can cause
        // 419 Page Expired errors due to session cookie timing issues.

        return match(true) {

            $user->role === 'admin' =>
                redirect()->route('admin.dashboard'),

            $user->role === 'cashier' =>
                redirect()->route('cashier.dashboard'),

            $user->role === 'treasurer' =>
                redirect()->route('treasurer.dashboard'),

            $user->role === 'parent' =>
                redirect()->route('parent.dashboard'),

            $user->role === 'student' && (
                str_contains(strtolower($user->level_group ?? ''), 'junior') ||
                str_contains(strtolower($user->level_group ?? ''), 'senior')
            ) => redirect()->route('hs.dashboard'),

            $user->role === 'student' =>
                redirect()->route('student.dashboard'),

            // Fallback — unknown role
            default => redirect('/')->with(
                'status',
                'Your account role does not have a portal yet. Please contact the administrator.'
            ),
        };
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