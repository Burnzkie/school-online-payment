<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Fee;
use App\Models\Payment;
use App\Models\OnlinePaymentSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class BillingController extends Controller
{

    // ── GET /student/billing ─────────────────────────────────────────
    public function index(Request $request)
    {
        $student = Auth::user();

        // ── Available school years (from fees assigned to this student) ──
        $availableYears = Fee::where('student_id', $student->id)
            ->distinct()
            ->pluck('school_year')
            ->sortDesc()
            ->values()
            ->toArray();

        // Fall back to current school year if no fees yet
        $month       = now()->month;
        $year        = now()->year;
        $currentYear = $month >= 8 ? "{$year}-" . ($year + 1) : ($year - 1) . "-{$year}";

        if (empty($availableYears)) {
            $availableYears = [$currentYear];
        }

        $selectedYear     = $request->get('school_year', $availableYears[0]);
        $selectedSemester = $request->get('semester', $month >= 8 ? '1' : '2');

        // ── Fees for selected period ──────────────────────────────────
        $fees = Fee::where('student_id', $student->id)
            ->where('school_year', $selectedYear)
            ->where('semester', $selectedSemester)
            ->where('status', 'active')
            ->orderBy('fee_name')
            ->get();

        $totalCharges = $fees->sum('amount');

        // ── Completed payments for selected period ────────────────────
        // This includes BOTH cash payments recorded by cashier
        // AND approved online payments (GCash/Maya) — all status=completed
        $payments = Payment::where('student_id', $student->id)
            ->where('school_year', $selectedYear)
            ->where('semester', $selectedSemester)
            ->where('status', 'completed')
            ->orderBy('payment_date')
            ->get();

        $paid    = $payments->sum('amount');
        $balance = max(0, $totalCharges - $paid);

        // ── Progress percentage ───────────────────────────────────────
        $progress = $totalCharges > 0
            ? min(100, round(($paid / $totalCharges) * 100))
            : ($paid > 0 ? 100 : 0);

        // ── Previous semester carryover ───────────────────────────────
        [$prevYear, $prevSemester] = $this->previousPeriod($selectedYear, $selectedSemester);

        $prevFees     = Fee::where('student_id', $student->id)
            ->where('school_year', $prevYear)
            ->where('semester', $prevSemester)
            ->where('status', 'active')
            ->sum('amount');

        $prevPayments = Payment::where('student_id', $student->id)
            ->where('school_year', $prevYear)
            ->where('semester', $prevSemester)
            ->where('status', 'completed')
            ->sum('amount');

        $previousBalance = max(0, $prevFees - $prevPayments);

        // ── Build ledger items (fees + payments interleaved) ──────────
        $ledgerItems = [];

        // Add each fee as a charge row
        foreach ($fees as $fee) {
            $ledgerItems[] = [
                'description' => $fee->fee_name,
                'charge'      => $fee->amount,
                'payment'     => 0,
                'type'        => 'fee',
                'date'        => null,
            ];
        }

        // Add each payment as a payment row
        foreach ($payments as $payment) {
            $label = $payment->payment_method;
            if ($payment->or_number) {
                $label .= ' · OR# ' . $payment->or_number;
            } elseif ($payment->reference_number) {
                $label .= ' · Ref: ' . $payment->reference_number;
            }
            $ledgerItems[] = [
                'description' => 'Payment — ' . $label,
                'charge'      => 0,
                'payment'     => $payment->amount,
                'type'        => 'payment',
                'date'        => $payment->payment_date,
            ];
        }

        // ── Pending online submissions (so student sees their pending ones) ──
        $pendingSubmissions = OnlinePaymentSubmission::where('student_id', $student->id)
            ->where('school_year', $selectedYear)
            ->where('semester', $selectedSemester)
            ->where('status', 'pending')
            ->orderByDesc('created_at')
            ->get();

        return view('students.college.billing', compact(
            'fees',
            'payments',
            'totalCharges',
            'paid',
            'balance',
            'progress',
            'previousBalance',
            'selectedYear',
            'selectedSemester',
            'availableYears',
            'ledgerItems',
            'pendingSubmissions',
        ) + [
            'total'  => $totalCharges,
        ]);
    }

    // ── Helpers ───────────────────────────────────────────────────────

    private function previousPeriod(string $year, string $semester): array
    {
        if ($semester === '1') {
            [$start, $end] = explode('-', $year);
            return [($start - 1) . '-' . ($end - 1), '2'];
        }
        if ($semester === '2') {
            return [$year, '1'];
        }
        // summer → 2nd semester of same year
        return [$year, '2'];
    }


    // ── POST /student/billing/pay-online ─────────────────────────────
    public function payOnline(Request $request)
    {
        $student = Auth::user();

        $validated = $request->validate([
            'school_year'      => ['required', 'string', 'regex:/^\d{4}-\d{4}$/'],
            'semester'         => ['required', 'in:1,2,summer'],
            'payment_method'   => ['required', 'in:GCash,PayMaya,Bank Transfer'],
            'amount'           => ['required', 'numeric', 'min:1'],
            'reference_number' => ['required', 'string', 'max:100'],
            'proof_of_payment' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
            'notes'            => ['nullable', 'string', 'max:500'],
        ]);

        // Store proof of payment file
        $path = $request->file('proof_of_payment')
            ->store("online-payments/{$student->id}", 'public');

        OnlinePaymentSubmission::create([
            'student_id'       => $student->id,
            'school_year'      => $validated['school_year'],
            'semester'         => $validated['semester'],
            'payment_method'   => $validated['payment_method'],
            'amount'           => $validated['amount'],
            'reference_number' => $validated['reference_number'],
            'proof_of_payment' => $path,
            'notes'            => $validated['notes'] ?? null,
            'status'           => 'pending',
        ]);

        return redirect()
            ->route('student.billing', [
                'school_year' => $validated['school_year'],
                'semester'    => $validated['semester'],
            ])
            ->with('payment_submitted', true);
    }
}