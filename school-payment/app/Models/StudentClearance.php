<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

class StudentClearance extends Model
{
    protected $table = 'student_clearances';

    protected $fillable = [
        'student_id',
        'school_year',
        'semester',
        'is_cleared',
        'hold_reason',
        'manual_override',
        'override_note',
        'cleared_by',
        'cleared_at',
    ];

    protected $casts = [
        'is_cleared'      => 'boolean',
        'manual_override' => 'boolean',
        'cleared_at'      => 'datetime',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function clearedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cleared_by');
    }

    /**
     * Auto-sync clearance from current balance.
     * Call after every payment or fee change.
     */
    public static function syncForStudent(
        int $studentId,
        string $schoolYear,
        string $semester,
        float $balance
    ): self {
        $clearance = self::firstOrNew([
            'student_id'  => $studentId,
            'school_year' => $schoolYear,
            'semester'    => $semester,
        ]);

        // Never overwrite a manual override
        if (! $clearance->manual_override) {
            $cleared = $balance <= 0;
            $clearance->is_cleared   = $cleared;
            $clearance->hold_reason  = $cleared ? null : "Outstanding balance of ₱".number_format($balance, 2);
            $clearance->cleared_by   = $cleared ? Auth::id() : null;
            $clearance->cleared_at   = $cleared ? now() : null;
            $clearance->save();
        }

        return $clearance;
    }

    /**
     * Manually clear a student (override).
     */
    public function manualClear(string $note): void
    {
        $this->update([
            'is_cleared'      => true,
            'manual_override' => true,
            'override_note'   => $note,
            'cleared_by'      => Auth::id(),
            'cleared_at'      => now(),
            'hold_reason'     => null,
        ]);
    }

    /**
     * Put a student on hold (manual).
     */
    public function manualHold(string $reason): void
    {
        $this->update([
            'is_cleared'      => false,
            'manual_override' => true,
            'hold_reason'     => $reason,
            'override_note'   => null,
            'cleared_by'      => null,
            'cleared_at'      => null,
        ]);
    }

    public function scopeOnHold($query)
    {
        return $query->where('is_cleared', false);
    }

    public function scopeCleared($query)
    {
        return $query->where('is_cleared', true);
    }

    public function scopeForSemester($query, string $schoolYear, string $semester)
    {
        return $query->where('school_year', $schoolYear)->where('semester', $semester);
    }
}