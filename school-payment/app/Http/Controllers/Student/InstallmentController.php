<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;

class InstallmentController extends Controller
{
    public function index()
    {
        return view('students.college.installments', [
            'plan' => [
                'installments' => 4,
                'next_due'     => 'March 15, 2026',
                'amount_per'   => 10500,
                'paid_installments' => 2,
            ]
        ]);
    }


// Add this method to your existing InstallmentController
// Location: app/Http/Controllers/Student/InstallmentController.php

public function choose(Request $request): RedirectResponse
{
    $request->validate([
        'plan_type' => ['required', 'in:full,2,3'],
    ]);

    $planType    = $request->input('plan_type');
    $installments = $planType === 'full' ? 1 : (int) $planType;

    // Get the student's current balance (replace with your real DB query)
    $student     = auth()->user();
    $totalBalance = 0; // TODO: pull from fees/student_fees tables, e.g.:
    // $totalBalance = Fee::where('student_id', $student->id)
    //     ->where('school_year', $currentYear)
    //     ->where('semester', $currentSemester)
    //     ->where('status', 'active')
    //     ->sum('amount');

    $amountPer   = $installments > 0 ? $totalBalance / $installments : $totalBalance;

    // Save to session (swap this for a DB record when ready)
    session([
        'installment_plan' => [
            'type'         => $planType,
            'installments' => $installments,
            'amount_per'   => $amountPer,
            'paid_count'   => 0,
            'next_due'     => now()->addMonth()->format('M d, Y'),
        ],
    ]);

    return redirect()
        ->route('student.installments')
        ->with('success', 'Your installment plan has been set up successfully.');
}
}