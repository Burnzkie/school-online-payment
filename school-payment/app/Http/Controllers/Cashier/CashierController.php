<?php
// app/Http/Controllers/Cashier/CashierController.php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Payment;
use App\Models\Fee;
use App\Models\StudentFee;
use App\Models\InstallmentPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;
use Carbon\Carbon;

class CashierController extends Controller
{
    /**
     * Shared nav for the cashier portal layout.
     */
    private function nav(): array
    {
        return [
            ['route' => 'cashier.dashboard',       'label' => 'Dashboard',    'icon' => '🏠', 'desc' => 'Overview & stats'],
            ['route' => 'cashier.students',         'label' => 'Students',     'icon' => '👥', 'desc' => 'Lookup & ledgers'],
            ['route' => 'cashier.receive-payment',  'label' => 'Receive Pay',  'icon' => '💵', 'desc' => 'Post a payment'],
            ['route' => 'cashier.transactions',     'label' => 'Transactions', 'icon' => '🧾', 'desc' => 'Payment history'],
        ];
    }

    /**
     * Dashboard — today's stats, recent payments.
     */
    public function dashboard()
    {
        $today      = Carbon::today();
        $monthStart = Carbon::now()->startOfMonth();

        $todayPayments     = Payment::where('payment_date', $today->toDateString())->get();
        $todayCollections  = $todayPayments->where('status', 'completed')->sum('amount');
        $todayPaymentCount = $todayPayments->count();

        $monthCollections = Payment::where('payment_date', '>=', $monthStart)
            ->where('status', 'completed')->sum('amount');

        $totalStudents = User::where('role', 'student')->count();

        $sy = date('Y') . '-' . (date('Y') + 1);

        $studentsWithBalance = User::where('role', 'student')
            ->whereHas('fees', function ($q) use ($sy) {
                $q->where('school_year', $sy)->where('status', 'active');
            })
            ->get()
            ->filter(function ($student) use ($sy) {
                $totalFees = $student->fees()->where('school_year', $sy)->where('status', 'active')->sum('amount');
                $totalPaid = $student->payments()->where('school_year', $sy)->where('status', 'completed')->sum('amount');
                return $totalFees > $totalPaid;
            })
            ->count();

        $recentPayments = Payment::with('student')
            ->latest('payment_date')
            ->take(10)
            ->get();

        return view('cashier.dashboard', array_merge([
            'todayCollections'    => $todayCollections,
            'todayPaymentCount'   => $todayPaymentCount,
            'monthCollections'    => $monthCollections,
            'totalStudents'       => $totalStudents,
            'studentsWithBalance' => $studentsWithBalance,
            'recentPayments'      => $recentPayments,
        ], ['nav' => $this->nav()]));
    }

    /**
     * Students list — searchable, filterable.
     */
    public function students(Request $request)
    {
        $query = User::where('role', 'student');

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($builder) use ($q) {
                $builder->where('name', 'like', "%{$q}%")
                    ->orWhere('student_id', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%");
            });
        }

        if ($request->filled('level_group')) {
            $query->where('level_group', $request->level_group);
        }

        $sy = date('Y') . '-' . (date('Y') + 1);

        if ($request->balance_filter === 'with_balance') {
            $query->whereHas('fees', fn($q) => $q->where('school_year', $sy)->where('status', 'active'));
        }

        $students = $query->orderBy('name')->paginate(20);

