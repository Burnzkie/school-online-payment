<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentDashboardController extends Controller
{
    public function index(Request $request)
    {
        $student = Auth::user();

        // Redirect HS students to their own portal
        $levelGroup = strtolower($student->level_group ?? '');
        if (str_contains($levelGroup, 'junior') || str_contains($levelGroup, 'senior')) {
            return redirect()->route('hs.dashboard');
        }

        // Current school year & semester
        $currentYear     = date('n') >= 8
            ? date('Y') . '-' . (date('Y') + 1)
            : (date('Y') - 1) . '-' . date('Y');
        $currentSemester = $request->get('semester', '1');

        // ── Fees: total charges for this period ──────────────────────────
        $totalCharges = $student->fees()
            ->where('school_year', $currentYear)
            ->where('semester', $currentSemester)
            ->where('status', 'active')
            ->sum('amount');

        // ── Payments: total paid for this period ─────────────────────────
        $totalPaid = $student->payments()
            ->where('school_year', $currentYear)
            ->where('semester', $currentSemester)
            ->where('status', 'completed')
            ->sum('amount');

        // ── Derived values ────────────────────────────────────────────────
        $balance  = max(0, $totalCharges - $totalPaid);
        $progress = $totalCharges > 0
            ? min(100, round(($totalPaid / $totalCharges) * 100))
            : 0;

        // ── Next due date (earliest unpaid installment, if any) ───────────
        $nextDueDate = null;
        try {
            $nextInstallment = \DB::table('installment_schedules')
                ->where('student_id', $student->id)
                ->where('is_paid', false)
                ->where('due_date', '>=', now())
                ->orderBy('due_date')
                ->first();
            if ($nextInstallment) {
                $nextDueDate = \Carbon\Carbon::parse($nextInstallment->due_date)->format('M d, Y');
            }
        } catch (\Exception $e) {
            // installment_schedules table may not exist yet — skip silently
        }

        // ── Recent payments (last 5) ──────────────────────────────────────
        try {
            $recentPayments = $student->payments()
                ->where('status', 'completed')
                ->orderByDesc('payment_date')
                ->limit(5)
                ->get()
                ->map(fn($p) => [
                    'date'   => \Carbon\Carbon::parse($p->payment_date)->format('M d, Y'),
                    'amount' => $p->amount,
                    'method' => $p->payment_method,
                ]);
        } catch (\Exception $e) {
            $recentPayments = collect();
        }

        return view('students.college.dashboard', compact(
            'balance',
            'totalPaid',
            'totalCharges',
            'progress',
            'nextDueDate',
            'recentPayments',
            'currentYear',
            'currentSemester',
        ) + [
            'paid'  => $totalPaid,
            'total' => $totalCharges,
        ]);
    }

    // Keep any other methods that were already in this controller
    public function paymentCreate()
    {
        return redirect()->route('student.billing');
    }
}