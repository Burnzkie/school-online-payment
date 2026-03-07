<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\InstallmentPlan;
use App\Models\InstallmentSchedule;
use App\Models\StudentFee;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class InstallmentPlanController extends Controller
{
    // ─────────────────────────────────────────────────────────────────────────
    // Semester start dates (M-D format). Adjust to your school calendar.
    // ─────────────────────────────────────────────────────────────────────────
    private const SEMESTER_STARTS = [
        '1'      => '06-01',   // June  1 — 1st Semester
        '2'      => '11-01',   // Nov   1 — 2nd Semester
        'summer' => '04-01',   // April 1 — Summer
    ];

    // Month offsets from semester start for each plan type.
    private const INTERVALS = [
        'full' => [0],
        '2'    => [0, 1],
        '3'    => [0, 1, 2],
        '4'    => [0, 1, 2, 3],
    ];

    // ─────────────────────────────────────────────────────────────────────────
    // SHOW — installments page
    // ─────────────────────────────────────────────────────────────────────────
    public function index(): View
    {
        $student    = Auth::user();
        $schoolYear = $this->currentSchoolYear();
        $semester   = $this->currentSemester();

        $totalBalance = $this->computeBalance($student->id, $schoolYear, $semester);

        $activePlan = InstallmentPlan::with('schedules')
            ->where('student_id', $student->id)
            ->where('school_year', $schoolYear)
            ->where('semester', $semester)
            ->where('status', 'active')
            ->first();

        if ($activePlan) {
            $formattedPlan     = $this->formatActivePlan($activePlan);
            $formattedSchedule = $this->formatSchedule($activePlan->schedules);

            return view('students.installments', [
                'activePlan'   => $formattedPlan,
                'schedule'     => $formattedSchedule,
                'totalBalance' => $totalBalance,
            ]);
        }

        return view('students.college.installments', [
            'totalBalance' => $totalBalance,
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // STORE — save the chosen plan
    // ─────────────────────────────────────────────────────────────────────────
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'plan_type' => ['required', 'in:full,2,3,4'],
            'confirmed' => ['required', 'accepted'],
        ]);

        $student    = Auth::user();
        $schoolYear = $this->currentSchoolYear();
        $semester   = $this->currentSemester();

        // Prevent duplicate active plans
        $exists = InstallmentPlan::where('student_id', $student->id)
            ->where('school_year', $schoolYear)
            ->where('semester', $semester)
            ->where('status', 'active')
            ->exists();

        if ($exists) {
            return redirect()->route('student.installments')
                ->with('error', 'You already have an active installment plan for this semester.');
        }

        $planType     = $request->plan_type;
        $totalAmount  = $this->computeBalance($student->id, $schoolYear, $semester);

        if ($totalAmount <= 0) {
            return redirect()->route('student.installments')
                ->with('error', 'You have no outstanding balance for this semester.');
        }

        $totalInstallments    = $planType === 'full' ? 1 : (int) $planType;
        $amountPerInstallment = round($totalAmount / $totalInstallments, 2);
        $remainder            = round($totalAmount - ($amountPerInstallment * $totalInstallments), 2);
        $intervals            = self::INTERVALS[$planType];
        $semStart             = $this->semesterStartDate($schoolYear, $semester);

        DB::transaction(function () use (
            $student, $schoolYear, $semester, $planType,
            $totalInstallments, $totalAmount, $amountPerInstallment, $remainder,
            $intervals, $semStart
        ) {
            $plan = InstallmentPlan::create([
                'student_id'              => $student->id,
                'school_year'             => $schoolYear,
                'semester'                => $semester,
                'plan_type'               => $planType,
                'total_installments'      => $totalInstallments,
                'total_amount'            => $totalAmount,
                'amount_per_installment'  => $amountPerInstallment,
                'status'                  => 'active',
                'confirmed_at'            => now(),
            ]);

            foreach ($intervals as $i => $monthOffset) {
                $isLast    = $i === count($intervals) - 1;
                $amountDue = $isLast
                    ? $amountPerInstallment + $remainder
                    : $amountPerInstallment;

                InstallmentSchedule::create([
                    'installment_plan_id' => $plan->id,
                    'student_id'          => $student->id,
                    'installment_number'  => $i + 1,
                    'amount_due'          => $amountDue,
                    'due_date'            => $semStart->copy()->addMonths($monthOffset)->toDateString(),
                ]);
            }
        });

        return redirect()->route('student.installments')
            ->with('success', 'Your installment plan has been confirmed! 🎉');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // HELPERS
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Compute outstanding balance = total fees − total completed payments.
     */
    private function computeBalance(int $studentId, string $schoolYear, string $semester): float
    {
        $totalFees = StudentFee::join('fees', 'student_fees.fee_id', '=', 'fees.id')
            ->where('student_fees.student_id', $studentId)
            ->where('fees.school_year', $schoolYear)
            ->where('fees.semester', $semester)
            ->where('fees.status', 'active')
            ->sum('fees.amount');

        $totalPaid = Payment::where('student_id', $studentId)
            ->where('school_year', $schoolYear)
            ->where('semester', $semester)
            ->where('status', 'completed')
            ->sum('amount');

        return max(0, (float) $totalFees - (float) $totalPaid);
    }

    /**
     * Format an active InstallmentPlan into the array the view expects.
     */
    private function formatActivePlan(InstallmentPlan $plan): array
    {
        $paidCount = $plan->schedules->where('is_paid', true)->count();
        $nextDue   = $plan->schedules
            ->where('is_paid', false)
            ->sortBy('due_date')
            ->first();

        $daysUntil = $nextDue
            ? (int) now()->startOfDay()->diffInDays(
                Carbon::parse($nextDue->due_date)->startOfDay(),
                false
            )
            : null;

        return [
            'type'         => $plan->plan_type,
            'installments' => $plan->total_installments,
            'amount_per'   => (float) $plan->amount_per_installment,
            'total_amount' => (float) $plan->total_amount,
            'paid_count'   => $paidCount,
            'next_due'     => $nextDue
                ? Carbon::parse($nextDue->due_date)->format('M d, Y')
                : null,
            'days_until'   => $daysUntil,
        ];
    }

    /**
     * Format a collection of InstallmentSchedule rows for the view.
     */
    private function formatSchedule($schedules): array
    {
        return $schedules
            ->sortBy('installment_number')
            ->map(fn ($s) => [
                'due_date' => Carbon::parse($s->due_date)->format('M d, Y'),
                'amount'   => (float) $s->amount_due,
                'paid'     => (bool) $s->is_paid,
                'paid_at'  => $s->paid_at
                    ? Carbon::parse($s->paid_at)->format('M d, Y')
                    : null,
                'overdue'  => (bool) $s->is_overdue,
            ])
            ->values()
            ->toArray();
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Academic calendar helpers
    // ─────────────────────────────────────────────────────────────────────────

    private function currentSchoolYear(): string
    {
        $year  = (int) now()->format('Y');
        $month = (int) now()->format('n');

        return $month >= 6
            ? "{$year}-" . ($year + 1)
            : ($year - 1) . "-{$year}";
    }

    private function currentSemester(): string
    {
        $month = (int) now()->format('n');

        if ($month >= 6 && $month <= 10) return '1';
        if ($month >= 11 || $month <= 3) return '2';
        return 'summer';
    }

    private function semesterStartDate(string $schoolYear, string $semester): Carbon
    {
        $startYear = (int) explode('-', $schoolYear)[0];

        // 2nd semester & summer belong to the second year of the school year
        if ($semester === '2' || $semester === 'summer') {
            $startYear += 1;
        }

        return Carbon::createFromFormat('Y-m-d', "{$startYear}-" . self::SEMESTER_STARTS[$semester]);
    }
}