        return view('cashier.students', array_merge([
            'students' => $students,
        ], ['nav' => $this->nav()]));
    }

    /**
     * AJAX student search for the receive-payment form.
     */
    public function searchStudents(Request $request)
    {
        $q  = $request->get('q', '');
        $sy = date('Y') . '-' . (date('Y') + 1);

        $students = User::where('role', 'student')
            ->where(function ($builder) use ($q) {
                $builder->where('name', 'like', "%{$q}%")
                    ->orWhere('student_id', 'like', "%{$q}%");
            })
            ->limit(10)
            ->get(['id', 'name', 'student_id', 'email', 'level_group', 'year_level'])
            ->map(function ($student) use ($sy) {
                $totalFees        = Fee::where('student_id', $student->id)->where('school_year', $sy)->where('status', 'active')->sum('amount');
                $totalPaid        = Payment::where('student_id', $student->id)->where('school_year', $sy)->where('status', 'completed')->sum('amount');
                $student->balance = max(0, $totalFees - $totalPaid);
                return $student;
            });

        return response()->json($students);
    }

    /**
     * Show student ledger with full fee/payment breakdown.
     */
    public function studentLedger(Request $request, User $student)
    {
        abort_unless($student->role === 'student', 404);

        $sy       = $request->get('school_year', date('Y') . '-' . (date('Y') + 1));
        $semester = $request->get('semester', '1');

        $isJHS = str_contains(strtolower($student->level_group ?? ''), 'junior');
        $isSHS = str_contains(strtolower($student->level_group ?? ''), 'senior');

        $feesQuery = Fee::where('student_id', $student->id)->where('school_year', $sy);
        if ($isSHS) {
            $feesQuery->where('semester', $semester);
        }
        $fees = $feesQuery->get()->map(function ($fee) use ($student) {
            $fee->studentFee = StudentFee::where('student_id', $student->id)
                ->where('fee_id', $fee->id)->first();
            return $fee;
        });

        $paymentsQuery = Payment::where('student_id', $student->id)->where('school_year', $sy);
        if ($isSHS) {
            $paymentsQuery->where('semester', $semester);
        }
        $payments = $paymentsQuery->orderByDesc('payment_date')->get();

        $totalCharges = $fees->where('status', 'active')->sum('amount');
        $totalPaid    = $payments->where('status', 'completed')->sum('amount');
        $balance      = max(0, $totalCharges - $totalPaid);

        $installmentPlan = InstallmentPlan::with('schedules')
            ->where('student_id', $student->id)
            ->where('school_year', $sy)
            ->when($isSHS, fn($q) => $q->where('semester', $semester))
            ->where('status', 'active')
            ->first();

        $student->balance = $balance;

        return view('cashier.student-ledger', array_merge([
            'student'          => $student,
            'fees'             => $fees,
            'payments'         => $payments,
            'totalCharges'     => $totalCharges,
            'totalPaid'        => $totalPaid,
            'balance'          => $balance,
            'selectedYear'     => $sy,
            'selectedSemester' => $semester,
            'installmentPlan'  => $installmentPlan,
        ], ['nav' => $this->nav()]));
    }

    /**
     * Show receive payment form.
     */
    public function receivePaymentForm(Request $request)
    {
        $preselectedStudent = null;

        if ($request->filled('student_id')) {
            $student = User::find($request->student_id);
            if ($student && $student->role === 'student') {
                $sy = date('Y') . '-' . (date('Y') + 1);
                $totalFees        = Fee::where('student_id', $student->id)->where('school_year', $sy)->where('status', 'active')->sum('amount');
                $totalPaid        = Payment::where('student_id', $student->id)->where('school_year', $sy)->where('status', 'completed')->sum('amount');
                $student->balance = max(0, $totalFees - $totalPaid);
                $preselectedStudent = $student;
            }
        }

        return view('cashier.receive-payment', array_merge([
            'preselectedStudent' => $preselectedStudent,
        ], ['nav' => $this->nav()]));
    }

    /**
     * Store a new payment.
     */
    public function storePayment(Request $request)
    {
        $validated = $request->validate([
            'student_id'       => 'required|exists:users,id',
            'amount'           => 'required|numeric|min:0.01',
            'payment_date'     => 'required|date',
            'payment_method'   => 'required|string|in:Cash,GCash,PayMaya,Bank Transfer,Check',
            'semester'         => 'required|in:1,2,summer',
            'school_year'      => 'required|string',
            'or_number'        => 'nullable|string|max:50',
            'reference_number' => 'nullable|string|max:100',
            'notes'            => 'nullable|string|max:500',
        ]);

        $student = User::find($validated['student_id']);
        abort_unless($student && $student->role === 'student', 422, 'Invalid student.');

        $payment = Payment::create([
            'student_id'       => $validated['student_id'],
            'cashier_id'       => Auth::id(),
            'amount'           => $validated['amount'],
            'payment_date'     => $validated['payment_date'],
            'payment_method'   => $validated['payment_method'],
            'semester'         => $validated['semester'],
            'school_year'      => $validated['school_year'],
            'or_number'        => $validated['or_number'] ?? null,
            'reference_number' => $validated['reference_number'] ?? null,
            'notes'            => $validated['notes'] ?? null,
            'status'           => 'completed',
        ]);

        $this->applyPaymentToFees($payment);

        return redirect()
            ->route('cashier.receipt', $payment->id)
            ->with('success', 'Payment of ₱' . number_format($payment->amount, 2) . ' recorded successfully.');
    }

    /**
     * Allocate a payment against outstanding student fees (oldest first).
     */
    private function applyPaymentToFees(Payment $payment): void
    {
        $remaining = $payment->amount;

        $fees = Fee::where('student_id', $payment->student_id)
            ->where('school_year', $payment->school_year)
            ->where('status', 'active')
            ->get();

        foreach ($fees as $fee) {
            if ($remaining <= 0) break;

            $sf = StudentFee::firstOrCreate(
                ['student_id' => $payment->student_id, 'fee_id' => $fee->id],
                ['amount_paid' => 0, 'is_fully_paid' => false]
            );

            $owed    = $fee->amount - $sf->amount_paid;
            $toApply = min($remaining, $owed);

            if ($toApply > 0) {
                $sf->amount_paid   += $toApply;
                $sf->is_fully_paid  = $sf->amount_paid >= $fee->amount;
                $sf->save();
                $remaining -= $toApply;
            }
        }
    }

    /**
     * Transactions list — filterable by method, status, date range.
     */
    public function transactions(Request $request)
    {
        $query = Payment::with('student')->latest('payment_date');

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($builder) use ($q) {
                $builder->whereHas('student', fn($sq) => $sq->where('name', 'like', "%{$q}%")->orWhere('student_id', 'like', "%{$q}%"))
                    ->orWhere('or_number', 'like', "%{$q}%")
                    ->orWhere('reference_number', 'like', "%{$q}%");
            });
        }

        if ($request->filled('method')) {
            $query->where('payment_method', $request->method);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->where('payment_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('payment_date', '<=', $request->date_to);
        }

        $allFiltered = (clone $query)->where('status', 'completed')->sum('amount');
        $payments    = $query->paginate(20);

        return view('cashier.transactions', array_merge([
            'payments'      => $payments,
            'filteredTotal' => $allFiltered,
        ], ['nav' => $this->nav()]));
    }

    /**
     * Show a single payment as a printable receipt.
     */
    public function receipt(Payment $payment)
    {
        $payment->load('student', 'cashier');

        return view('cashier.receipt', array_merge([
            'payment' => $payment,
        ], ['nav' => $this->nav()]));
    }

    /**
     * Mark a pending payment as completed.
     */
    public function completePayment(Payment $payment)
    {
        $payment->update(['status' => 'completed']);
        $this->applyPaymentToFees($payment);

        return back()->with('success', 'Payment marked as completed.');
    }

    // ═══════════════════════════════════════════════════════════════
    // PROFILE
    // ═══════════════════════════════════════════════════════════════

    /**
     * Show the cashier's own profile page.
     */
    public function profile()
    {
        return view('cashier.profile', ['nav' => $this->nav()]);
    }

    /**
     * Update personal info, contact details, and address.
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name'            => ['required', 'string', 'max:100'],
            'middle_name'     => ['nullable', 'string', 'max:100'],
            'last_name'       => ['nullable', 'string', 'max:100'],
            'suffix'          => ['nullable', 'string', 'max:20'],
            'birth_date'      => ['nullable', 'date'],
            'gender'          => ['nullable', 'in:Male,Female'],
            'position'        => ['nullable', 'string', 'max:100'],
            'employee_id'     => ['nullable', 'string', 'max:50'],
            'email'           => ['required', 'email', 'unique:users,email,' . $user->id],
            'phone'           => ['nullable', 'string', 'max:20'],
            'alternate_phone' => ['nullable', 'string', 'max:20'],
            'street'          => ['nullable', 'string', 'max:255'],
            'barangay'        => ['nullable', 'string', 'max:100'],
            'municipality'    => ['nullable', 'string', 'max:100'],
            'city'            => ['nullable', 'string', 'max:100'],
        ]);

        $user->update($validated);

        return redirect()->route('cashier.profile')
            ->with('success', 'Profile updated successfully.');
    }

    /**
     * Change password.
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required'],
            'password'         => ['required', 'confirmed', Password::min(8)],
        ]);

        if (! Hash::check($request->current_password, Auth::user()->password)) {
            return back()->withErrors(['current_password' => 'The current password is incorrect.']);
        }

        Auth::user()->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('cashier.profile')
            ->with('success', 'Password changed successfully.');
    }

    /**
     * Update profile photo.
     */
    public function updatePhoto(Request $request)
    {
        $request->validate([
            'profile_picture' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $user = Auth::user();

        if ($user->profile_picture && Storage::disk('public')->exists($user->profile_picture)) {
            Storage::disk('public')->delete($user->profile_picture);
        }

        $path = $request->file('profile_picture')->store('profile-pictures', 'public');
        $user->update(['profile_picture' => $path]);

        return redirect()->route('cashier.profile')
            ->with('success', 'Profile photo updated.');
    }
}