<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Fee extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'student_id',
        'school_year',
        'semester',
        'fee_name',
        'amount',
        'description',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'amount' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the student that owns this fee.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    /**
     * Students associated with this fee (many-to-many).
     * Useful if you have standard fees assigned to multiple students.
     */
    public function students(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'student_fees')
            ->withPivot('amount_paid', 'is_fully_paid', 'due_date')
            ->withTimestamps();
    }

    /**
     * Scope to filter fees by school year.
     */
    public function scopeForSchoolYear($query, string $schoolYear)
    {
        return $query->where('school_year', $schoolYear);
    }

    /**
     * Scope to filter fees by semester.
     */
    public function scopeForSemester($query, string $semester)
    {
        return $query->where('semester', $semester);
    }

    /**
     * Scope to get active fees only.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Get the formatted semester name.
     */
    public function getSemesterNameAttribute(): string
    {
        return match($this->semester) {
            '1' => '1st Semester',
            '2' => '2nd Semester',
            'summer' => 'Summer',
            default => $this->semester,
        };
    }
}