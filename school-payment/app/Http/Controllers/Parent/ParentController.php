<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Fee;
use App\Models\Payment;
use App\Models\InstallmentPlan;
use App\Models\InstallmentSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ParentController extends Controller
{
    // =========================================================================
    // Core: Get linked students from the pivot table
    // =========================================================================

    /**
     * Returns all students linked to the authenticated parent
     * via the parent_student pivot table.
     *
     * Also runs a "catch-up" phone scan: if a student was added to the system
     * AFTER the parent registered, they won't have been auto-linked at
     * registration time. This corrects that silently on each dashboard load
     * and then persists the new links so it only pays the cost once.
     */
    private function getLinkedStudents()
    {
        $parent = auth()->user();

        // ── Catch-up: find any new students not yet in the pivot ──────────────
        if ($parent->phone) {
            $this->catchUpMissingLinks($parent);
        }

        // ── Read from pivot (fast, indexed, single join) ──────────────────────
        return $parent->linkedStudents()->get();
    }

    /**
     * Scans for any students whose contact fields match the parent's phone
     * but are NOT yet in the parent_student pivot, and inserts them.
     *
     * Runs on every dashboard load but is cheap: it only does a DB insert
     * when it actually finds something new, which after initial linking is
     * almost never.
     */
    private function catchUpMissingLinks(User $parent): void
    {
        $phone = $parent->phone;

        // Students that match by phone
        $phoneMatched = User::where('role', 'student')
            ->where(function ($q) use ($phone) {
                $q->where('father_contact',    $phone)
                  ->orWhere('mother_contact',  $phone)
                  ->orWhere('guardian_contact', $phone);
            })
            ->pluck('id');

        // Students already in the pivot for this parent
        $alreadyLinked = DB::table('parent_student')
            ->where('parent_id', $parent->id)
            ->pluck('student_id');

        // New students not yet linked
        $newStudentIds = $phoneMatched->diff($alreadyLinked);

        if ($newStudentIds->isEmpty()) {
            return;
        }

        $rows = [];
        $now  = now();

        foreach ($newStudentIds as $studentId) {
            $rows[] = [
                'parent_id'   => $parent->id,
                'student_id'  => $studentId,
                'link_method' => 'auto_phone',
                'linked_by'   => null,
                'created_at'  => $now,
                'updated_at'  => $now,
            ];
            Log::info("CatchUpLink: Linked parent #{$parent->id} → student #{$studentId}.");
        }

        DB::table('parent_student')->insert($rows);
    }

    // =========================================================================
    // Dashboard
    // =========================================================================

    public function dashboard()
    {
        $parent   = auth()->user();
        $students = $this->getLinkedStudents();

        $totalBalance     = 0;
        $totalPaid        = 0;
        $overdueCount     = 0;
        $upcomingDue      = null;
        $studentSummaries = [];

        foreach ($students as $student) {
            $year     = $this->currentSchoolYear();
            $semester = $this->currentSemester();

            $fees = Fee::where('student_id', $student->id)
                ->where('school_year', $year)
                ->where('semester', $semester)
                ->where('status', 'active')
                ->sum('amount');

            $paid = Payment::where('student_id', $student->id)
                ->where('school_year', $year)
                ->where('semester', $semester)
                ->where('status', 'completed')
                ->sum('amount');

            $balance       = max(0, $fees - $paid);
            $totalBalance += $balance;
            $totalPaid    += $paid;

            $overdue = InstallmentSchedule::where('student_id', $student->id)
                ->where('is_paid', false)
                ->where('due_date', '<', now()->toDateString())
                ->count();
            $overdueCount += $overdue;

            $nextDue = InstallmentSchedule::where('student_id', $student->id)
                ->where('is_paid', false)
                ->where('due_date', '>=', now()->toDateString())
                ->orderBy('due_date')
                ->first();

            if ($nextDue && (!$upcomingDue || $nextDue->due_date < $upcomingDue->due_date)) {
                $upcomingDue = $nextDue;
            }

            $recentPayments = Payment::where('student_id', $student->id)
                ->where('status', 'completed')
                ->orderByDesc('payment_date')
                ->take(3)
                ->get();

            $studentSummaries[] = [
                'student'         => $student,
                'balance'         => $balance,
                'paid'            => $paid,
                'total_fees'      => $fees,
                'overdue'         => $overdue,
                'next_due'        => $nextDue,
                'recent_payments' => $recentPayments,
                'progress'        => $fees > 0 ? round(($paid / $fees) * 100) : 0,
            ];
        }

        return view('parent.dashboard', compact(
            'parent', 'students', 'studentSummaries',
            'totalBalance', 'totalPaid', 'overdueCount', 'upcomingDue'
        ));
    }

    // =========================================================================
    // Student Detail (Billing)
    // =========================================================================

    public function studentDetail(User $student)
    {
        $this->authorizeStudent($student);

        $schoolYear = request('school_year', $this->currentSchoolYear());
        $semester   = request('semester',    $this->currentSemester());

        $fees = Fee::where('student_id', $student->id)
            ->where('school_year', $schoolYear)
            ->where('semester', $semester)
            ->where('status', 'active')
            ->get();

        $payments = Payment::where('student_id', $student->id)
            ->where('school_year', $schoolYear)
            ->where('semester', $semester)
            ->where('status', 'completed')
            ->orderByDesc('payment_date')
            ->get();

        $totalFees = $fees->sum('amount');
        $totalPaid = $payments->sum('amount');
        $balance   = max(0, $totalFees - $totalPaid);
        $progress  = $totalFees > 0 ? round(($totalPaid / $totalFees) * 100) : 0;

        $installmentPlan = InstallmentPlan::where('student_id', $student->id)
            ->where('school_year', $schoolYear)
            ->where('semester', $semester)
            ->with('schedules')
            ->first();

        $availableYears = Fee::where('student_id', $student->id)
            ->select('school_year')
            ->distinct()
            ->orderByDesc('school_year')
            ->pluck('school_year');

        return view('parent.student-detail', compact(
            'student', 'fees', 'payments',
            'totalFees', 'totalPaid', 'balance', 'progress',
            'installmentPlan', 'schoolYear', 'semester', 'availableYears'
        ));
    }

    // =========================================================================
    // Payment History
    // =========================================================================

    public function paymentHistory(User $student)
    {
        $this->authorizeStudent($student);

        $payments = Payment::where('student_id', $student->id)
            ->where('status', 'completed')
            ->orderByDesc('payment_date')
            ->paginate(15);

        return view('parent.payment-history', compact('student', 'payments'));
    }

    // =========================================================================
    // Statement of Account
    // =========================================================================

    public function statement(User $student)
    {
        $this->authorizeStudent($student);

        $schoolYear = request('school_year', $this->currentSchoolYear());
        $semester   = request('semester',    $this->currentSemester());

        $fees = Fee::where('student_id', $student->id)
            ->where('school_year', $schoolYear)
            ->where('semester', $semester)
            ->get();

        $payments = Payment::where('student_id', $student->id)
            ->where('school_year', $schoolYear)
            ->where('semester', $semester)
            ->where('status', 'completed')
            ->orderBy('payment_date')
            ->get();

        $totalFees = $fees->sum('amount');
        $totalPaid = $payments->sum('amount');
        $balance   = max(0, $totalFees - $totalPaid);

        $installmentPlan = InstallmentPlan::where('student_id', $student->id)
            ->where('school_year', $schoolYear)
            ->where('semester', $semester)
            ->with('schedules')
            ->first();

        $availableYears = Fee::where('student_id', $student->id)
            ->select('school_year')
            ->distinct()
            ->orderByDesc('school_year')
            ->pluck('school_year');

        return view('parent.statement', compact(
            'student', 'fees', 'payments',
            'totalFees', 'totalPaid', 'balance',
            'schoolYear', 'semester', 'availableYears',
            'installmentPlan'
        ));
    }

    // =========================================================================
    // Notifications
    // =========================================================================

    public function notifications()
    {
        $notifications = auth()->user()
            ->notifications()
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('parent.notifications', compact('notifications'));
    }

    public function markNotificationRead($id)
    {
        auth()->user()->notifications()->findOrFail($id)->update(['is_read' => true]);
        return response()->json(['ok' => true]);
    }

    public function markAllRead()
    {
        auth()->user()->notifications()->where('is_read', false)->update(['is_read' => true]);
        return back()->with('success', 'All notifications marked as read.');
    }

    // =========================================================================
    // Profile
    // =========================================================================

    public function profile()
    {
        return view('parent.profile', ['parent' => auth()->user()]);
    }

    public function editProfile()
    {
        return view('parent.profile-edit', ['parent' => auth()->user()]);
    }

    public function updateProfile(Request $request)
    {
        $parent = auth()->user();

        $validated = $request->validate([
            'name'         => 'required|string|max:255',
            'middle_name'  => 'nullable|string|max:100',
            'last_name'    => 'nullable|string|max:100',
            'phone'        => 'nullable|string|max:30',
            'birth_date'   => 'nullable|date',
            'gender'       => 'nullable|in:MALE,FEMALE',
            'nationality'  => 'nullable|string|max:100',
            'street'       => 'nullable|string|max:255',
            'barangay'     => 'nullable|string|max:255',
            'municipality' => 'nullable|string|max:255',
            'city'         => 'nullable|string|max:255',
            'extra_info'   => 'nullable|string|max:255',
        ]);

        $oldPhone = $parent->phone;
        $parent->update($validated);

        // If the parent changed their phone number, re-run the catch-up link
        // so any students matching the new number get linked automatically.
        if ($oldPhone !== $parent->fresh()->phone && $parent->phone) {
            $this->catchUpMissingLinks($parent->fresh());
        }

        return redirect()->route('parent.profile')->with('success', 'Profile updated successfully.');
    }

    public function updateProfilePhoto(Request $request)
    {
        $request->validate(['photo' => 'required|image|mimes:jpg,jpeg,png,webp|max:5120']);

        $path = $request->file('photo')->store('profile-photos', 'public');
        auth()->user()->update(['profile_picture' => $path]);

        return back()->with('success', 'Profile photo updated.');
    }

    // =========================================================================
    // Student search API (used by the register page live search)
    // =========================================================================

    /**
     * GET /api/students/search?q=...
     * Returns a JSON list of students matching the name query.
     * Only returns fields safe to expose during registration (no billing data).
     */
    public function searchStudents(Request $request)
    {
        $q = trim($request->get('q', ''));

        if (strlen($q) < 2) {
            return response()->json([]);
        }

        $students = User::where('role', 'student')
            ->where(function ($query) use ($q) {
                $query->where('name',      'like', "%{$q}%")
                      ->orWhere('last_name', 'like', "%{$q}%")
                      ->orWhere('middle_name', 'like', "%{$q}%");
            })
            ->select('id', 'name', 'middle_name', 'last_name', 'year_level', 'level_group', 'student_id')
            ->orderBy('last_name')
            ->orderBy('name')
            ->limit(10)
            ->get();

        return response()->json($students);
    }

    // =========================================================================
    // Helpers
    // =========================================================================

    /**
     * Abort with 403 if the given student is not in this parent's linked list.
     * Reads from the pivot — no live phone scan.
     */
    private function authorizeStudent(User $student): void
    {
        $isLinked = DB::table('parent_student')
            ->where('parent_id',  auth()->id())
            ->where('student_id', $student->id)
            ->exists();

        abort_unless($isLinked, 403, 'You are not authorized to view this student.');
    }

    private function currentSchoolYear(): string
    {
        $year  = now()->year;
        $month = now()->month;
        return $month >= 6
            ? "{$year}-" . ($year + 1)
            : ($year - 1) . "-{$year}";
    }

    private function currentSemester(): string
    {
        $month = now()->month;
        if ($month >= 6 && $month <= 10) return '1';
        if ($month >= 11 || $month <= 3) return '2';
        return 'summer';
    }
}