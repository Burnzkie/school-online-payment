<?php
// app/Http/Middleware/EnsureHighSchoolStudent.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureHighSchoolStudent
{
    /**
     * Only allow Junior High or Senior High students through.
     * Everyone else is redirected to the college student dashboard.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        $level = strtolower($user->level_group ?? '');

        if (!str_contains($level, 'junior') && !str_contains($level, 'senior')) {
            return redirect()->route('student.dashboard');
        }

        return $next($request);
    }
}