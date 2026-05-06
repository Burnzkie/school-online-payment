<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

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

        // ── Current school year & semester ────────────────────────────────────
        $currentYear     = $this->currentSchoolYear();
        $currentSemester = $request->get('semester', $this->currentSemester());

        // ── Fees ──────────────────────────────────────────────────────────────
        $totalCharges = $student->fees()
            ->where('school_year', $currentYear)
            ->where('semester', $currentSemester)
            ->where('status', 'active')
            ->sum('amount');

        // ── Payments ──────────────────────────────────────────────────────────
        $totalPaid = $student->payments()
            ->where('school_year', $currentYear)
            ->where('semester', $currentSemester)
            ->where('status', 'completed')
            ->sum('amount');

        // ── Previous semester carryover balance ───────────────────────────────
        [$prevYear, $prevSemester] = $this->previousPeriod($currentYear, $currentSemester);

        $prevCharges = $student->fees()
            ->where('school_year', $prevYear)
            ->where('semester', $prevSemester)
            ->where('status', 'active')
            ->sum('amount');

        $prevPaid = $student->payments()
            ->where('school_year', $prevYear)
            ->where('semester', $prevSemester)
            ->where('status', 'completed')
            ->sum('amount');

        $previousBalance = max(0, $prevCharges - $prevPaid);

        // ── Derived values ────────────────────────────────────────────────────
        $balance  = max(0, $totalCharges - $totalPaid) + $previousBalance;
        $progress = $totalCharges > 0
            ? min(100, round(($totalPaid / $totalCharges) * 100))
            : 0;

        // ── Recent payments (last 5) ──────────────────────────────────────────
        try {
            $recentPayments = $student->payments()
                ->where('status', 'completed')
                ->orderByDesc('payment_date')
                ->limit(5)
                ->get()
                ->map(fn($p) => [
                    'date'   => Carbon::parse($p->payment_date)->format('M d, Y'),
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
            'recentPayments',
            'currentYear',
            'currentSemester',
        ) + [
            'paid'  => $totalPaid,
            'total' => $totalCharges,
        ]);
    }

    public function paymentCreate()
    {
        return redirect()->route('student.billing');
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

        // summer
        return [$year, '2'];
    }
}