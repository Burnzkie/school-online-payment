<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OnlinePaymentSubmission extends Model
{
    protected $fillable = [
        'student_id',
        'school_year',
        'semester',
        'payment_method',
        'amount',
        'reference_number',
        'proof_of_payment',
        'notes',
        'status',
        'verified_by',
        'verified_at',
        'rejection_reason',
        'payment_id',
    ];

    protected $casts = [
        'amount'      => 'decimal:2',
        'verified_at' => 'datetime',
    ];

    // ── Relationships ────────────────────────────────────────────────

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    // ── Helpers ──────────────────────────────────────────────────────

    public function isPending(): bool   { return $this->status === 'pending'; }
    public function isVerified(): bool  { return $this->status === 'verified'; }
    public function isRejected(): bool  { return $this->status === 'rejected'; }
}