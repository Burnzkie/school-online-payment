<?php

namespace App\Http\Controllers\Student\HS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class HSDashboardController extends Controller
{
    public function index(Request $request)
    {
        $student = Auth::user();

        $isJHS = str_contains(strtolower($student->level_group ?? ''), 'junior');
        $isSHS = str_contains(strtolower($student->level_group ?? ''), 'senior');

        // ── Current school year & semester ────────────────────────────────────
        $currentYear     = $this->currentSchoolYear();
        $currentSemester = $isSHS
            ? $request->get('semester', $this->currentSemester())
            : null; // JHS is annual — no semester filter

        // ── Fees ──────────────────────────────────────────────────────────────
        $feesQuery = $student->fees()
            ->where('school_year', $currentYear)
            ->where('status', 'active');

        if ($isSHS && $currentSemester) {
            $feesQuery->where('semester', $currentSemester);
        }

        $totalCharges = $feesQuery->sum('amount');

        // ── Payments ──────────────────────────────────────────────────────────
        $paymentsQuery = $student->payments()
            ->where('school_year', $currentYear)
            ->where('status', 'completed');

        if ($isSHS && $currentSemester) {
            $paymentsQuery->where('semester', $currentSemester);
        }

        $totalPaid = $paymentsQuery->sum('amount');

        // ── Previous balance carryover ────────────────────────────────────────
        $previousBalance = 0;

        if ($isSHS && $currentSemester) {
            // SHS: compare previous semester
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

        } elseif ($isJHS) {
            // JHS: compare previous school year (annual billing)
            $prevYear = $this->previousSchoolYear($currentYear);

            $prevCharges = $student->fees()
                ->where('school_year', $prevYear)
                ->where('status', 'active')
                ->sum('amount');

            $prevPaid = $student->payments()
                ->where('school_year', $prevYear)
                ->where('status', 'completed')
                ->sum('amount');

            $previousBalance = max(0, $prevCharges - $prevPaid);
        }

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

        return view('students.hs.dashboard', [
            'balance'         => $balance,
            'paid'            => $totalPaid,
            'total'           => $totalCharges,
            'totalPaid'       => $totalPaid,
            'totalCharges'    => $totalCharges,
            'previousBalance' => $previousBalance,
            'progress'        => $progress,
            'recentPayments'  => $recentPayments,
            'currentYear'     => $currentYear,
            'currentSemester' => $currentSemester,
            'semester'        => $currentSemester, // alias used by the blade
            'isJHS'           => $isJHS,
            'isSHS'           => $isSHS,
        ]);
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    private function currentSchoolYear(): string
    {
        $month = (int) date('n');
        $year  = (int) date('Y');
        return ($month >= 8)
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

        return [$year, '1'];
    }

    private function previousSchoolYear(string $year): string
    {
        [$start, $end] = explode('-', $year);
        return ($start - 1) . '-' . ($end - 1);
    }
}