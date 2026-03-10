<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InvitationCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InvitationCodeController extends Controller
{
    /**
     * List all invitation codes.
     */
    public function index(Request $request)
    {
        $codes = InvitationCode::with(['creator', 'usedBy'])
            ->when($request->role,   fn($q) => $q->where('role', $request->role))
            ->when($request->status === 'used',    fn($q) => $q->whereNotNull('used_by'))
            ->when($request->status === 'unused',  fn($q) => $q->whereNull('used_by'))
            ->latest()
            ->paginate(20);

        return view('admin.invitation-codes.index', compact('codes'));
    }

    /**
     * Generate a new invitation code.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'role'            => ['required', 'in:cashier,treasurer,parent'],
            'label'           => ['nullable', 'string', 'max:100'],
            'expires_in_days' => ['nullable', 'integer', 'min:1', 'max:365'],
            'quantity'        => ['nullable', 'integer', 'min:1', 'max:20'],
        ]);

        $quantity = $validated['quantity'] ?? 1;

        for ($i = 0; $i < $quantity; $i++) {
            InvitationCode::generate(
                role:          $validated['role'],
                adminId:       Auth::id(),
                label:         $validated['label'] ?? null,
                expiresInDays: $validated['expires_in_days'] ?? 7,
            );
        }

        return back()->with('success', "{$quantity} invitation code(s) generated for role: {$validated['role']}.");
    }

    /**
     * Revoke (delete) an unused invitation code.
     */
    public function destroy(InvitationCode $invitationCode)
    {
        if ($invitationCode->isUsed()) {
            return back()->with('error', 'Cannot revoke a code that has already been used.');
        }

        $invitationCode->delete();

        return back()->with('success', 'Invitation code revoked.');
    }
}