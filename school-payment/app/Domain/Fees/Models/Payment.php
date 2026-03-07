<?php

namespace App\Domain\Fees\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $fillable = [
        'invoice_id',
        'payer_id',             // user_id who paid (student or parent)
        'amount',
        'payment_method',       // gcash, maya, card, manual, bank, cash
        'status',               // pending, approved, rejected, failed
        'gateway_reference',
        'verified_by',          // cashier user_id
        'verified_at',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'verified_at' => 'datetime',
    ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function payer()
    {
        return $this->belongsTo(\App\Models\User::class, 'payer_id');
    }

    public function verifier()
    {
        return $this->belongsTo(\App\Models\User::class, 'verified_by');
    }

    public function proof()
    {
        return $this->hasOne(PaymentProof::class);
    }

    public function isManual(): bool
    {
        return in_array($this->payment_method, ['manual', 'bank', 'cash']);
    }
}