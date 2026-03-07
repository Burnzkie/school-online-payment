<?php

namespace App\Domain\Fees\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    protected $fillable = [
        'student_id',
        'invoice_number',
        'academic_year_id',
        'total_amount',
        'due_date',
        'status',           // pending, partial, paid, overdue
        'notes',
    ];

    protected $casts = [
        'due_date' => 'date',
        'total_amount' => 'decimal:2',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class); // assume you have App\Models\Student
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function getBalanceAttribute(): float
    {
        return $this->total_amount - $this->payments()->sum('amount');
    }
}