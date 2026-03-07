<?php

// app/Models/InstallmentSchedule.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InstallmentSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'installment_plan_id',
        'student_id',
        'installment_number',
        'amount_due',
        'due_date',
        'amount_paid',
        'is_paid',
        'paid_at',
        'payment_id',
        'is_overdue',
    ];

    protected $casts = [
        'amount_due'  => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'is_paid'     => 'boolean',
        'is_overdue'  => 'boolean',
        'due_date'    => 'date',
        'paid_at'     => 'date',
    ];

    // ── Relationships ────────────────────────────────────────────────────────

    public function plan(): BelongsTo
    {
        return $this->belongsTo(InstallmentPlan::class, 'installment_plan_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    // ── Helpers ──────────────────────────────────────────────────────────────

    /**
     * Mark this installment as paid.
     * Optionally link to a Payment record.
     */
    public function markPaid(float $amount, ?int $paymentId = null): void
    {
        $this->update([
            'amount_paid' => $amount,
            'is_paid'     => true,
            'paid_at'     => now()->toDateString(),
            'payment_id'  => $paymentId,
            'is_overdue'  => false,
        ]);

        // Auto-complete the parent plan when all schedules are paid
        $plan = $this->plan()->with('schedules')->first();
        if ($plan && $plan->isComplete()) {
            $plan->update(['status' => 'completed']);
        }
    }

    /**
     * Check and set the overdue flag based on today's date.
     * Call this from a scheduled Artisan command (e.g. daily).
     */
    public function checkOverdue(): void
    {
        if (! $this->is_paid && $this->due_date->isPast()) {
            $this->update(['is_overdue' => true]);
        }
    }

    // ── Scopes ───────────────────────────────────────────────────────────────

    /**
     * Filter schedules for a specific student.
     * Used by the HS dashboard to find the next upcoming due date.
     */
    public function scopeForStudent($query, int $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    /**
     * Filter to only unpaid schedules.
     */
    public function scopeUnpaid($query)
    {
        return $query->where('is_paid', false);
    }

    /**
     * Filter to only overdue schedules.
     */
    public function scopeOverdue($query)
    {
        return $query->where('is_overdue', true)->where('is_paid', false);
    }
}