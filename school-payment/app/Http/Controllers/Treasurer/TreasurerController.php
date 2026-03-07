<?php

namespace App\Http\Controllers\Treasurer;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Fee;
use App\Models\Payment;
use App\Models\StudentFee;
use App\Models\InstallmentPlan;
use App\Models\InstallmentSchedule;
use App\Models\Scholarship;
use App\Models\StudentClearance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class TreasurerController extends Controller
{
    // ─────────────────────────────────────────────
    // DASHBOARD
    // ─────────────────────────────────────────────
    public function dashboard()
    {
        $currentYear = $this->currentSchoolYear();

        // Revenue stats
        $totalRevenue = Payment::where('school_year', $currentYear)
            ->where('status', 'completed')
            ->sum('amount');

        $monthlyRevenue = Payment::where('school_year', $currentYear)
            ->where('status', 'completed')
            ->whereMonth('payment_date', now()->month)
            ->whereYear('payment_date', now()->year)
            ->sum('amount');

        $pendingPayments = Payment::where('status', 'pending')->count();

        // Student counts
        $totalStudents   = User::where('role', 'student')->count();
        $paidStudents    = Payment::where('school_year', $currentYear)
            ->where('status', 'completed')
            ->distinct('student_id')
            ->count('student_id');
        $unpaidStudents  = $totalStudents - $paidStudents;

        // Total fees assessed
        $totalFees = Fee::where('school_year', $currentYear)
            ->where('status', 'active')
            ->sum('amount');

        // Outstanding balance
        $outstanding = max(0, $totalFees - $totalRevenue);

        // Monthly revenue chart (last 6 months)
        $monthlyChart = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthlyChart[] = [
                'month'  => $date->format('M Y'),
                'amount' => (float) Payment::where('status', 'completed')
                    ->whereMonth('payment_date', $date->month)
                    ->whereYear('payment_date', $date->year)
                    ->sum('amount'),
            ];
        }

        // Revenue by level group
        $revenueByLevel = Payment::join('users', 'payments.student_id', '=', 'users.id')
            ->where('payments.status', 'completed')
            ->where('payments.school_year', $currentYear)
            ->select('users.level_group', DB::raw('SUM(payments.amount) as total'))
            ->groupBy('users.level_group')
            ->get()
            ->keyBy('level_group');

        // Recent payments
        $recentPayments = Payment::with('student')
            ->where('status', 'completed')
            ->orderByDesc('payment_date')
            ->limit(8)
            ->get();

        // Overdue installments
        $overdueCount = InstallmentSchedule::where('is_paid', false)
            ->where('due_date', '<', today())
            ->count();

        // ── Aging buckets (based on installment due dates) ──────────────────
        $aging = [
            '1-30'  => InstallmentSchedule::where('is_paid', false)
                ->whereBetween('due_date', [today()->subDays(30), today()])
                ->sum('amount_due'),
            '31-60' => InstallmentSchedule::where('is_paid', false)
                ->whereBetween('due_date', [today()->subDays(60), today()->subDays(31)])
                ->sum('amount_due'),
            '61-90' => InstallmentSchedule::where('is_paid', false)
                ->whereBetween('due_date', [today()->subDays(90), today()->subDays(61)])
                ->sum('amount_due'),
            '90+'   => InstallmentSchedule::where('is_paid', false)
                ->where('due_date', '<', today()->subDays(90))
                ->sum('amount_due'),
        ];

        // ── Collection rate ───────────────────────────────────────────────────
        $collectionRate = $totalFees > 0
            ? round(($totalRevenue / $totalFees) * 100, 1)
            : 0;

        // ── Top delinquent students (highest balance, unpaid installments) ────
        $topDelinquent = User::where('role', 'student')
            ->whereHas('fees', fn($q) => $q->where('school_year', $currentYear)->where('status', 'active'))
            ->with(['fees' => fn($q) => $q->where('school_year', $currentYear)->where('status', 'active'),
                    'payments' => fn($q) => $q->where('school_year', $currentYear)->where('status', 'completed')])
            ->get()
            ->map(function ($s) {
                $fees = $s->fees->sum('amount');
                $paid = $s->payments->sum('amount');
                return ['student' => $s, 'balance' => max(0, $fees - $paid)];
            })
            ->filter(fn($r) => $r['balance'] > 0)
            ->sortByDesc('balance')
            ->take(5)
            ->values();

        // ── Students on hold ─────────────────────────────────────────────────
        $onHoldCount = StudentClearance::where('school_year', $currentYear)
            ->where('is_cleared', false)
            ->count();

        // ── Active scholarships this year ─────────────────────────────────────
        $activeScholarships = Scholarship::where('school_year', $currentYear)
            ->where('status', 'active')
            ->count();

        return view('treasurer.dashboard', compact(
            'totalRevenue', 'monthlyRevenue', 'pendingPayments',
            'totalStudents', 'paidStudents', 'unpaidStudents',
            'totalFees', 'outstanding', 'monthlyChart',
            'revenueByLevel', 'recentPayments', 'overdueCount', 'currentYear',
            'aging', 'collectionRate', 'topDelinquent', 'onHoldCount', 'activeScholarships'
        ));
    }

    // ─────────────────────────────────────────────
    // FEES MANAGEMENT
    // ─────────────────────────────────────────────
    public function fees(Request $request)
    {
        $query = Fee::with('student')
            ->when($request->school_year, fn($q) => $q->where('school_year', $request->school_year))
            ->when($request->semester,    fn($q) => $q->where('semester', $request->semester))
            ->when($request->status,      fn($q) => $q->where('status', $request->status))
            ->when($request->level_group, fn($q) => $q->whereHas('student', fn($sq) =>
                $sq->where('level_group', $request->level_group)))
            ->when($request->search,      fn($q) => $q->where('fee_name', 'like', "%{$request->search}%")
                ->orWhereHas('student', fn($sq) =>
                    $sq->where('name', 'like', "%{$request->search}%")
                       ->orWhere('student_id', 'like', "%{$request->search}%")));

        $fees = $query->orderByDesc('created_at')->paginate(20)->withQueryString();

        $schoolYears = Fee::distinct()->pluck('school_year')->sortDesc();
        $stats = [
            'total'     => Fee::where('school_year', $this->currentSchoolYear())->where('status', 'active')->sum('amount'),
            'count'     => Fee::where('school_year', $this->currentSchoolYear())->count(),
            'waived'    => Fee::where('school_year', $this->currentSchoolYear())->where('status', 'waived')->count(),
            'cancelled' => Fee::where('school_year', $this->currentSchoolYear())->where('status', 'cancelled')->count(),
        ];

        return view('treasurer.fees.index', compact('fees', 'schoolYears', 'stats'));
    }

    public function feesCreate()
    {
        $students   = User::where('role', 'student')->orderBy('name')->get(['id', 'name', 'student_id', 'level_group', 'year_level']);
        $levelGroups = User::where('role', 'student')->distinct()->pluck('level_group')->filter()->sort()->values();
        return view('treasurer.fees.create', compact('students', 'levelGroups'));
    }

    public function feesBulkCreate()
    {
        $levelGroups = User::where('role', 'student')
            ->distinct()->pluck('level_group')->filter()->sort()->values();

        $strands = User::where('role', 'student')
            ->whereNotNull('strand')
            ->distinct()->pluck('strand')->filter()->sort()->values();

        $departments = User::where('role', 'student')
            ->whereNotNull('department')
            ->distinct()->pluck('department')->filter()->sort()->values();

        $programsByDepartment = User::where('role', 'student')
            ->whereNotNull('program')
            ->whereNotNull('department')
            ->select('department', 'program')
            ->distinct()
            ->get()
            ->groupBy('department')
            ->map(fn($rows) => $rows->pluck('program')->sort()->values());

        return view('treasurer.fees.bulk-create', compact(
            'levelGroups', 'strands', 'departments', 'programsByDepartment'
        ));
    }

    public function feesBatchCount(Request $request)
    {
        $query = User::where('role', 'student');

        if ($request->level_group) {
            $query->where('level_group', $request->level_group);
        }
        if ($request->year_level) {
            $query->where('year_level', $request->year_level);
        }
        if ($request->strand) {
            $query->where('strand', $request->strand);
        }
        if ($request->department) {
            $query->where('department', $request->department);
        }
        if ($request->program) {
            $query->where('program', $request->program);
        }

        $count = $query->count();

        return response()->json(['count' => $count]);
    }

    public function feesStore(Request $request)
    {
        $request->validate([
            'student_id'  => 'required|exists:users,id',
            'school_year' => 'required|string|max:20',
            'semester'    => 'required|in:1,2,summer',
            'fee_name'    => 'required|string|max:255',
            'amount'      => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'status'      => 'required|in:active,waived,cancelled',
        ]);

        Fee::create($request->only(['student_id', 'school_year', 'semester', 'fee_name', 'amount', 'description', 'status']));

        return redirect()->route('treasurer.fees')->with('success', 'Fee created successfully.');
    }

    public function feesBulkStore(Request $request)
    {
        $request->validate([
            'level_group'  => 'required|string',
            'school_year'  => 'required|string|max:20',
            'semester'     => 'required|in:1,2,summer',
            'fee_name'     => 'required|string|max:255',
            'amount'       => 'required|numeric|min:0',
            'status'       => 'required|in:active,waived,cancelled',
            'description'  => 'nullable|string',
            'year_level'   => 'nullable|string',
            'strand'       => 'nullable|string',
            'department'   => 'nullable|string',
            'program'      => 'nullable|string',
        ]);

        $students = User::where('role', 'student')
            ->where('level_group', $request->level_group)
            ->when($request->year_level,  fn($q) => $q->where('year_level', $request->year_level))
            ->when($request->strand,      fn($q) => $q->where('strand', $request->strand))
            ->when($request->department,  fn($q) => $q->where('department', $request->department))
            ->when($request->program,     fn($q) => $q->where('program', $request->program))
            ->pluck('id');

        if ($students->isEmpty()) {
            return back()->with('error', 'No students found in the selected group.');
        }

        $skipExisting = (bool) $request->skip_existing;

        $count = 0;
        foreach ($students as $studentId) {
            if ($skipExisting) {
                $exists = Fee::where('student_id', $studentId)
                    ->where('school_year', $request->school_year)
                    ->where('semester', $request->semester)
                    ->where('fee_name', $request->fee_name)
                    ->exists();

                if ($exists) continue;
            }

            Fee::create([
                'student_id'  => $studentId,
                'school_year' => $request->school_year,
                'semester'    => $request->semester,
                'fee_name'    => $request->fee_name,
                'amount'      => $request->amount,
                'description' => $request->description,
                'status'      => $request->status,
            ]);
            $count++;
        }

        return redirect()->route('treasurer.fees')->with('success', "Fee assigned to {$count} students successfully.");
    }

    public function feesEdit(Fee $fee)
    {
        $students = User::where('role', 'student')->orderBy('name')->get(['id', 'name', 'student_id', 'level_group']);
        return view('treasurer.fees.edit', compact('fee', 'students'));
    }

    public function feesUpdate(Request $request, Fee $fee)
    {
        $request->validate([
            'fee_name'    => 'required|string|max:255',
            'amount'      => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'status'      => 'required|in:active,waived,cancelled',
        ]);

        $fee->update($request->only(['fee_name', 'amount', 'description', 'status']));

        return redirect()->route('treasurer.fees')->with('success', 'Fee updated successfully.');
    }

    public function feesDestroy(Fee $fee)
    {
        $fee->delete();
        return redirect()->route('treasurer.fees')->with('success', 'Fee deleted successfully.');
    }

    // ─────────────────────────────────────────────
    // PAYMENTS / REVENUE
    // ─────────────────────────────────────────────
    public function payments(Request $request)
    {
        $query = Payment::with(['student', 'cashier'])
            ->when($request->school_year, fn($q) => $q->where('school_year', $request->school_year))
            ->when($request->semester,    fn($q) => $q->where('semester', $request->semester))
            ->when($request->status,      fn($q) => $q->where('status', $request->status))
            ->when($request->method,      fn($q) => $q->where('payment_method', $request->method))
            ->when($request->date_from,   fn($q) => $q->whereDate('payment_date', '>=', $request->date_from))
            ->when($request->date_to,     fn($q) => $q->whereDate('payment_date', '<=', $request->date_to))
            ->when($request->search,      fn($q) => $q->where('or_number', 'like', "%{$request->search}%")
                ->orWhere('reference_number', 'like', "%{$request->search}%")
                ->orWhereHas('student', fn($sq) =>
                    $sq->where('name', 'like', "%{$request->search}%")
                       ->orWhere('student_id', 'like', "%{$request->search}%")));

        $payments    = $query->orderByDesc('payment_date')->paginate(20)->withQueryString();
        $schoolYears = Payment::distinct()->pluck('school_year')->sortDesc();

        $totals = [
            'all'       => Payment::where('status', 'completed')->sum('amount'),
            'month'     => Payment::where('status', 'completed')->whereMonth('payment_date', now()->month)->whereYear('payment_date', now()->year)->sum('amount'),
            'pending'   => Payment::where('status', 'pending')->sum('amount'),
            'refunded'  => Payment::where('status', 'refunded')->sum('amount'),
        ];

        $methods = Payment::distinct()->pluck('payment_method')->filter()->sort()->values();

        return view('treasurer.payments.index', compact('payments', 'schoolYears', 'totals', 'methods'));
    }

    public function paymentShow(Payment $payment)
    {
        $payment->load(['student', 'cashier']);
        return view('treasurer.payments.show', compact('payment'));
    }

    // ─────────────────────────────────────────────
    // REPORTS
    // ─────────────────────────────────────────────
    public function reports(Request $request)
    {
        $schoolYear = $request->school_year ?? $this->currentSchoolYear();
        $semester   = $request->semester ?? '1';

        // Collection rate by level group
        $levelGroups = User::where('role', 'student')
            ->whereNotNull('level_group')
            ->distinct()
            ->pluck('level_group');

        $collectionByLevel = [];
        foreach ($levelGroups as $lg) {
            $students = User::where('role', 'student')->where('level_group', $lg)->pluck('id');

            $totalFees = Fee::whereIn('student_id', $students)
                ->where('school_year', $schoolYear)
                ->where('semester', $semester)
                ->where('status', 'active')
                ->sum('amount');

            $totalPaid = Payment::whereIn('student_id', $students)
                ->where('school_year', $schoolYear)
                ->where('semester', $semester)
                ->where('status', 'completed')
                ->sum('amount');

            $collectionByLevel[] = [
                'level_group' => $lg,
                'total_fees'  => $totalFees,
                'total_paid'  => $totalPaid,
                'outstanding' => max(0, $totalFees - $totalPaid),
                'rate'        => $totalFees > 0 ? round(($totalPaid / $totalFees) * 100, 1) : 0,
                'students'    => $students->count(),
            ];
        }

        // Payment method breakdown
        $paymentMethods = Payment::where('status', 'completed')
            ->where('school_year', $schoolYear)
            ->select('payment_method', DB::raw('COUNT(*) as count'), DB::raw('SUM(amount) as total'))
            ->groupBy('payment_method')
            ->orderByDesc('total')
            ->get();

        // Fee type breakdown
        $feeTypes = Fee::where('school_year', $schoolYear)
            ->where('semester', $semester)
            ->select('fee_name', DB::raw('COUNT(*) as count'), DB::raw('SUM(amount) as total'))
            ->groupBy('fee_name')
            ->orderByDesc('total')
            ->get();

        // Monthly trend for the school year
        $yearParts = explode('-', $schoolYear);
        $startYear = (int) ($yearParts[0] ?? date('Y'));
        $months = [];
        for ($m = 8; $m <= 12; $m++) {
            $months[] = ['year' => $startYear,   'month' => $m, 'label' => Carbon::create($startYear, $m)->format('M Y')];
        }
        for ($m = 1; $m <= 6; $m++) {
            $months[] = ['year' => $startYear + 1, 'month' => $m, 'label' => Carbon::create($startYear + 1, $m)->format('M Y')];
        }
        $trend = [];
        foreach ($months as $mo) {
            $trend[] = [
                'label'  => $mo['label'],
                'amount' => (float) Payment::where('status', 'completed')
                    ->whereYear('payment_date', $mo['year'])
                    ->whereMonth('payment_date', $mo['month'])
                    ->sum('amount'),
            ];
        }

        $schoolYears = Fee::distinct()->pluck('school_year')->merge(Payment::distinct()->pluck('school_year'))->unique()->sortDesc()->values();

        return view('treasurer.reports', compact(
            'collectionByLevel', 'paymentMethods', 'feeTypes', 'trend',
            'schoolYear', 'semester', 'schoolYears'
        ));
    }

    // ─────────────────────────────────────────────
    // STUDENTS OVERVIEW
    // ─────────────────────────────────────────────
    public function students(Request $request)
    {
        $currentYear = $this->currentSchoolYear();

        $query = User::where('role', 'student')
            ->when($request->level_group, fn($q) => $q->where('level_group', $request->level_group))
            ->when($request->year_level,  fn($q) => $q->where('year_level', $request->year_level))
            ->when($request->payment_status, function ($q) use ($request, $currentYear) {
                if ($request->payment_status === 'paid') {
                    $q->whereHas('payments', fn($pq) => $pq->where('school_year', $currentYear)->where('status', 'completed'));
                } elseif ($request->payment_status === 'unpaid') {
                    $q->whereDoesntHave('payments', fn($pq) => $pq->where('school_year', $currentYear)->where('status', 'completed'));
                }
            })
            ->when($request->search, fn($q) => $q->where('name', 'like', "%{$request->search}%")
                ->orWhere('student_id', 'like', "%{$request->search}%")
                ->orWhere('email', 'like', "%{$request->search}%"));

        $students = $query->orderBy('name')->paginate(25)->withQueryString();

        // Attach payment summary to each student
        $studentIds = $students->pluck('id');
        $feesByStudent = Fee::whereIn('student_id', $studentIds)
            ->where('school_year', $currentYear)
            ->where('status', 'active')
            ->select('student_id', DB::raw('SUM(amount) as total_fees'))
            ->groupBy('student_id')
            ->pluck('total_fees', 'student_id');

        $paidByStudent = Payment::whereIn('student_id', $studentIds)
            ->where('school_year', $currentYear)
            ->where('status', 'completed')
            ->select('student_id', DB::raw('SUM(amount) as total_paid'))
            ->groupBy('student_id')
            ->pluck('total_paid', 'student_id');

        $levelGroups = User::where('role', 'student')->distinct()->pluck('level_group')->filter()->sort()->values();

        return view('treasurer.students', compact(
            'students', 'feesByStudent', 'paidByStudent', 'levelGroups', 'currentYear'
        ));
    }

    public function studentDetail(User $student)
    {
        $currentYear = $this->currentSchoolYear();

        $fees = Fee::where('student_id', $student->id)
            ->orderByDesc('school_year')
            ->orderBy('semester')
            ->get();

        $payments = Payment::where('student_id', $student->id)
            ->with('cashier')
            ->orderByDesc('payment_date')
            ->get();

        $installmentPlan = InstallmentPlan::where('student_id', $student->id)
            ->where('school_year', $currentYear)
            ->with('schedules')
            ->latest()
            ->first();

        $totalFees = $fees->where('school_year', $currentYear)->where('status', 'active')->sum('amount');
        $totalPaid = $payments->where('school_year', $currentYear)->where('status', 'completed')->sum('amount');
        $balance   = max(0, $totalFees - $totalPaid);

        $clearance = StudentClearance::where('student_id', $student->id)
            ->where('school_year', $currentYear)
            ->where('semester', '1')
            ->first();

        $scholarships = Scholarship::where('student_id', $student->id)
            ->where('school_year', $currentYear)
            ->where('status', 'active')
            ->get();

        return view('treasurer.student-detail', compact(
            'student', 'fees', 'payments', 'installmentPlan',
            'totalFees', 'totalPaid', 'balance', 'currentYear',
            'clearance', 'scholarships'
        ));
    }

    // ─────────────────────────────────────────────
    // INSTALLMENTS OVERVIEW
    // ─────────────────────────────────────────────
    public function installments(Request $request)
    {
        $query = InstallmentPlan::with(['student', 'schedules'])
            ->when($request->school_year, fn($q) => $q->where('school_year', $request->school_year))
            ->when($request->semester,    fn($q) => $q->where('semester', $request->semester))
            ->when($request->status,      fn($q) => $q->where('status', $request->status))
            ->when($request->search,      fn($q) => $q->whereHas('student', fn($sq) =>
                $sq->where('name', 'like', "%{$request->search}%")
                   ->orWhere('student_id', 'like', "%{$request->search}%")));

        $plans = $query->orderByDesc('created_at')->paginate(20)->withQueryString();

        $overdueSchedules = InstallmentSchedule::with(['plan.student'])
            ->where('is_paid', false)
            ->where('due_date', '<', today())
            ->orderBy('due_date')
            ->limit(10)
            ->get();

        $schoolYears = InstallmentPlan::distinct()->pluck('school_year')->sortDesc();

        $stats = [
            'active'    => InstallmentPlan::where('status', 'active')->count(),
            'completed' => InstallmentPlan::where('status', 'completed')->count(),
            'overdue'   => InstallmentSchedule::where('is_paid', false)->where('due_date', '<', today())->count(),
        ];

        return view('treasurer.installments', compact('plans', 'overdueSchedules', 'schoolYears', 'stats'));
    }

    // ─────────────────────────────────────────────
    // PROFILE
    // ─────────────────────────────────────────────
    public function profile()
    {
        return view('treasurer.profile', ['user' => Auth::user()]);
    }

    public function editProfile()
    {
        return view('treasurer.profile-edit', ['user' => Auth::user()]);
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name'        => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:100',
            'last_name'   => 'nullable|string|max:100',
            'phone'       => 'nullable|string|max:30',
            'email'       => 'required|email|unique:users,email,' . $user->id,
        ]);

        $user->update($request->only(['name', 'middle_name', 'last_name', 'phone', 'email']));

        return redirect()->route('treasurer.profile')->with('success', 'Profile updated successfully.');
    }

    public function updateProfilePhoto(Request $request)
    {
        $request->validate(['photo' => 'required|image|max:2048']);
        $user = Auth::user();
        $path = $request->file('photo')->store('profile-pictures', 'public');
        $user->update(['profile_picture' => $path]);
        return back()->with('success', 'Profile photo updated.');
    }

    // ─────────────────────────────────────────────
    // HELPERS
    // ─────────────────────────────────────────────
    private function currentSchoolYear(): string
    {
        $month = now()->month;
        $year  = now()->year;
        return $month >= 8 ? "{$year}-" . ($year + 1) : ($year - 1) . "-{$year}";
    }

    // ─────────────────────────────────────────────
    // STATEMENT OF ACCOUNT (SOA)
    // ─────────────────────────────────────────────

    /**
     * GET /treasurer/students/{student}/soa
     * Renders a printable Statement of Account for one student, one semester.
     */
    public function soa(User $student, Request $request)
    {
        $schoolYear = $request->school_year ?? $this->currentSchoolYear();
        $semester   = $request->semester   ?? '1';

        $fees = Fee::where('student_id', $student->id)
            ->where('school_year', $schoolYear)
            ->where('semester', $semester)
            ->where('status', 'active')
            ->get();

        // Apply scholarships
        $scholarships = Scholarship::where('student_id', $student->id)
            ->where('school_year', $schoolYear)
            ->where('semester', $semester)
            ->where('status', 'active')
            ->get();

        $feeRows = $fees->map(function ($fee) use ($scholarships) {
            $discount = 0;
            foreach ($scholarships as $s) {
                if ($s->applies_to_fee === null || $s->applies_to_fee === $fee->fee_name) {
                    $discount += $s->computeDiscount((float) $fee->amount);
                }
            }
            return [
                'fee'         => $fee,
                'gross'       => $fee->amount,
                'discount'    => $discount,
                'net'         => max(0, $fee->amount - $discount),
            ];
        });

        $totalGross    = $feeRows->sum('gross');
        $totalDiscount = $feeRows->sum('discount');
        $totalNet      = $feeRows->sum('net');

        $payments = Payment::where('student_id', $student->id)
            ->where('school_year', $schoolYear)
            ->where('semester', $semester)
            ->where('status', 'completed')
            ->orderBy('payment_date')
            ->get();

        $totalPaid = $payments->sum('amount');
        $balance   = max(0, $totalNet - $totalPaid);

        $installmentPlan = InstallmentPlan::where('student_id', $student->id)
            ->where('school_year', $schoolYear)
            ->where('semester', $semester)
            ->with('schedules')
            ->latest()
            ->first();

        $clearance = StudentClearance::where('student_id', $student->id)
            ->where('school_year', $schoolYear)
            ->where('semester', $semester)
            ->first();

        $schoolYears = Fee::where('student_id', $student->id)
            ->distinct()->pluck('school_year')->sortDesc();

        return view('treasurer.soa.show', compact(
            'student', 'feeRows', 'scholarships', 'payments',
            'totalGross', 'totalDiscount', 'totalNet', 'totalPaid', 'balance',
            'installmentPlan', 'clearance', 'schoolYear', 'semester', 'schoolYears'
        ));
    }

    // ─────────────────────────────────────────────
    // AGING REPORT
    // ─────────────────────────────────────────────

    public function aging(Request $request)
    {
        $schoolYear = $request->school_year ?? $this->currentSchoolYear();
        $semester   = $request->semester   ?? '1';

        // Build balance per student
        $studentFees = Fee::where('school_year', $schoolYear)
            ->where('semester', $semester)
            ->where('status', 'active')
            ->select('student_id', DB::raw('SUM(amount) as total_fees'))
            ->groupBy('student_id')
            ->pluck('total_fees', 'student_id');

        $studentPaid = Payment::where('school_year', $schoolYear)
            ->where('semester', $semester)
            ->where('status', 'completed')
            ->select('student_id', DB::raw('SUM(amount) as total_paid'))
            ->groupBy('student_id')
            ->pluck('total_paid', 'student_id');

        // For aging, use the earliest unpaid installment due date as the reference
        $earliestDue = InstallmentSchedule::where('is_paid', false)
            ->join('installment_plans', 'installment_schedules.installment_plan_id', '=', 'installment_plans.id')
            ->where('installment_plans.school_year', $schoolYear)
            ->where('installment_plans.semester', $semester)
            ->select('installment_schedules.student_id', DB::raw('MIN(due_date) as earliest_due'))
            ->groupBy('installment_schedules.student_id')
            ->pluck('earliest_due', 'student_id');

        $students = User::where('role', 'student')
            ->whereIn('id', $studentFees->keys())
            ->orderBy('name')
            ->get(['id', 'name', 'student_id', 'level_group', 'year_level']);

        $rows = $students->map(function ($s) use ($studentFees, $studentPaid, $earliestDue) {
            $fees    = (float) ($studentFees[$s->id] ?? 0);
            $paid    = (float) ($studentPaid[$s->id] ?? 0);
            $balance = max(0, $fees - $paid);

            if ($balance <= 0) return null;

            $dueDate = isset($earliestDue[$s->id])
                ? Carbon::parse($earliestDue[$s->id])
                : null;

            $daysOverdue = $dueDate ? max(0, today()->diffInDays($dueDate, false) * -1) : 0;

            $bucket = match(true) {
                $daysOverdue <= 0  => 'current',
                $daysOverdue <= 30 => '1-30',
                $daysOverdue <= 60 => '31-60',
                $daysOverdue <= 90 => '61-90',
                default            => '90+',
            };

            return [
                'student'      => $s,
                'balance'      => $balance,
                'days_overdue' => $daysOverdue,
                'bucket'       => $bucket,
                'earliest_due' => $dueDate,
            ];
        })->filter()->values();

        $bucketTotals = [
            'current' => $rows->where('bucket', 'current')->sum('balance'),
            '1-30'    => $rows->where('bucket', '1-30')->sum('balance'),
            '31-60'   => $rows->where('bucket', '31-60')->sum('balance'),
            '61-90'   => $rows->where('bucket', '61-90')->sum('balance'),
            '90+'     => $rows->where('bucket', '90+')->sum('balance'),
        ];

        $schoolYears = Fee::distinct()->pluck('school_year')
            ->merge(Payment::distinct()->pluck('school_year'))
            ->unique()->sortDesc()->values();

        return view('treasurer.aging.index', compact(
            'rows', 'bucketTotals', 'schoolYear', 'semester', 'schoolYears'
        ));
    }

    // ─────────────────────────────────────────────
    // SCHOLARSHIPS & DISCOUNTS
    // ─────────────────────────────────────────────

    public function scholarships(Request $request)
    {
        $query = Scholarship::with('student')
            ->when($request->school_year, fn($q) => $q->where('school_year', $request->school_year))
            ->when($request->semester,    fn($q) => $q->where('semester', $request->semester))
            ->when($request->status,      fn($q) => $q->where('status', $request->status))
            ->when($request->search,      fn($q) => $q->whereHas('student', fn($sq) =>
                $sq->where('name', 'like', "%{$request->search}%")
                   ->orWhere('student_id', 'like', "%{$request->search}%")));

        $scholarships = $query->orderByDesc('created_at')->paginate(20)->withQueryString();

        $schoolYears = Scholarship::distinct()->pluck('school_year')->sortDesc();
        $currentYear = $this->currentSchoolYear();

        $stats = [
            'active'  => Scholarship::where('school_year', $currentYear)->where('status', 'active')->count(),
            'revoked' => Scholarship::where('school_year', $currentYear)->where('status', 'revoked')->count(),
            'total_discount' => Scholarship::where('school_year', $currentYear)
                ->where('status', 'active')
                ->where('discount_type', 'fixed')
                ->sum('discount_value'),
        ];

        return view('treasurer.scholarships.index', compact('scholarships', 'schoolYears', 'stats'));
    }

    public function scholarshipCreate()
    {
        $students  = User::where('role', 'student')->orderBy('name')
            ->get(['id', 'name', 'student_id', 'level_group']);
        $feeNames  = Fee::distinct()->orderBy('fee_name')->pluck('fee_name');
        return view('treasurer.scholarships.create', compact('students', 'feeNames'));
    }

    public function scholarshipStore(Request $request)
    {
        $data = $request->validate([
            'student_id'       => 'required|exists:users,id',
            'scholarship_name' => 'required|string|max:255',
            'school_year'      => ['required', 'string', 'regex:/^\d{4}-\d{4}$/'],
            'semester'         => 'required|in:1,2,summer',
            'discount_type'    => 'required|in:percent,fixed',
            'discount_value'   => 'required|numeric|min:0',
            'max_discount'     => 'nullable|numeric|min:0',
            'applies_to_fee'   => 'nullable|string|max:255',
            'remarks'          => 'nullable|string',
        ]);

        $data['approved_by'] = Auth::id();
        $data['status']      = 'active';

        Scholarship::create($data);

        return redirect()->route('treasurer.scholarships')
            ->with('success', "Scholarship \"{$data['scholarship_name']}\" granted successfully.");
    }

    public function scholarshipRevoke(Scholarship $scholarship)
    {
        $scholarship->update(['status' => 'revoked']);
        return back()->with('success', 'Scholarship revoked.');
    }

    // ─────────────────────────────────────────────
    // CLEARANCE / HOLD MANAGEMENT
    // ─────────────────────────────────────────────

    public function clearances(Request $request)
    {
        $schoolYear = $request->school_year ?? $this->currentSchoolYear();
        $semester   = $request->semester   ?? '1';

        // Auto-sync clearances for ALL students this semester
        // (runs fast using a single bulk query — no N+1)
        $this->syncAllClearances($schoolYear, $semester);

        $query = StudentClearance::with('student')
            ->where('school_year', $schoolYear)
            ->where('semester', $semester)
            ->when($request->status === 'cleared',   fn($q) => $q->where('is_cleared', true))
            ->when($request->status === 'on_hold',   fn($q) => $q->where('is_cleared', false))
            ->when($request->level_group, fn($q) => $q->whereHas('student', fn($sq) =>
                $sq->where('level_group', $request->level_group)))
            ->when($request->search, fn($q) => $q->whereHas('student', fn($sq) =>
                $sq->where('name', 'like', "%{$request->search}%")
                   ->orWhere('student_id', 'like', "%{$request->search}%")));

        $clearances = $query->orderBy('is_cleared')->orderByDesc('updated_at')
            ->paginate(25)->withQueryString();

        $schoolYears = Fee::distinct()->pluck('school_year')->sortDesc();
        $levelGroups = User::where('role', 'student')->distinct()->pluck('level_group')->filter()->sort()->values();

        $stats = [
            'cleared' => StudentClearance::where('school_year', $schoolYear)
                ->where('semester', $semester)->where('is_cleared', true)->count(),
            'on_hold' => StudentClearance::where('school_year', $schoolYear)
                ->where('semester', $semester)->where('is_cleared', false)->count(),
            'manual'  => StudentClearance::where('school_year', $schoolYear)
                ->where('semester', $semester)->where('manual_override', true)->count(),
        ];

        return view('treasurer.clearances.index', compact(
            'clearances', 'stats', 'schoolYear', 'semester', 'schoolYears', 'levelGroups'
        ));
    }

    public function clearanceGrant(Request $request, StudentClearance $clearance)
    {
        $request->validate(['override_note' => 'nullable|string|max:500']);
        $clearance->manualClear($request->override_note ?? 'Manually cleared by treasurer.');
        return back()->with('success', "✅ {$clearance->student->name} cleared.");
    }

    public function clearanceHold(Request $request, StudentClearance $clearance)
    {
        $request->validate(['hold_reason' => 'required|string|max:500']);
        $clearance->manualHold($request->hold_reason);
        return back()->with('success', "🚫 Hold placed on {$clearance->student->name}.");
    }

    /**
     * Bulk-sync clearances for all students in a semester based on balance.
     * Skips records with manual_override = true.
     */
    private function syncAllClearances(string $schoolYear, string $semester): void
    {
        $fees = Fee::where('school_year', $schoolYear)
            ->where('semester', $semester)
            ->where('status', 'active')
            ->select('student_id', DB::raw('SUM(amount) as total'))
            ->groupBy('student_id')
            ->pluck('total', 'student_id');

        $paid = Payment::where('school_year', $schoolYear)
            ->where('semester', $semester)
            ->where('status', 'completed')
            ->select('student_id', DB::raw('SUM(amount) as total'))
            ->groupBy('student_id')
            ->pluck('total', 'student_id');

        $allStudentIds = $fees->keys()->merge($paid->keys())->unique();

        foreach ($allStudentIds as $studentId) {
            $balance = max(0, ($fees[$studentId] ?? 0) - ($paid[$studentId] ?? 0));
            StudentClearance::syncForStudent($studentId, $schoolYear, $semester, $balance);
        }
    }
}