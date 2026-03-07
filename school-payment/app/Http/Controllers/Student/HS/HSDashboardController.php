<?php
// app/Http/Controllers/Student/HS/HSDashboardController.php

namespace App\Http\Controllers\Student\HS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Fee;
use App\Models\Payment;
use App\Models\StudentFee;

class HSDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $isJHS = str_contains(strtolower($user->level_group ?? ''), 'junior');
        $isSHS = str_contains(strtolower($user->level_group ?? ''), 'senior');

        $schoolYear = $this->currentSchoolYear();

        // For JHS, no semester. For SHS, determine current semester.
        $semester = null;
        if ($isSHS) {
            $semester = $this->currentSemester();
        }

        // Query fees for this student
        $feesQuery = Fee::where('student_id', $user->id)
            ->where('school_year', $schoolYear)
            ->where('status', 'active');

        if ($isSHS && $semester) {
            $feesQuery->where('semester', $semester);
        }

        $fees = $feesQuery->get();
        $totalCharges = $fees->sum('amount');

        // Query payments
        $paymentsQuery = Payment::where('student_id', $user->id)
            ->where('school_year', $schoolYear)
            ->where('status', 'completed');

        if ($isSHS && $semester) {
            $paymentsQuery->where('semester', $semester);
        }

        $totalPaid = $paymentsQuery->sum('amount');
        $balance = max(0, $totalCharges - $totalPaid);
        $progress = $totalCharges > 0 ? min(100, round(($totalPaid / $totalCharges) * 100)) : 0;

        // Recent payments (last 5)
        $recentQuery = Payment::where('student_id', $user->id)
            ->where('status', 'completed')
            ->latest('payment_date')
            ->limit(5);

        if ($isSHS && $semester) {
            $recentQuery->where('semester', $semester);
        }

        $recentPayments = $recentQuery->get()->map(function($p) {
            return [
                'date'   => \Carbon\Carbon::parse($p->payment_date),
                'amount' => $p->amount,
                'method' => $p->payment_method,
            ];
        });

        // Next due date (from installment schedule) — uses model scopes
        $nextDue = \App\Models\InstallmentSchedule::forStudent($user->id)
            ->unpaid()
            ->orderBy('due_date')
            ->first();

        $nextDueDate = $nextDue ? \Carbon\Carbon::parse($nextDue->due_date)->format('M d, Y') : null;

        return view('students.hs.dashboard', compact(
            'balance', 'totalPaid', 'totalCharges', 'progress',
            'recentPayments', 'nextDueDate', 'schoolYear', 'semester',
            'isJHS', 'isSHS'
        ))->with([
            'paid'  => $totalPaid,
            'total' => $totalCharges,
        ]);
    }

    private function currentSchoolYear(): string
    {
        $month = (int) date('n');
        $year  = (int) date('Y');
        // School year typically starts June
        if ($month >= 6) {
            return $year . '-' . ($year + 1);
        }
        return ($year - 1) . '-' . $year;
    }

    private function currentSemester(): string
    {
        $month = (int) date('n');
        // 1st sem: Aug–Dec, 2nd sem: Jan–May
        return ($month >= 8 || $month <= 12 && $month >= 8) ? '1' : '2';
    }
}