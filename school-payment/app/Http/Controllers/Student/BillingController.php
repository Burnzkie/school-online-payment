<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Fee;
use App\Models\Payment;


class BillingController extends Controller
{
    public function index(Request $request)
    {
        $student = Auth::user();

        // ── Available years (from DB, most recent first) ──────────────────────
        $availableYears = Fee::where('student_id', $student->id)
            ->distinct('school_year')
            ->orderByDesc('school_year')
            ->pluck('school_year')
            ->toArray();

        if (empty($availableYears)) {
            $availableYears = [$this->currentSchoolYear()];
        }

        // ── Selected period (defaults to current year & semester) ─────────────
        $selectedYear     = $request->input('school_year', $availableYears[0] ?? $this->currentSchoolYear());
        $selectedSemester = $request->input('semester', $this->currentSemester());

        // ── Current semester fees & payments ──────────────────────────────────
        $fees = Fee::where('student_id', $student->id)
            ->where('school_year', $selectedYear)
            ->where('semester', $selectedSemester)
            ->get();

        $payments = Payment::where('student_id', $student->id)
            ->where('school_year', $selectedYear)
            ->where('semester', $selectedSemester)
            ->where('status', 'completed')
            ->orderBy('payment_date')
            ->get();

        $totalCharges = $fees->sum('amount');
        $totalPaid    = $payments->sum('amount');
        $balance      = max(0, $totalCharges - $totalPaid);

        // ── Previous semester carryover balance ───────────────────────────────
        [$prevYear, $prevSemester] = $this->previousPeriod($selectedYear, $selectedSemester);

        $prevCharges = Fee::where('student_id', $student->id)
            ->where('school_year', $prevYear)
            ->where('semester', $prevSemester)
            ->sum('amount');

        $prevPaid = Payment::where('student_id', $student->id)
            ->where('school_year', $prevYear)
            ->where('semester', $prevSemester)
            ->where('status', 'completed')
            ->sum('amount');

        $previousBalance = max(0, $prevCharges - $prevPaid);

        // ── Build ledger ──────────────────────────────────────────────────────
        $ledgerItems = [];

        foreach ($fees as $fee) {
            $ledgerItems[] = [
                'description' => $fee->fee_name . ($fee->description ? ' — ' . $fee->description : ''),
                'charge'      => $fee->amount,
                'payment'     => 0,
            ];
        }

        foreach ($payments as $payment) {
            $ref = $payment->or_number
                ? 'OR# ' . $payment->or_number
                : ($payment->reference_number ? 'Ref# ' . $payment->reference_number : null);

            $ledgerItems[] = [
                'description' => 'Payment — ' . strtoupper($payment->payment_method) . ($ref ? ' (' . $ref . ')' : ''),
                'charge'      => 0,
                'payment'     => $payment->amount,
            ];
        }


        return view('students.college.billing', [
            'selectedYear'    => $selectedYear,
            'selectedSemester'=> $selectedSemester,
            'availableYears'  => $availableYears,
            'balance'         => $balance,
            'paid'            => $totalPaid,
            'totalCharges'    => $totalCharges,
            'ledgerItems'     => $ledgerItems,
            'previousBalance' => $previousBalance,
            
        ]);
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    private function currentSchoolYear(): string
    {
        $month = (int) date('n');
        $year  = (int) date('Y');
        // June onwards = new school year
        return ($month >= 6)
            ? $year . '-' . ($year + 1)
            : ($year - 1) . '-' . $year;
    }

    private function currentSemester(): string
    {
        $month = (int) date('n');
        // Aug–Dec = 1st sem | Jan–Jul = 2nd sem
        return ($month >= 8) ? '1' : '2';
    }

    /**
     * Returns [prevYear, prevSemester] for the carryover calculation.
     *
     * 1st Sem  → 2nd Sem of previous school year
     * 2nd Sem  → 1st Sem of same school year
     * Summer   → 2nd Sem of same school year
     */
    private function previousPeriod(string $year, string $semester): array
    {
        if ($semester === '1') {
            [$start, $end] = explode('-', $year);
            return [($start - 1) . '-' . ($end - 1), '2'];
        }

        if ($semester === '2') {
            return [$year, '1'];
        }

        // summer
        return [$year, '2'];
    }
}