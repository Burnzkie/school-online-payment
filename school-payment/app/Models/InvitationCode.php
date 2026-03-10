<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class InvitationCode extends Model
{
    protected $fillable = [
        'code', 'role', 'label', 'created_by', 'used_by', 'used_at', 'expires_at',
    ];

    protected $casts = [
        'used_at'    => 'datetime',
        'expires_at' => 'datetime',
    ];

    // ── Relationships ─────────────────────────────────────────────────────────

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function usedBy()
    {
        return $this->belongsTo(User::class, 'used_by');
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    public function isUsed(): bool
    {
        return $this->used_by !== null;
    }

    public function isExpired(): bool
    {
        return $this->expires_at !== null && $this->expires_at->isPast();
    }

    public function isValid(): bool
    {
        return !$this->isUsed() && !$this->isExpired();
    }

    // ── Static factory ────────────────────────────────────────────────────────

    public static function generate(string $role, int $adminId, ?string $label = null, ?int $expiresInDays = 7): self
    {
        return self::create([
            'code'       => strtoupper(Str::random(4) . '-' . Str::random(4) . '-' . Str::random(4)),
            'role'       => $role,
            'label'      => $label,
            'created_by' => $adminId,
            'expires_at' => $expiresInDays ? now()->addDays($expiresInDays) : null,
        ]);
    }
}