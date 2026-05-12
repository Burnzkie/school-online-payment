<?php
// app/Http/Middleware/EnsureCollegeStudent.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureCollegeStudent
{
    /**
     * Block HS students from accessing college routes.
     * If an HS student somehow reaches a college route, redirect them
     * to their own portal instead of letting college data leak through.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Reuse the shared HS detection logic
        if (EnsureHighSchoolStudent::isHighSchoolStudent($user)) {
            return redirect()->route('hs.dashboard');
        }

        return $next($request);
    }
}