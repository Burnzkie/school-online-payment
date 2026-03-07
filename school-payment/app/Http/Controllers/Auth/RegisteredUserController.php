<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            // Core
            'name'                  => ['required', 'string', 'max:255'],
            'email'                 => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users'],
            'password'              => ['required', 'confirmed', Rules\Password::defaults()],
            'role'                  => ['required', 'in:student,parent,treasurer,cashier'],

            // Personal
            'middle_name'           => ['nullable', 'string', 'max:100'],
            'last_name'             => ['nullable', 'string', 'max:100'],
            'suffix'                => ['nullable', 'string', 'max:20'],
            'birth_date'            => ['nullable', 'date'],
            'age'                   => ['nullable', 'integer', 'min:3', 'max:120'],
            'gender'                => ['nullable', 'in:MALE,FEMALE'],
            'nationality'           => ['nullable', 'string', 'max:100'],
            'phone'                 => ['nullable', 'string', 'max:30'],

            // Student identification
            'student_id'            => ['nullable', 'string', 'max:50', 'unique:users'],

            // Enrollment
            'level_group'           => ['nullable', 'string', 'max:50'],
            'year_level'            => ['nullable', 'string', 'max:100'],
            'strand'                => ['nullable', 'string', 'max:50'],
            'department'            => ['nullable', 'string', 'max:150'],
            'program'               => ['nullable', 'string', 'max:150'],

            // Address
            'street'                => ['nullable', 'string', 'max:255'],
            'barangay'              => ['nullable', 'string', 'max:255'],
            'municipality'          => ['nullable', 'string', 'max:255'],
            'city'                  => ['nullable', 'string', 'max:255'],

            // Student's parent/guardian contacts (filled during student registration)
            'father_name'           => ['nullable', 'string', 'max:255'],
            'father_occupation'     => ['nullable', 'string', 'max:255'],
            'father_contact'        => ['nullable', 'string', 'max:30'],
            'mother_name'           => ['nullable', 'string', 'max:255'],
            'mother_occupation'     => ['nullable', 'string', 'max:255'],
            'mother_contact'        => ['nullable', 'string', 'max:30'],
            'guardian_name'         => ['nullable', 'string', 'max:255'],
            'guardian_relationship' => ['nullable', 'string', 'max:100'],
            'guardian_contact'      => ['nullable', 'string', 'max:30'],

            // Non-student roles
            'extra_info'            => ['nullable', 'string', 'max:255'],
        ]);

        // ── Create the user ───────────────────────────────────────────────────
        $user = User::create([
            'email'                 => $validated['email'],
            'password'              => Hash::make($validated['password']),
            'role'                  => $validated['role'],
            'name'                  => $validated['name'],
            'middle_name'           => $validated['middle_name']           ?? null,
            'last_name'             => $validated['last_name']             ?? null,
            'suffix'                => $validated['suffix']                ?? null,
            'birth_date'            => $validated['birth_date']            ?? null,
            'age'                   => $validated['age']                   ?? null,
            'gender'                => $validated['gender']                ?? null,
            'nationality'           => $validated['nationality']           ?? 'Filipino',
            'phone'                 => $validated['phone']                 ?? null,
            'student_id'            => $validated['student_id']            ?? null,
            'level_group'           => $validated['level_group']           ?? null,
            'year_level'            => $validated['year_level']            ?? null,
            'strand'                => $validated['strand']                ?? null,
            'department'            => $validated['department']            ?? null,
            'program'               => $validated['program']               ?? null,
            'street'                => $validated['street']                ?? null,
            'barangay'              => $validated['barangay']              ?? null,
            'municipality'          => $validated['municipality']          ?? null,
            'city'                  => $validated['city']                  ?? null,
            'father_name'           => $validated['father_name']           ?? null,
            'father_occupation'     => $validated['father_occupation']     ?? null,
            'father_contact'        => $validated['father_contact']        ?? null,
            'mother_name'           => $validated['mother_name']           ?? null,
            'mother_occupation'     => $validated['mother_occupation']     ?? null,
            'mother_contact'        => $validated['mother_contact']        ?? null,
            'guardian_name'         => $validated['guardian_name']         ?? null,
            'guardian_relationship' => $validated['guardian_relationship'] ?? null,
            'guardian_contact'      => $validated['guardian_contact']      ?? null,
            'extra_info'            => $validated['extra_info']            ?? null,
        ]);

        event(new Registered($user));

        // ── Auto-link logic ───────────────────────────────────────────────────
        if ($user->role === 'parent') {
            $this->autoLinkParentToStudents($user);
        }

        if ($user->role === 'student') {
            $this->autoLinkStudentToParents($user);
        }
        // ─────────────────────────────────────────────────────────────────────

        Auth::logout();

        return redirect()->route('register')
            ->with('registration_success', true)
            ->with('status', '✅ Registration successful! Please login below.');
    }

    // =========================================================================
    // Auto-link: Parent registers → find their children
    // =========================================================================

    /**
     * When a PARENT registers, scan all student records and link any student
     * whose father_contact / mother_contact / guardian_contact matches
     * the parent's registered phone number.
     *
     * This covers ALL cases:
     *   - Father registers   → matched via father_contact
     *   - Mother registers   → matched via mother_contact
     *   - Guardian registers → matched via guardian_contact
     *   - Different surname  → doesn't matter, phone is the key
     *   - No parent, only guardian → still matched via guardian_contact
     */
    private function autoLinkParentToStudents(User $parent): void
    {
        // Nothing to match if the parent has no phone number
        if (empty($parent->phone)) {
            Log::info("ParentAutoLink: Parent #{$parent->id} has no phone — skipping auto-link.");
            return;
        }

        $phone = $parent->phone;

        // Find every student whose contact fields contain this phone number
        $matchedStudents = User::where('role', 'student')
            ->where(function ($q) use ($phone) {
                $q->where('father_contact',   $phone)
                  ->orWhere('mother_contact',  $phone)
                  ->orWhere('guardian_contact', $phone);
            })
            ->get();

        if ($matchedStudents->isEmpty()) {
            Log::info("ParentAutoLink: No students matched phone {$phone} for parent #{$parent->id}.");
            return;
        }

        $rows = [];
        $now  = now();

        foreach ($matchedStudents as $student) {
            // Skip if already linked (prevents duplicate key errors)
            $alreadyLinked = DB::table('parent_student')
                ->where('parent_id',  $parent->id)
                ->where('student_id', $student->id)
                ->exists();

            if ($alreadyLinked) {
                continue;
            }

            $rows[] = [
                'parent_id'   => $parent->id,
                'student_id'  => $student->id,
                'link_method' => 'auto_phone',
                'linked_by'   => null, // system-generated
                'created_at'  => $now,
                'updated_at'  => $now,
            ];

            Log::info("ParentAutoLink: Linked parent #{$parent->id} → student #{$student->id} via phone match.");
        }

        if (!empty($rows)) {
            DB::table('parent_student')->insert($rows);
        }
    }

    // =========================================================================
    // Auto-link: Student registers → find their parents already in the system
    // =========================================================================

    /**
     * When a STUDENT registers, they fill in father/mother/guardian contact
     * numbers. We scan existing parent accounts and link any whose phone
     * matches one of those contact fields.
     *
     * This handles the case where the parent registered FIRST, then the student.
     * Without this, a phone-match on the parent's side already covered it, but
     * if the student registers first, the parent's future registration will
     * trigger autoLinkParentToStudents() above — so both directions are covered.
     *
     * This method also retroactively links parents already in the system when
     * a NEW student is added.
     */
    private function autoLinkStudentToParents(User $student): void
    {
        // Collect all non-null contact numbers from the student's record
        $contacts = array_filter([
            $student->father_contact,
            $student->mother_contact,
            $student->guardian_contact,
        ]);

        if (empty($contacts)) {
            Log::info("StudentAutoLink: Student #{$student->id} has no parent contacts — skipping auto-link.");
            return;
        }

        // Find parent accounts whose phone matches any of these contacts
        $matchedParents = User::where('role', 'parent')
            ->whereIn('phone', $contacts)
            ->get();

        if ($matchedParents->isEmpty()) {
            Log::info("StudentAutoLink: No parent accounts matched contacts for student #{$student->id}.");
            return;
        }

        $rows = [];
        $now  = now();

        foreach ($matchedParents as $parent) {
            $alreadyLinked = DB::table('parent_student')
                ->where('parent_id',  $parent->id)
                ->where('student_id', $student->id)
                ->exists();

            if ($alreadyLinked) {
                continue;
            }

            $rows[] = [
                'parent_id'   => $parent->id,
                'student_id'  => $student->id,
                'link_method' => 'auto_phone',
                'linked_by'   => null,
                'created_at'  => $now,
                'updated_at'  => $now,
            ];

            Log::info("StudentAutoLink: Linked student #{$student->id} → parent #{$parent->id} via phone match.");
        }

        if (!empty($rows)) {
            DB::table('parent_student')->insert($rows);
        }
    }
}