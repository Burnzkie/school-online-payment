<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        // Authentication & Role
        'email',
        'password',
        'role',

        // Personal Information
        'name',
        'middle_name',
        'last_name',
        'suffix',
        'birth_date',
        'age',
        'gender',
        'nationality',
        'phone',

        // Student Identification
        'student_id',

        // Enrollment Information
        'level_group',
        'year_level',
        'strand',
        'department',
        'program',

        // Address Information
        'street',
        'barangay',
        'municipality',
        'city',

        // Parent Information on student records
        'father_name',
        'father_occupation',
        'father_contact',
        'mother_name',
        'mother_occupation',
        'mother_contact',
        'guardian_name',
        'guardian_relationship',
        'guardian_contact',

        // Extra info for non-student roles
        'extra_info',
        'profile_picture',

        // Student drop / status fields
        'status',           // 'active' | 'dropped'
        'drop_reason',
        'drop_notes',
        'dropped_at',
        'dropped_by_name',
        'dark_mode',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'birth_date'        => 'date',
            'age'               => 'integer',
            'dropped_at'        => 'datetime',
            'dark_mode'         => 'boolean',
        ];
    }

    // =========================================================================
    // Relationships
    // =========================================================================

    /** Fees assessed to this student. */
    public function fees(): HasMany
    {
        return $this->hasMany(Fee::class, 'student_id');
    }

    /** Payments made by / for this student. */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'student_id');
    }

    /**
     * Alias for payments() — used by the Treasurer portal filters.
     * Keeps controller queries readable: $user->paymentsAs()->...
     */
    public function paymentsAs(): HasMany
    {
        return $this->hasMany(Payment::class, 'student_id');
    }

    /** Installment plans for this student. */
    public function installmentPlans(): HasMany
    {
        return $this->hasMany(InstallmentPlan::class, 'student_id');
    }

    /** Payments processed by this cashier. */
    public function processedPayments(): HasMany
    {
        return $this->hasMany(Payment::class, 'cashier_id');
    }

    /** Fees through the student_fees pivot. */
    public function assignedFees(): BelongsToMany
    {
        return $this->belongsToMany(Fee::class, 'student_fees')
            ->withPivot('amount_paid', 'is_fully_paid', 'due_date')
            ->withTimestamps();
    }

    /** Notifications for this user. */
    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class, 'user_id');
    }

    /**
     * Students linked to this parent account.
     * Reads from the parent_student pivot table.
     * Use this on a User where role = 'parent'.
     */
    public function linkedStudents(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'parent_student',
            'parent_id',
            'student_id'
        )->withPivot('link_method', 'linked_by')->withTimestamps();
    }

    /**
     * Parents linked to this student account.
     * Use this on a User where role = 'student'.
     */
    public function linkedParents(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'parent_student',
            'student_id',
            'parent_id'
        )->withPivot('link_method', 'linked_by')->withTimestamps();
    }

    // =========================================================================
    // Helper Methods
    // =========================================================================

    public function getFullNameAttribute(): string
    {
        return implode(' ', array_filter([
            $this->name,
            $this->middle_name,
            $this->last_name,
            $this->suffix,
        ]));
    }

    public function getTotalFeesForSemester(string $schoolYear, string $semester): float
    {
        return $this->fees()
            ->where('school_year', $schoolYear)
            ->where('semester', $semester)
            ->where('status', 'active')
            ->sum('amount');
    }

    public function getTotalPaymentsForSemester(string $schoolYear, string $semester): float
    {
        return $this->payments()
            ->where('school_year', $schoolYear)
            ->where('semester', $semester)
            ->where('status', 'completed')
            ->sum('amount');
    }

    public function getBalanceForSemester(string $schoolYear, string $semester): float
    {
        return max(0,
            $this->getTotalFeesForSemester($schoolYear, $semester) -
            $this->getTotalPaymentsForSemester($schoolYear, $semester)
        );
    }

    public function isStudent():   bool { return $this->role === 'student';   }
    public function isParent():    bool { return $this->role === 'parent';    }
    public function isTreasurer(): bool { return $this->role === 'treasurer'; }
    public function isCashier():   bool { return $this->role === 'cashier';   }
}