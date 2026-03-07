<?php
// app/Http/Controllers/Student/HS/HSBillingController.php

namespace App\Http\Controllers\Student\HS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Fee;
use App\Models\Payment;
use App\Models\InstallmentPlan;

class HSBillingController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $isJHS = str_contains(strtolower($user->level_group ?? ''), 'junior');
        $isSHS = str_contains(strtolower($user->level_group ?? ''), 'senior');

        // Available school years
        $availableYears = Fee::where('student_id', $user->id)
            ->distinct('school_year')
            ->orderByDesc('school_year')
            ->pluck('school_year')
            ->toArray();

        if (empty($availableYears)) {
            $availableYears = [$this->currentSchoolYear()];
        }

        $selectedYear = $request->input('school_year', $availableYears[0] ?? $this->currentSchoolYear());
        $selectedSemester = $isSHS ? $request->input('semester', $this->currentSemester()) : null;

        // Fees
        $feesQuery = Fee::where('student_id', $user->id)
            ->where('school_year', $selectedYear)
            ->where('status', 'active');

        if ($isSHS && $selectedSemester) {
            $feesQuery->where('semester', $selectedSemester);
        }

        $fees = $feesQuery->get();
        $totalCharges = $fees->sum('amount');

        // Payments
        $paymentsQuery = Payment::where('student_id', $user->id)
            ->where('school_year', $selectedYear)
            ->where('status', 'completed');

        if ($isSHS && $selectedSemester) {
            $paymentsQuery->where('semester', $selectedSemester);
        }

        $payments = $paymentsQuery->get();
        $totalPayments = $payments->sum('amount');
        $balance = max(0, $totalCharges - $totalPayments);
        $paid = $totalPayments;

        // Build ledger items
        $ledgerItems = [];
        foreach ($fees as $fee) {
            $ledgerItems[] = ['description' => $fee->fee_name, 'charge' => $fee->amount, 'payment' => 0];
        }
        foreach ($payments as $payment) {
            $ledgerItems[] = ['description' => 'Payment — ' . $payment->payment_method . ($payment->or_number ? ' (OR# ' . $payment->or_number . ')' : ''), 'charge' => 0, 'payment' => $payment->amount];
        }

        // Active plan
        $planQuery = InstallmentPlan::where('student_id', $user->id)
            ->where('school_year', $selectedYear)
            ->where('status', 'active');
        if ($isSHS && $selectedSemester) {
            $planQuery->where('semester', $selectedSemester);
        }
        $activePlan = $planQuery->first();

        return view('students.hs.billing', compact(
            'balance', 'paid', 'totalCharges', 'totalPayments',
            'ledgerItems', 'availableYears', 'selectedYear', 'selectedSemester',
            'activePlan', 'isJHS', 'isSHS'
        ));
    }

    private function currentSchoolYear(): string
    {
        $month = (int) date('n');
        $year  = (int) date('Y');
        return ($month >= 6) ? $year . '-' . ($year + 1) : ($year - 1) . '-' . $year;
    }

    private function currentSemester(): string
    {
        $month = (int) date('n');
        return ($month >= 8) ? '1' : '2';
    }
}