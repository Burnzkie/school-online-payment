<?php
// app/Http/Controllers/Student/HS/HSInstallmentPlanController.php

namespace App\Http\Controllers\Student\HS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Fee;
use App\Models\Payment;
use App\Models\InstallmentPlan;
use App\Models\InstallmentSchedule;
use Carbon\Carbon;

class HSInstallmentPlanController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $isJHS = str_contains(strtolower($user->level_group ?? ''), 'junior');
        $isSHS = str_contains(strtolower($user->level_group ?? ''), 'senior');

        $schoolYear = $this->currentSchoolYear();

        // For JHS use semester='0' (no semester), SHS uses '1' or '2'
        $semester = $isSHS ? $this->currentSemester() : '1'; // JHS still needs a value for DB

        // Check for existing active plan — use model scopes
        $planQuery = InstallmentPlan::with('schedules')
            ->where('student_id', $user->id)
            ->active();

        if ($isSHS) {
            $planQuery->forSemester($schoolYear, $semester);
        } else {
            $planQuery->where('school_year', $schoolYear);
        }

        $activePlan = $planQuery->first();

        // Balance
        $feesQuery = Fee::where('student_id', $user->id)
            ->where('school_year', $schoolYear)
            ->where('status', 'active');

        if ($isSHS) {
            $feesQuery->where('semester', $semester);
        }

        $totalCharges = $feesQuery->sum('amount');

        $paymentsQuery = Payment::where('student_id', $user->id)
            ->where('school_year', $schoolYear)
            ->where('status', 'completed');

        if ($isSHS) {
            $paymentsQuery->where('semester', $semester);
        }

        $totalPaid = $paymentsQuery->sum('amount');
        $balance = max(0, $totalCharges - $totalPaid);

        return view('students.hs.installments', compact(
            'activePlan', 'balance', 'schoolYear', 'semester',
            'isJHS', 'isSHS'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'plan_type'   => ['required', 'in:full,2,3,4'],
            'school_year' => ['required', 'string'],
        ]);

        $user = Auth::user();
        $isJHS = str_contains(strtolower($user->level_group ?? ''), 'junior');
        $isSHS = str_contains(strtolower($user->level_group ?? ''), 'senior');

        $schoolYear = $request->school_year;
        $semester   = $isSHS ? $request->input('semester', '1') : '1';

        // Calculate balance
        $feesQuery = Fee::where('student_id', $user->id)
            ->where('school_year', $schoolYear)
            ->where('status', 'active');
        if ($isSHS) $feesQuery->where('semester', $semester);
        $totalCharges = $feesQuery->sum('amount');

        $paymentsQuery = Payment::where('student_id', $user->id)
            ->where('school_year', $schoolYear)
            ->where('status', 'completed');
        if ($isSHS) $paymentsQuery->where('semester', $semester);
        $totalPaid = $paymentsQuery->sum('amount');
        $balance = max(0, $totalCharges - $totalPaid);

        if ($balance <= 0) {
            return back()->with('success', 'No outstanding balance — nothing to set up!');
        }

        $planType   = $request->plan_type;
        $numParts   = $planType === 'full' ? 1 : (int) $planType;
        $perPart    = round($balance / $numParts, 2);

        DB::transaction(function () use ($user, $schoolYear, $semester, $planType, $numParts, $balance, $perPart, $isSHS) {
            // Cancel any existing plan using the model scope
            $cancelQuery = InstallmentPlan::where('student_id', $user->id)
                ->where('school_year', $schoolYear);
            if ($isSHS) {
                $cancelQuery->where('semester', $semester);
            }
            $cancelQuery->update(['status' => 'cancelled']);

            $plan = InstallmentPlan::create([
                'student_id'              => $user->id,
                'school_year'             => $schoolYear,
                'semester'                => $semester,
                'plan_type'               => $planType,
                'total_installments'      => $numParts,
                'total_amount'            => $balance,
                'amount_per_installment'  => $perPart,
                'status'                  => 'active',
                'confirmed_at'            => now(),
            ]);

            // Generate schedules — spread monthly from today
            $baseDate = Carbon::now()->startOfMonth()->addMonth();
            for ($i = 1; $i <= $numParts; $i++) {
                InstallmentSchedule::create([
                    'installment_plan_id'  => $plan->id,
                    'student_id'           => $user->id,
                    'installment_number'   => $i,
                    'amount_due'           => $perPart,
                    'due_date'             => $baseDate->copy()->addMonths($i - 1)->endOfMonth()->toDateString(),
                    'amount_paid'          => 0,
                    'is_paid'              => false,
                    'is_overdue'           => false,
                ]);
            }
        });

        return redirect()->route('hs.installments')->with('success', 'Installment plan set up successfully!');
    }

    private function currentSchoolYear(): string
    {
        $month = (int) date('n');
        $year  = (int) date('Y');
        return ($month >= 6) ? $year . '-' . ($year + 1) : ($year - 1) . '-' . $year;
    }

    private function currentSemester(): string
    {
        return ((int) date('n') >= 8) ? '1' : '2';
    }
}