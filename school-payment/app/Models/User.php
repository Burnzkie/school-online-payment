<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
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

        // Parent / Guardian contacts (stored on student records)
        'father_name',
        'father_occupation',
        'father_contact',
        'mother_name',
        'mother_occupation',
        'mother_contact',
        'guardian_name',
        'guardian_relationship',
        'guardian_contact',

        // Miscellaneous
        'extra_info',
        'profile_picture',
        'dark_mode',

        // Student status / drop fields
        'status',            // 'active' | 'dropped'
        'drop_reason',
        'drop_notes',
        'dropped_at',
        'dropped_by_name',
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
            'dropped_at'        => 'datetime',
            'age'               => 'integer',
            'dark_mode'         => 'boolean',
        ];
    }

    // =========================================================================
    // Query Scopes
    // =========================================================================

    /** User::students()->get() */
    public function scopeStudents(Builder $query): Builder
    {
        return $query->where('role', 'student');
    }

    /** User::parents()->get() */
    public function scopeParents(Builder $query): Builder
    {
        return $query->where('role', 'parent');
    }

    /** User::treasurers()->get() */
    public function scopeTreasurers(Builder $query): Builder
    {
        return $query->where('role', 'treasurer');
    }

    /** User::cashiers()->get() */
    public function scopeCashiers(Builder $query): Builder
    {
        return $query->where('role', 'cashier');
    }

    /** User::students()->active()->get() */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    /** User::students()->dropped()->get() */
    public function scopeDropped(Builder $query): Builder
    {
        return $query->where('status', 'dropped');
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

    /** Payments recorded/processed by this cashier. */
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

    /**
     * Students linked to this parent account.
     * Use on a User where role = 'parent'.
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
     * Use on a User where role = 'student'.
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
    // Accessors
    // =========================================================================

    /** Full name with middle name, last name, and suffix. */
    public function getFullNameAttribute(): string
    {
        return implode(' ', array_filter([
            $this->name,
            $this->middle_name,
            $this->last_name,
            $this->suffix,
        ]));
    }

    // =========================================================================
    // Financial Helpers
    // =========================================================================

    public function getTotalFeesForSemester(string $schoolYear, string $semester): float
    {
        return (float) $this->fees()
            ->where('school_year', $schoolYear)
            ->where('semester', $semester)
            ->where('status', 'active')
            ->sum('amount');
    }

    public function getTotalPaymentsForSemester(string $schoolYear, string $semester): float
    {
        return (float) $this->payments()
            ->where('school_year', $schoolYear)
            ->where('semester', $semester)
            ->where('status', 'completed')
            ->sum('amount');
    }

    public function getBalanceForSemester(string $schoolYear, string $semester): float
    {
        return max(0.0,
            $this->getTotalFeesForSemester($schoolYear, $semester) -
            $this->getTotalPaymentsForSemester($schoolYear, $semester)
        );
    }

    // =========================================================================
    // Role & Status Helpers
    // =========================================================================

    public function isStudent():   bool { return $this->role === 'student';   }
    public function isParent():    bool { return $this->role === 'parent';    }
    public function isTreasurer(): bool { return $this->role === 'treasurer'; }
    public function isCashier():   bool { return $this->role === 'cashier';   }

    public function isVerified(): bool { return $this->email_verified_at !== null; }
    public function isDropped():  bool { return $this->status === 'dropped';       }
    public function isActive():   bool { return $this->status !== 'dropped';       }
}