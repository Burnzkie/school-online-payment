<?php
// app/Http/Controllers/Student/HS/HSStatementController.php

namespace App\Http\Controllers\Student\HS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Fee;
use App\Models\Payment;

class HSStatementController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $isJHS = str_contains(strtolower($user->level_group ?? ''), 'junior');
        $isSHS = str_contains(strtolower($user->level_group ?? ''), 'senior');

        // Include years from both fees and payments so students
        // always see their school years even with no payments yet
        $yearsFromPayments = Payment::where('student_id', $user->id)
            ->distinct()->pluck('school_year');

        $yearsFromFees = Fee::where('student_id', $user->id)
            ->distinct()->pluck('school_year');

        $availableYears = $yearsFromPayments->merge($yearsFromFees)
            ->unique()->sortDesc()->values()->toArray();

        if (empty($availableYears)) {
            $availableYears = [$this->currentSchoolYear()];
        }

        $selectedYear     = $request->input('school_year', $availableYears[0]);
        $selectedSemester = $isSHS ? $request->input('semester', 'all') : null;

        $query = Payment::where('student_id', $user->id)
            ->where('school_year', $selectedYear)
            ->whereIn('status', ['completed', 'pending'])
            ->orderByDesc('payment_date');

        if ($isSHS && $selectedSemester && $selectedSemester !== 'all') {
            $query->where('semester', $selectedSemester);
        }

        $payments = $query->get();

        return view('students.hs.statements', compact(
            'payments', 'availableYears', 'selectedYear', 'selectedSemester',
            'isJHS', 'isSHS'
        ));
    }

    private function currentSchoolYear(): string
    {
        $month = (int) date('n');
        $year  = (int) date('Y');
        return ($month >= 8) ? $year . '-' . ($year + 1) : ($year - 1) . '-' . $year;
    }
}