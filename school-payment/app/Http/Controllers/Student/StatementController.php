<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Fee;
use App\Models\Payment;

class StatementController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        // ── Available school years ─────────────────────────────────────────────
        $yearsFromFees     = Fee::where('student_id', $user->id)->distinct()->pluck('school_year');
        $yearsFromPayments = Payment::where('student_id', $user->id)->distinct()->pluck('school_year');

        $availableYears = $yearsFromFees->merge($yearsFromPayments)
            ->unique()->sortDesc()->values()->toArray();

        if (empty($availableYears)) {
            $availableYears = [$this->currentSchoolYear()];
        }

        $selectedYear = $request->input('school_year', $availableYears[0]);

        // ── Build statements from fees + payment records ───────────────────────
        // Each fee becomes one row; we check total payments to determine paid status.
        $fees = Fee::where('student_id', $user->id)
            ->where('school_year', $selectedYear)
            ->where('status', 'active')
            ->orderBy('created_at')
            ->get();

        $payments = Payment::where('student_id', $user->id)
            ->where('school_year', $selectedYear)
            ->whereIn('status', ['completed', 'pending'])
            ->orderBy('payment_date')
            ->get();

        // Map fees → statement rows (charges)
        $feeRows = $fees->map(fn($fee) => [
            'date'        => $fee->created_at?->format('Y-m-d') ?? $selectedYear . '-08-01',
            'description' => $fee->fee_name,
            'amount'      => $fee->amount,
            'paid'        => false, // resolved below
            'type'        => 'fee',
            'semester'    => $fee->semester ?? null,
            '_fee_id'     => $fee->id,
        ]);

        // Map payments → statement rows (payments received)
        $paymentRows = $payments->map(fn($p) => [
            'date'        => $p->payment_date ?? $p->created_at?->format('Y-m-d'),
            'description' => 'Payment — ' . ($p->payment_method ?? 'Cash')
                             . ($p->or_number ? ' (OR# ' . $p->or_number . ')' : ''),
            'amount'      => $p->amount,
            'paid'        => $p->status === 'completed',
            'reference'   => $p->or_number ?? null,
            'type'        => 'payment',
            'semester'    => $p->semester ?? null,
        ]);

        // Merge fees and payments, sorted by date
        $statements = $feeRows->merge($paymentRows)
            ->sortBy('date')
            ->values();

        // Mark fee rows as paid if total completed payments >= total fees
        $totalCompleted = $payments->where('status', 'completed')->sum('amount');
        $runningCovered = 0;

        $statements = $statements->map(function ($item) use (&$runningCovered, $totalCompleted) {
            if ($item['type'] === 'fee') {
                $runningCovered += $item['amount'];
                $item['paid'] = $totalCompleted >= $runningCovered;
            }
            return $item;
        });

        return view('students.college.statements', compact(
            'statements',
            'availableYears',
            'selectedYear',
        ));
    }

    private function currentSchoolYear(): string
    {
        $month = (int) date('n');
        $year  = (int) date('Y');
        return ($month >= 8)
            ? $year . '-' . ($year + 1)
            : ($year - 1) . '-' . $year;
    }
}