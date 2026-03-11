<?php
// app/Http/Middleware/EnsureStudentIsActive.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureStudentIsActive
{
    /**
     * Handle an incoming request.
     *
     * Checks on every request if a logged-in student has been dropped.
     * If so, immediately logs them out and redirects to the register page
     * with an explanatory message — even if they were already logged in
     * when the admin dropped them.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if (
            $user &&
            $user->role === 'student' &&
            ($user->status ?? 'active') === 'dropped'
        ) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('register')
                ->with('status',
                    '⚠️ Your account has been deactivated. '
                    . 'You are no longer enrolled at Philippine Advent College. '
                    . 'Please contact the Registrar\'s Office for assistance.'
                );
        }

        return $next($request);
    }
}