<?php

// app/Models/InstallmentPlan.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InstallmentPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'school_year',
        'semester',
        'plan_type',
        'total_installments',
        'total_amount',
        'amount_per_installment',
        'status',
        'confirmed_at',
    ];

    protected $casts = [
        'total_amount'           => 'decimal:2',
        'amount_per_installment' => 'decimal:2',
        'confirmed_at'           => 'datetime',
    ];

    // ── Relationships ────────────────────────────────────────────────────────

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(InstallmentSchedule::class)->orderBy('installment_number');
    }

    // ── Scopes ───────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeForSemester($query, string $schoolYear, string $semester)
    {
        return $query->where('school_year', $schoolYear)
                     ->where('semester', $semester);
    }

    // ── Computed Attributes ──────────────────────────────────────────────────

    /**
     * Human-readable plan label, e.g. "Full Payment" or "3-Installment Plan".
     */
    public function getPlanLabelAttribute(): string
    {
        return $this->plan_type === 'full'
            ? 'Full Payment'
            : "{$this->total_installments}-Installment Plan";
    }

    /**
     * Whether every schedule row has been marked paid.
     */
    public function isComplete(): bool
    {
        return $this->schedules->isNotEmpty()
            && $this->schedules->every(fn ($s) => $s->is_paid);
    }
}