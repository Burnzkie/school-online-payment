<?php

// app/Http/Controllers/Student/HS/HSBillingController.php

namespace App\Http\Controllers\Student\HS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Fee;
use App\Models\Payment;

class HSBillingController extends Controller
{
    public function index(Request $request)
    {
        $user  = Auth::user();
        $isJHS = str_contains(strtolower($user->level_group ?? ''), 'junior');
        $isSHS = str_contains(strtolower($user->level_group ?? ''), 'senior');

        // ── Available years ───────────────────────────────────────────────────
        $availableYears = Fee::where('student_id', $user->id)
            ->distinct('school_year')
            ->orderByDesc('school_year')
            ->pluck('school_year')
            ->toArray();

        if (empty($availableYears)) {
            $availableYears = [$this->currentSchoolYear()];
        }

        // ── Selected period ───────────────────────────────────────────────────
        $selectedYear     = $request->input('school_year', $availableYears[0] ?? $this->currentSchoolYear());
        $selectedSemester = $isSHS ? $request->input('semester', $this->currentSemester()) : null;

        // ── Fees ──────────────────────────────────────────────────────────────
        $feesQuery = Fee::where('student_id', $user->id)
            ->where('school_year', $selectedYear)
            ->where('status', 'active');

        if ($isSHS && $selectedSemester) {
            $feesQuery->where('semester', $selectedSemester);
        }

        $fees         = $feesQuery->get();
        $totalCharges = $fees->sum('amount');

        // ── Payments ──────────────────────────────────────────────────────────
        $paymentsQuery = Payment::where('student_id', $user->id)
            ->where('school_year', $selectedYear)
            ->where('status', 'completed');

        if ($isSHS && $selectedSemester) {
            $paymentsQuery->where('semester', $selectedSemester);
        }

        $payments      = $paymentsQuery->get();
        $totalPayments = $payments->sum('amount');
        $balance       = max(0, $totalCharges - $totalPayments);
        $paid          = $totalPayments;

        // ── Previous balance carryover ────────────────────────────────────────
        $previousBalance = 0;

        if ($isSHS && $selectedSemester) {
            [$prevYear, $prevSemester] = $this->previousPeriod($selectedYear, $selectedSemester);

            $prevCharges = Fee::where('student_id', $user->id)
                ->where('school_year', $prevYear)
                ->where('semester', $prevSemester)
                ->where('status', 'active')
                ->sum('amount');

            $prevPaid = Payment::where('student_id', $user->id)
                ->where('school_year', $prevYear)
                ->where('semester', $prevSemester)
                ->where('status', 'completed')
                ->sum('amount');

            $previousBalance = max(0, $prevCharges - $prevPaid);

        } elseif ($isJHS) {
            $prevYear = $this->previousSchoolYear($selectedYear);

            $prevCharges = Fee::where('student_id', $user->id)
                ->where('school_year', $prevYear)
                ->where('status', 'active')
                ->sum('amount');

            $prevPaid = Payment::where('student_id', $user->id)
                ->where('school_year', $prevYear)
                ->where('status', 'completed')
                ->sum('amount');

            $previousBalance = max(0, $prevCharges - $prevPaid);
        }

        // ── Build ledger ──────────────────────────────────────────────────────
        $ledgerItems = [];

        foreach ($fees as $fee) {
            $ledgerItems[] = [
                'description' => $fee->fee_name,
                'charge'      => $fee->amount,
                'payment'     => 0,
            ];
        }

        foreach ($payments as $payment) {
            $ledgerItems[] = [
                'description' => 'Payment — ' . $payment->payment_method
                    . ($payment->or_number ? ' (OR# ' . $payment->or_number . ')' : ''),
                'charge'      => 0,
                'payment'     => $payment->amount,
            ];
        }

        return view('students.hs.billing', compact(
            'balance', 'paid', 'totalCharges', 'totalPayments',
            'ledgerItems', 'availableYears', 'selectedYear', 'selectedSemester',
            'isJHS', 'isSHS', 'previousBalance'
        ));
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    private function currentSchoolYear(): string
    {
        $month = (int) date('n');
        $year  = (int) date('Y');
        return ($month >= 6)
            ? $year . '-' . ($year + 1)
            : ($year - 1) . '-' . $year;
    }

    private function currentSemester(): string
    {
        $month = (int) date('n');
        return ($month >= 8) ? '1' : '2';
    }

    private function previousPeriod(string $year, string $semester): array
    {
        if ($semester === '1') {
            [$start, $end] = explode('-', $year);
            return [($start - 1) . '-' . ($end - 1), '2'];
        }

        if ($semester === '2') {
            return [$year, '1'];
        }

        return [$year, '2'];
    }

    private function previousSchoolYear(string $year): string
    {
        [$start, $end] = explode('-', $year);
        return ($start - 1) . '-' . ($end - 1);
    }
}