<?php
// app/Http/Controllers/Admin/AdminController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Fee;
use App\Models\InstallmentPlan;
use App\Models\InstallmentSchedule;
use App\Models\Payment;
use App\Models\Scholarship;
use App\Models\StudentClearance;
use App\Models\StudentFee;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{
    // =========================================================================
    // SHARED NAV
    // =========================================================================

    private function nav(): array
    {
        return [
            ['route' => 'admin.dashboard',     'label' => 'Dashboard',     'icon' => '🏠', 'desc' => 'System overview'],
            ['route' => 'admin.users',          'label' => 'Users & Roles', 'icon' => '👤', 'desc' => 'Manage staff accounts'],
            ['route' => 'admin.students',       'label' => 'Students',      'icon' => '🎓', 'desc' => 'All enrolled students'],
            ['route' => 'admin.fees',           'label' => 'Fee Management','icon' => '💰', 'desc' => 'Fee structures'],
            ['route' => 'admin.payments',       'label' => 'Payments',      'icon' => '🧾', 'desc' => 'Transaction log'],
            ['route' => 'admin.scholarships',   'label' => 'Scholarships',  'icon' => '🎓', 'desc' => 'Discounts & waivers'],
            ['route' => 'admin.clearances',     'label' => 'Clearances',    'icon' => '✅', 'desc' => 'Hold management'],
            ['route' => 'admin.installments',   'label' => 'Installments',  'icon' => '📅', 'desc' => 'Payment plans'],
            ['route' => 'admin.reports',        'label' => 'Reports',       'icon' => '📊', 'desc' => 'Analytics & exports'],
            ['route' => 'admin.settings',       'label' => 'Settings',      'icon' => '⚙️', 'desc' => 'System config'],
            ['route' => 'admin.profile',        'label' => 'Profile',       'icon' => '🧑', 'desc' => 'My account'],
        ];
    }

    private function currentSchoolYear(): string
    {
        $month = now()->month;
        $year  = now()->year;
        return $month >= 8 ? "{$year}-" . ($year + 1) : ($year - 1) . "-{$year}";
    }

    // =========================================================================
    // DASHBOARD
    // =========================================================================

    public function dashboard()
    {
        $currentYear = $this->currentSchoolYear();

        // Revenue stats
        $totalRevenue = Payment::where('school_year', $currentYear)
            ->where('status', 'completed')->sum('amount');

        $monthlyRevenue = Payment::where('school_year', $currentYear)
            ->where('status', 'completed')
            ->whereMonth('payment_date', now()->month)
            ->whereYear('payment_date', now()->year)
            ->sum('amount');

        $todayRevenue = Payment::where('payment_date', today()->toDateString())
            ->where('status', 'completed')->sum('amount');

        $pendingPayments = Payment::where('status', 'pending')->count();

        // Student counts
        $totalStudents  = User::where('role', 'student')->count();
        $totalStaff     = User::whereIn('role', ['cashier', 'treasurer', 'admin'])->count();

        // Fee totals
        $totalFees   = Fee::where('school_year', $currentYear)->where('status', 'active')->sum('amount');
        $outstanding = max(0, $totalFees - $totalRevenue);
        $collectionRate = $totalFees > 0 ? round(($totalRevenue / $totalFees) * 100, 1) : 0;

        // Monthly chart (last 7 months)
        $monthlyChart = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthlyChart[] = [
                'month'  => $date->format('M'),
                'amount' => (float) Payment::where('status', 'completed')
                    ->whereMonth('payment_date', $date->month)
                    ->whereYear('payment_date', $date->year)
                    ->sum('amount'),
            ];
        }

        // Revenue by level
        $revenueByLevel = Payment::join('users', 'payments.student_id', '=', 'users.id')
            ->where('payments.status', 'completed')
            ->where('payments.school_year', $currentYear)
            ->select('users.level_group', DB::raw('SUM(payments.amount) as total'))
            ->groupBy('users.level_group')
            ->get()->keyBy('level_group');

        // Recent payments
        $recentPayments = Payment::with('student')
            ->where('status', 'completed')
            ->orderByDesc('payment_date')
            ->limit(8)->get();

        // Overdue installments
        $overdueCount = InstallmentSchedule::where('is_paid', false)
            ->where('due_date', '<', today())->count();

        // Clearance stats
        $clearedCount = StudentClearance::where('school_year', $currentYear)
            ->where('is_cleared', true)->count();
        $onHoldCount  = StudentClearance::where('school_year', $currentYear)
            ->where('is_cleared', false)->count();

        // Payment methods breakdown
        $paymentMethods = Payment::where('school_year', $currentYear)
            ->where('status', 'completed')
            ->select('payment_method', DB::raw('SUM(amount) as total'), DB::raw('COUNT(*) as count'))
            ->groupBy('payment_method')
            ->get();

        // Active scholarships
        $activeScholarships = Scholarship::where('school_year', $currentYear)
            ->where('status', 'active')->count();

        // Role distribution
        $roleCounts = User::select('role', DB::raw('count(*) as total'))
            ->groupBy('role')->get()->keyBy('role');

        return view('admin.dashboard', array_merge([
            'currentYear'        => $currentYear,
            'totalRevenue'       => $totalRevenue,
            'monthlyRevenue'     => $monthlyRevenue,
            'todayRevenue'       => $todayRevenue,
            'pendingPayments'    => $pendingPayments,
            'totalStudents'      => $totalStudents,
            'totalStaff'         => $totalStaff,
            'totalFees'          => $totalFees,
            'outstanding'        => $outstanding,
            'collectionRate'     => $collectionRate,
            'monthlyChart'       => $monthlyChart,
            'revenueByLevel'     => $revenueByLevel,
            'recentPayments'     => $recentPayments,
            'overdueCount'       => $overdueCount,
            'clearedCount'       => $clearedCount,
            'onHoldCount'        => $onHoldCount,
            'paymentMethods'     => $paymentMethods,
            'activeScholarships' => $activeScholarships,
            'roleCounts'         => $roleCounts,
        ], ['nav' => $this->nav()]));
    }

    // =========================================================================
    // USERS & ROLES MANAGEMENT
    // =========================================================================

    public function users(Request $request)
    {
        $query = User::whereIn('role', ['admin', 'treasurer', 'cashier', 'parent'])
            ->when($request->q, fn($q) => $q->where(fn($b) =>
                $b->where('name', 'like', "%{$request->q}%")
                  ->orWhere('email', 'like', "%{$request->q}%")))
            ->when($request->role, fn($q) => $q->where('role', $request->role));

        $users = $query->orderBy('role')->orderBy('name')->paginate(20)->withQueryString();

        $roleCounts = User::whereIn('role', ['admin', 'treasurer', 'cashier', 'parent'])
            ->select('role', DB::raw('count(*) as total'))
            ->groupBy('role')->get()->keyBy('role');

        return view('admin.users.index', array_merge([
            'users'      => $users,
            'roleCounts' => $roleCounts,
        ], ['nav' => $this->nav()]));
    }

    public function userCreate()
    {
        return view('admin.users.create', ['nav' => $this->nav()]);
    }

    public function userStore(Request $request)
    {
        $validated = $request->validate([
            'name'       => 'required|string|max:255',
            'last_name'  => 'nullable|string|max:100',
            'email'      => 'required|email|unique:users',
            'role'       => 'required|in:admin,treasurer,cashier',
            'phone'      => 'nullable|string|max:30',
            'extra_info' => 'nullable|string|max:255',
        ]);

        $validated['password'] = Hash::make('PAC@' . now()->year . '!');
        // In production, email the temp password or use email verification flow

        User::create($validated);

        return redirect()->route('admin.users')
            ->with('success', "Staff account for {$validated['name']} created. Temporary password: PAC@" . now()->year . "!");
    }

    public function userEdit(User $user)
    {
        abort_if($user->role === 'student', 404);
        return view('admin.users.edit', array_merge(['user' => $user], ['nav' => $this->nav()]));
    }

    public function userUpdate(Request $request, User $user)
    {
        abort_if($user->role === 'student', 404);

        $validated = $request->validate([
            'name'       => 'required|string|max:255',
            'last_name'  => 'nullable|string|max:100',
            'email'      => 'required|email|unique:users,email,' . $user->id,
            'role'       => 'required|in:admin,treasurer,cashier,parent',
            'phone'      => 'nullable|string|max:30',
            'extra_info' => 'nullable|string|max:255',
        ]);

        $user->update($validated);

        return redirect()->route('admin.users')
            ->with('success', "User {$user->name} updated.");
    }

    public function userResetPassword(User $user)
    {
        abort_if($user->role === 'student', 404);
        $tempPassword = 'PAC@' . now()->year . '!';
        $user->update(['password' => Hash::make($tempPassword)]);

        return back()->with('success', "Password reset. New temporary password: {$tempPassword}");
    }

    public function userDestroy(User $user)
    {
        abort_if($user->id === Auth::id(), 422, 'Cannot delete your own account.');
        abort_if($user->role === 'student', 404);

        $user->delete();
        return redirect()->route('admin.users')->with('success', 'User deleted.');
    }

    // =========================================================================
    // STUDENTS
    // =========================================================================

    public function students(Request $request)
    {
        $currentYear = $this->currentSchoolYear();

        $query = User::where('role', 'student')
            ->when($request->q, fn($q) => $q->where(fn($b) =>
                $b->where('name',       'like', "%{$request->q}%")
                  ->orWhere('student_id','like', "%{$request->q}%")
                  ->orWhere('email',     'like', "%{$request->q}%")))
            ->when($request->level_group, fn($q) => $q->where('level_group', $request->level_group))
            ->when($request->status, fn($q) => $q->where('status', $request->status));

        $students    = $query->orderBy('name')->paginate(25)->withQueryString();
        $levelGroups = User::where('role', 'student')->distinct()->pluck('level_group')->filter()->sort()->values();

        return view('admin.students.index', array_merge([
            'students'    => $students,
            'levelGroups' => $levelGroups,
            'currentYear' => $currentYear,
        ], ['nav' => $this->nav()]));
    }

    public function studentDetail(User $student)
    {
        abort_unless($student->role === 'student', 404);
        $currentYear = $this->currentSchoolYear();

        $fees = Fee::where('student_id', $student->id)
            ->where('school_year', $currentYear)->where('status', 'active')->get();

        $payments = Payment::where('student_id', $student->id)
            ->where('school_year', $currentYear)->orderByDesc('payment_date')->get();

        $totalFees = $fees->sum('amount');
        $totalPaid = $payments->where('status', 'completed')->sum('amount');
        $balance   = max(0, $totalFees - $totalPaid);

        $installmentPlan = InstallmentPlan::with('schedules')
            ->where('student_id', $student->id)
            ->where('school_year', $currentYear)
            ->where('status', 'active')->first();

        $scholarships = Scholarship::where('student_id', $student->id)
            ->where('school_year', $currentYear)->where('status', 'active')->get();

        $clearance = StudentClearance::where('student_id', $student->id)
            ->where('school_year', $currentYear)->first();

        $linkedParents = DB::table('parent_student')
            ->join('users', 'parent_student.parent_id', '=', 'users.id')
            ->where('parent_student.student_id', $student->id)
            ->select('users.id', 'users.name', 'users.last_name', 'users.email', 'users.phone', 'parent_student.link_method')
            ->get();

        return view('admin.students.detail', array_merge([
            'student'         => $student,
            'fees'            => $fees,
            'payments'        => $payments,
            'totalFees'       => $totalFees,
            'totalPaid'       => $totalPaid,
            'balance'         => $balance,
            'installmentPlan' => $installmentPlan,
            'scholarships'    => $scholarships,
            'clearance'       => $clearance,
            'linkedParents'   => $linkedParents,
            'currentYear'     => $currentYear,
        ], ['nav' => $this->nav()]));
    }

    public function studentLinkParent(Request $request, User $student)
    {
        abort_unless($student->role === 'student', 404);

        $validated = $request->validate([
            'parent_id' => 'required|exists:users,id',
        ]);

        $parent = User::findOrFail($validated['parent_id']);
        abort_unless($parent->role === 'parent', 422, 'Selected user is not a parent.');

        $exists = DB::table('parent_student')
            ->where('parent_id', $parent->id)
            ->where('student_id', $student->id)->exists();

        if (!$exists) {
            DB::table('parent_student')->insert([
                'parent_id'   => $parent->id,
                'student_id'  => $student->id,
                'link_method' => 'manual_admin',
                'linked_by'   => Auth::id(),
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
        }

        return back()->with('success', "Linked {$parent->name} to {$student->name}.");
    }

    public function studentDrop(Request $request, User $student)
    {
        abort_unless($student->role === 'student', 404);

        $request->validate([
            'drop_reason'       => 'required|string|max:255',
            'drop_reason_other' => 'nullable|string|max:255',
            'drop_notes'        => 'nullable|string|max:1000',
        ]);

        // If "Other" was selected, use the custom typed reason instead
        $reason = $request->drop_reason === 'Other'
            ? ($request->drop_reason_other ?: 'Other')
            : $request->drop_reason;

        $student->update([
            'status'          => 'dropped',
            'drop_reason'     => $reason,
            'drop_notes'      => $request->drop_notes,
            'dropped_at'      => now(),
            'dropped_by_name' => Auth::user()->name . ' ' . Auth::user()->last_name,
        ]);

        // Put student on financial hold automatically
        $currentYear = $this->currentSchoolYear();
        StudentClearance::updateOrCreate(
            ['student_id' => $student->id, 'school_year' => $currentYear],
            ['is_cleared' => false, 'hold_reason' => 'Student dropped: ' . $reason]
        );

        Log::info('Student dropped', [
            'student_id'  => $student->id,
            'student'     => $student->name . ' ' . $student->last_name,
            'reason'      => $reason,
            'dropped_by'  => Auth::id(),
        ]);

        return redirect()->route('admin.students.detail', $student)
            ->with('success', "{$student->name} {$student->last_name} has been dropped. Their clearance has been placed on hold.");
    }

    public function studentReinstate(Request $request, User $student)
    {
        abort_unless($student->role === 'student', 404);
        abort_unless($student->status === 'dropped', 422, 'Student is not currently dropped.');

        $student->update([
            'status'          => 'active',
            'drop_reason'     => null,
            'drop_notes'      => null,
            'dropped_at'      => null,
            'dropped_by_name' => null,
        ]);

        Log::info('Student reinstated', [
            'student_id'     => $student->id,
            'student'        => $student->name . ' ' . $student->last_name,
            'reinstated_by'  => Auth::id(),
        ]);

        return redirect()->route('admin.students.detail', $student)
            ->with('success', "{$student->name} {$student->last_name} has been reinstated successfully.");
    }

    // =========================================================================
    // FEE MANAGEMENT
    // =========================================================================

    public function fees(Request $request)
    {
        $currentYear = $this->currentSchoolYear();

        $query = Fee::with('student')
            ->when($request->school_year, fn($q) => $q->where('school_year', $request->school_year))
            ->when($request->semester,    fn($q) => $q->where('semester', $request->semester))
            ->when($request->status,      fn($q) => $q->where('status', $request->status))
            ->when($request->level_group, fn($q) => $q->whereHas('student', fn($sq) =>
                $sq->where('level_group', $request->level_group)))
            ->when($request->q, fn($q) => $q->where('fee_name', 'like', "%{$request->q}%"));

        $fees        = $query->orderByDesc('created_at')->paginate(25)->withQueryString();
        $schoolYears = Fee::distinct()->pluck('school_year')->sortDesc();
        $levelGroups = User::where('role', 'student')->distinct()->pluck('level_group')->filter()->sort()->values();

        $stats = [
            'total'   => Fee::where('school_year', $currentYear)->where('status', 'active')->count(),
            'amount'  => Fee::where('school_year', $currentYear)->where('status', 'active')->sum('amount'),
            'waived'  => Fee::where('school_year', $currentYear)->where('status', 'waived')->count(),
        ];

        return view('admin.fees.index', array_merge([
            'fees'        => $fees,
            'stats'       => $stats,
            'schoolYears' => $schoolYears,
            'levelGroups' => $levelGroups,
        ], ['nav' => $this->nav()]));
    }

    public function feeCreate()
    {
        $students    = User::where('role', 'student')->orderBy('name')->get(['id', 'name', 'student_id', 'level_group']);
        $levelGroups = User::where('role', 'student')->distinct()->pluck('level_group')->filter()->sort()->values();

        return view('admin.fees.create', array_merge([
            'students'    => $students,
            'levelGroups' => $levelGroups,
        ], ['nav' => $this->nav()]));
    }

    public function feeStore(Request $request)
    {
        $validated = $request->validate([
            'student_id'  => 'required|exists:users,id',
            'fee_name'    => 'required|string|max:255',
            'amount'      => 'required|numeric|min:0',
            'school_year' => ['required', 'string', 'regex:/^\d{4}-\d{4}$/'],
            'semester'    => 'required|in:1,2,summer',
            'description' => 'nullable|string|max:500',
            'status'      => 'required|in:active,waived,cancelled',
        ]);

        Fee::create($validated);

        return redirect()->route('admin.fees')->with('success', 'Fee created successfully.');
    }

    public function feeBulkCreate()
    {
        $levelGroups = User::where('role', 'student')->distinct()->pluck('level_group')->filter()->sort()->values();
        return view('admin.fees.bulk-create', array_merge(['levelGroups' => $levelGroups], ['nav' => $this->nav()]));
    }

    public function feeBulkStore(Request $request)
    {
        $validated = $request->validate([
            'fee_name'    => 'required|string|max:255',
            'amount'      => 'required|numeric|min:0',
            'school_year' => ['required', 'string', 'regex:/^\d{4}-\d{4}$/'],
            'semester'    => 'required|in:1,2,summer',
            'level_group' => 'required|string',
            'description' => 'nullable|string|max:500',
        ]);

        $students = User::where('role', 'student')
            ->when($validated['level_group'] !== 'all', fn($q) => $q->where('level_group', $validated['level_group']))
            ->pluck('id');

        $count = 0;
        foreach ($students as $studentId) {
            $exists = Fee::where('student_id', $studentId)
                ->where('fee_name', $validated['fee_name'])
                ->where('school_year', $validated['school_year'])
                ->where('semester', $validated['semester'])
                ->exists();

            if (!$exists) {
                Fee::create([
                    'student_id'  => $studentId,
                    'fee_name'    => $validated['fee_name'],
                    'amount'      => $validated['amount'],
                    'school_year' => $validated['school_year'],
                    'semester'    => $validated['semester'],
                    'description' => $validated['description'] ?? null,
                    'status'      => 'active',
                ]);
                $count++;
            }
        }

        return redirect()->route('admin.fees')
            ->with('success', "Bulk fee \"{$validated['fee_name']}\" applied to {$count} students.");
    }

    public function feeEdit(Fee $fee)
    {
        return view('admin.fees.edit', array_merge(['fee' => $fee], ['nav' => $this->nav()]));
    }

    public function feeUpdate(Request $request, Fee $fee)
    {
        $validated = $request->validate([
            'fee_name'    => 'required|string|max:255',
            'amount'      => 'required|numeric|min:0',
            'school_year' => ['required', 'string', 'regex:/^\d{4}-\d{4}$/'],
            'semester'    => 'required|in:1,2,summer',
            'description' => 'nullable|string|max:500',
            'status'      => 'required|in:active,waived,cancelled',
        ]);

        $fee->update($validated);

        return redirect()->route('admin.fees')->with('success', 'Fee updated.');
    }

    public function feeDestroy(Fee $fee)
    {
        $fee->delete();
        return back()->with('success', 'Fee deleted.');
    }

    // =========================================================================
    // PAYMENTS
    // =========================================================================

    public function payments(Request $request)
    {
        $query = Payment::with(['student', 'cashier'])
            ->when($request->q, fn($q) => $q->where(fn($b) =>
                $b->whereHas('student', fn($sq) =>
                    $sq->where('name', 'like', "%{$request->q}%")
                      ->orWhere('student_id', 'like', "%{$request->q}%"))
                  ->orWhere('or_number', 'like', "%{$request->q}%")))
            ->when($request->method,     fn($q) => $q->where('payment_method', $request->method))
            ->when($request->status,     fn($q) => $q->where('status', $request->status))
            ->when($request->school_year,fn($q) => $q->where('school_year', $request->school_year))
            ->when($request->date_from,  fn($q) => $q->where('payment_date', '>=', $request->date_from))
            ->when($request->date_to,    fn($q) => $q->where('payment_date', '<=', $request->date_to));

        $payments    = $query->orderByDesc('payment_date')->paginate(25)->withQueryString();
        $totalAmount = (clone $query)->where('status', 'completed')->sum('amount');
        $schoolYears = Payment::distinct()->pluck('school_year')->sortDesc();

        $stats = [
            'today'     => Payment::where('payment_date', today())->where('status', 'completed')->sum('amount'),
            'pending'   => Payment::where('status', 'pending')->count(),
            'completed' => Payment::where('status', 'completed')->count(),
        ];

        return view('admin.payments.index', array_merge([
            'payments'    => $payments,
            'totalAmount' => $totalAmount,
            'schoolYears' => $schoolYears,
            'stats'       => $stats,
        ], ['nav' => $this->nav()]));
    }

    public function paymentShow(Payment $payment)
    {
        $payment->load('student', 'cashier');
        return view('admin.payments.show', array_merge(['payment' => $payment], ['nav' => $this->nav()]));
    }

    public function paymentVoid(Payment $payment)
    {
        abort_if($payment->status === 'refunded', 422, 'Already voided.');
        $payment->update(['status' => 'refunded']);

        Log::info("Admin #{" . Auth::id() . "} voided payment #{$payment->id}");

        return back()->with('success', "Payment OR#{$payment->or_number} voided.");
    }

    // =========================================================================
    // SCHOLARSHIPS
    // =========================================================================

    public function scholarships(Request $request)
    {
        $currentYear = $this->currentSchoolYear();

        $query = Scholarship::with('student')
            ->when($request->school_year, fn($q) => $q->where('school_year', $request->school_year))
            ->when($request->semester,    fn($q) => $q->where('semester', $request->semester))
            ->when($request->status,      fn($q) => $q->where('status', $request->status))
            ->when($request->q, fn($q) => $q->whereHas('student', fn($sq) =>
                $sq->where('name', 'like', "%{$request->q}%")
                  ->orWhere('student_id', 'like', "%{$request->q}%")));

        $scholarships = $query->orderByDesc('created_at')->paginate(20)->withQueryString();
        $schoolYears  = Scholarship::distinct()->pluck('school_year')->sortDesc();

        $stats = [
            'active'         => Scholarship::where('school_year', $currentYear)->where('status', 'active')->count(),
            'revoked'        => Scholarship::where('school_year', $currentYear)->where('status', 'revoked')->count(),
            'total_discount' => Scholarship::where('school_year', $currentYear)->where('status', 'active')->where('discount_type', 'fixed')->sum('discount_value'),
        ];

        return view('admin.scholarships.index', array_merge([
            'scholarships' => $scholarships,
            'schoolYears'  => $schoolYears,
            'stats'        => $stats,
        ], ['nav' => $this->nav()]));
    }

    public function scholarshipCreate()
    {
        $students = User::where('role', 'student')->orderBy('name')->get(['id', 'name', 'student_id', 'level_group']);
        $feeNames = Fee::distinct()->orderBy('fee_name')->pluck('fee_name');

        return view('admin.scholarships.create', array_merge([
            'students' => $students,
            'feeNames' => $feeNames,
        ], ['nav' => $this->nav()]));
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

        return redirect()->route('admin.scholarships')
            ->with('success', "Scholarship \"{$data['scholarship_name']}\" granted.");
    }

    public function scholarshipRevoke(Scholarship $scholarship)
    {
        $scholarship->update(['status' => 'revoked']);
        return back()->with('success', 'Scholarship revoked.');
    }

    // =========================================================================
    // CLEARANCES
    // =========================================================================

    public function clearances(Request $request)
    {
        $schoolYear = $request->school_year ?? $this->currentSchoolYear();
        $semester   = $request->semester   ?? '1';

        $this->syncAllClearances($schoolYear, $semester);

        $query = StudentClearance::with('student')
            ->where('school_year', $schoolYear)
            ->where('semester', $semester)
            ->when($request->status === 'cleared', fn($q) => $q->where('is_cleared', true))
            ->when($request->status === 'on_hold', fn($q) => $q->where('is_cleared', false))
            ->when($request->level_group, fn($q) => $q->whereHas('student', fn($sq) =>
                $sq->where('level_group', $request->level_group)))
            ->when($request->q, fn($q) => $q->whereHas('student', fn($sq) =>
                $sq->where('name', 'like', "%{$request->q}%")
                  ->orWhere('student_id', 'like', "%{$request->q}%")));

        $clearances  = $query->orderBy('is_cleared')->orderByDesc('updated_at')->paginate(25)->withQueryString();
        $schoolYears = Fee::distinct()->pluck('school_year')->sortDesc();
        $levelGroups = User::where('role', 'student')->distinct()->pluck('level_group')->filter()->sort()->values();

        $stats = [
            'cleared' => StudentClearance::where('school_year', $schoolYear)->where('semester', $semester)->where('is_cleared', true)->count(),
            'on_hold' => StudentClearance::where('school_year', $schoolYear)->where('semester', $semester)->where('is_cleared', false)->count(),
            'manual'  => StudentClearance::where('school_year', $schoolYear)->where('semester', $semester)->where('manual_override', true)->count(),
        ];

        return view('admin.clearances.index', array_merge([
            'clearances'  => $clearances,
            'stats'       => $stats,
            'schoolYear'  => $schoolYear,
            'semester'    => $semester,
            'schoolYears' => $schoolYears,
            'levelGroups' => $levelGroups,
        ], ['nav' => $this->nav()]));
    }

    public function clearanceGrant(Request $request, StudentClearance $clearance)
    {
        $request->validate(['override_note' => 'nullable|string|max:500']);
        $clearance->update([
            'is_cleared'      => true,
            'manual_override' => true,
            'override_note'   => $request->override_note ?? 'Manually cleared by admin.',
            'cleared_by'      => Auth::id(),
            'cleared_at'      => now(),
            'hold_reason'     => null,
        ]);
        return back()->with('success', "✅ {$clearance->student->name} cleared.");
    }

    public function clearanceHold(Request $request, StudentClearance $clearance)
    {
        $request->validate(['hold_reason' => 'required|string|max:500']);
        $clearance->update([
            'is_cleared'      => false,
            'manual_override' => true,
            'hold_reason'     => $request->hold_reason,
            'cleared_by'      => null,
            'cleared_at'      => null,
        ]);
        return back()->with('success', "🚫 Hold placed on {$clearance->student->name}.");
    }

    private function syncAllClearances(string $schoolYear, string $semester): void
    {
        $fees = Fee::where('school_year', $schoolYear)->where('semester', $semester)
            ->where('status', 'active')
            ->select('student_id', DB::raw('SUM(amount) as total'))
            ->groupBy('student_id')->pluck('total', 'student_id');

        $paid = Payment::where('school_year', $schoolYear)->where('semester', $semester)
            ->where('status', 'completed')
            ->select('student_id', DB::raw('SUM(amount) as total'))
            ->groupBy('student_id')->pluck('total', 'student_id');

        $allStudentIds = $fees->keys()->merge($paid->keys())->unique();

        foreach ($allStudentIds as $studentId) {
            $balance = max(0, ($fees[$studentId] ?? 0) - ($paid[$studentId] ?? 0));

            $clearance = StudentClearance::firstOrNew([
                'student_id'  => $studentId,
                'school_year' => $schoolYear,
                'semester'    => $semester,
            ]);

            if (!$clearance->manual_override) {
                $clearance->is_cleared  = $balance <= 0;
                $clearance->hold_reason = $balance > 0 ? "Balance of ₱" . number_format($balance, 2) . " unpaid." : null;
                $clearance->save();
            }
        }
    }

    // =========================================================================
    // INSTALLMENTS
    // =========================================================================

    public function installments(Request $request)
    {
        $query = InstallmentPlan::with(['student', 'schedules'])
            ->when($request->school_year, fn($q) => $q->where('school_year', $request->school_year))
            ->when($request->semester,    fn($q) => $q->where('semester', $request->semester))
            ->when($request->status,      fn($q) => $q->where('status', $request->status))
            ->when($request->q, fn($q) => $q->whereHas('student', fn($sq) =>
                $sq->where('name', 'like', "%{$request->q}%")
                  ->orWhere('student_id', 'like', "%{$request->q}%")));

        $plans = $query->orderByDesc('created_at')->paginate(20)->withQueryString();

        $overdueSchedules = InstallmentSchedule::with(['plan.student'])
            ->where('is_paid', false)->where('due_date', '<', today())
            ->orderBy('due_date')->limit(10)->get();

        $schoolYears = InstallmentPlan::distinct()->pluck('school_year')->sortDesc();

        $stats = [
            'active'    => InstallmentPlan::where('status', 'active')->count(),
            'completed' => InstallmentPlan::where('status', 'completed')->count(),
            'overdue'   => InstallmentSchedule::where('is_paid', false)->where('due_date', '<', today())->count(),
        ];

        return view('admin.installments.index', array_merge([
            'plans'            => $plans,
            'overdueSchedules' => $overdueSchedules,
            'schoolYears'      => $schoolYears,
            'stats'            => $stats,
        ], ['nav' => $this->nav()]));
    }

    // =========================================================================
    // REPORTS
    // =========================================================================

    public function reports(Request $request)
    {
        $currentYear = $this->currentSchoolYear();
        $schoolYear  = $request->school_year ?? $currentYear;
        $semester    = $request->semester   ?? '1';

        // Collection summary by method
        $byMethod = Payment::where('school_year', $schoolYear)->where('status', 'completed')
            ->select('payment_method', DB::raw('SUM(amount) as total'), DB::raw('COUNT(*) as count'))
            ->groupBy('payment_method')->get();

        // Collection by level group
        $byLevel = Payment::join('users', 'payments.student_id', '=', 'users.id')
            ->where('payments.school_year', $schoolYear)
            ->where('payments.status', 'completed')
            ->select('users.level_group', DB::raw('SUM(payments.amount) as total'))
            ->groupBy('users.level_group')->get();

        // Fee head breakdown
        $byFee = Fee::where('school_year', $schoolYear)->where('semester', $semester)->where('status', 'active')
            ->select('fee_name', DB::raw('SUM(amount) as total'), DB::raw('COUNT(*) as count'))
            ->groupBy('fee_name')->orderByDesc('total')->get();

        // Defaulters (students with balance > 0)
        $fees = Fee::where('school_year', $schoolYear)->where('semester', $semester)->where('status', 'active')
            ->select('student_id', DB::raw('SUM(amount) as total_fees'))->groupBy('student_id')
            ->pluck('total_fees', 'student_id');

        $paid = Payment::where('school_year', $schoolYear)->where('semester', $semester)->where('status', 'completed')
            ->select('student_id', DB::raw('SUM(amount) as total_paid'))->groupBy('student_id')
            ->pluck('total_paid', 'student_id');

        $defaulters = User::where('role', 'student')
            ->whereIn('id', $fees->keys())->orderBy('name')->get()
            ->map(fn($s) => [
                'student' => $s,
                'balance' => max(0, ($fees[$s->id] ?? 0) - ($paid[$s->id] ?? 0)),
            ])->filter(fn($r) => $r['balance'] > 0)->sortByDesc('balance')->values();

        // Aging buckets
        $aging = [
            'current' => InstallmentSchedule::where('is_paid', false)->where('due_date', '>=', today())->sum('amount_due'),
            '1-30'    => InstallmentSchedule::where('is_paid', false)->whereBetween('due_date', [today()->subDays(30), today()->subDays(1)])->sum('amount_due'),
            '31-60'   => InstallmentSchedule::where('is_paid', false)->whereBetween('due_date', [today()->subDays(60), today()->subDays(31)])->sum('amount_due'),
            '61-90'   => InstallmentSchedule::where('is_paid', false)->whereBetween('due_date', [today()->subDays(90), today()->subDays(61)])->sum('amount_due'),
            '90+'     => InstallmentSchedule::where('is_paid', false)->where('due_date', '<', today()->subDays(90))->sum('amount_due'),
        ];

        $schoolYears = Fee::distinct()->pluck('school_year')->merge(Payment::distinct()->pluck('school_year'))
            ->unique()->sortDesc()->values();

        return view('admin.reports.index', array_merge([
            'byMethod'    => $byMethod,
            'byLevel'     => $byLevel,
            'byFee'       => $byFee,
            'defaulters'  => $defaulters,
            'aging'       => $aging,
            'schoolYear'  => $schoolYear,
            'semester'    => $semester,
            'schoolYears' => $schoolYears,
        ], ['nav' => $this->nav()]));
    }

    // =========================================================================
    // SETTINGS
    // =========================================================================

    public function settings()
    {
        return view('admin.settings.index', ['nav' => $this->nav()]);
    }

    public function settingsUpdate(Request $request)
    {
        // Save to config / DB settings table as needed
        return back()->with('success', 'Settings saved.');
    }

    // =========================================================================
    // PROFILE
    // =========================================================================

    public function profile()
    {
        return view('admin.profile.index', array_merge(['user' => Auth::user()], ['nav' => $this->nav()]));
    }

    public function profileEdit()
    {
        return view('admin.profile.edit', array_merge(['user' => Auth::user()], ['nav' => $this->nav()]));
    }

    public function profileUpdate(Request $request)
    {
        $user = Auth::user();
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'last_name'   => 'nullable|string|max:100',
            'middle_name' => 'nullable|string|max:100',
            'email'       => 'required|email|unique:users,email,' . $user->id,
            'phone'       => 'nullable|string|max:30',
        ]);
        $user->update($validated);
        return redirect()->route('admin.profile')->with('success', 'Profile updated.');
    }

    public function profileUpdatePhoto(Request $request)
    {
        $request->validate(['photo' => 'required|image|mimes:jpg,jpeg,png,webp|max:5120']);
        $user = Auth::user();
        if ($user->profile_picture) {
            Storage::disk('public')->delete($user->profile_picture);
        }
        $path = $request->file('photo')->store('profile-pictures', 'public');
        $user->update(['profile_picture' => $path]);
        return back()->with('success', 'Profile photo updated.');
    }

    public function profileChangePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password'         => 'required|min:8|confirmed',
        ]);

        if (!Hash::check($request->current_password, Auth::user()->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        Auth::user()->update(['password' => Hash::make($request->password)]);
        return back()->with('success', 'Password changed successfully.');
    }
}