<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Scholarship extends Model
{
    protected $fillable = [
        'student_id',
        'scholarship_name',
        'school_year',
        'semester',
        'discount_type',
        'discount_value',
        'max_discount',
        'applies_to_fee',
        'status',
        'remarks',
        'approved_by',
    ];

    protected $casts = [
        'discount_value' => 'decimal:2',
        'max_discount'   => 'decimal:2',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Compute the peso discount amount against a given fee amount.
     */
    public function computeDiscount(float $feeAmount): float
    {
        if ($this->status !== 'active') return 0;

        if ($this->discount_type === 'percent') {
            $computed = $feeAmount * ($this->discount_value / 100);
            return $this->max_discount
                ? min($computed, $this->max_discount)
                : $computed;
        }

        return min($this->discount_value, $feeAmount);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeForSemester($query, string $schoolYear, string $semester)
    {
        return $query->where('school_year', $schoolYear)->where('semester', $semester);
    }

    public function getDiscountLabelAttribute(): string
    {
        return $this->discount_type === 'percent'
            ? "{$this->discount_value}%"
            : '₱'.number_format($this->discount_value, 2);
    }
}