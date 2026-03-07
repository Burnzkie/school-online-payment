<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentFee extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'student_fees';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'student_id',
        'fee_id',
        'amount_paid',
        'is_fully_paid',
        'due_date',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'amount_paid' => 'decimal:2',
        'is_fully_paid' => 'boolean',
        'due_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the student.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    /**
     * Get the fee.
     */
    public function fee(): BelongsTo
    {
        return $this->belongsTo(Fee::class, 'fee_id');
    }

    /**
     * Calculate the remaining balance for this fee.
     */
    public function getRemainingBalanceAttribute(): float
    {
        $feeAmount = $this->fee->amount ?? 0;
        return max(0, $feeAmount - $this->amount_paid);
    }

    /**
     * Check if fee is overdue.
     */
    public function isOverdue(): bool
    {
        if (!$this->due_date || $this->is_fully_paid) {
            return false;
        }
        
        return $this->due_date->isPast();
    }
}