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
     *
     * Detects HS students by level_group containing any of:
     *   "junior", "senior", "jhs", "shs", "high school", "highschool"
     * OR year_level matching Grade 7–12 as a safe fallback.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        if (!self::isHighSchoolStudent($user)) {
            return redirect()->route('student.dashboard');
        }

        return $next($request);
    }

    /**
     * Shared helper — reused by web.php dashboard redirect and EnsureCollegeStudent.
     */
    public static function isHighSchoolStudent($user): bool
    {
        $level = strtolower($user->level_group ?? '');

        // Match common level_group values used for JHS / SHS
        $hsKeywords = ['junior', 'senior', 'jhs', 'shs', 'high school', 'highschool'];

        foreach ($hsKeywords as $keyword) {
            if (str_contains($level, $keyword)) {
                return true;
            }
        }

        // Fallback: year_level looks like "Grade 7" – "Grade 12"
        $yearLevel = strtolower($user->year_level ?? '');
        if (preg_match('/grade\s*(7|8|9|10|11|12)/i', $yearLevel)) {
            return true;
        }

        return false;
    }
}