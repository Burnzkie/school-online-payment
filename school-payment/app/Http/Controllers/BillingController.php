<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\OnlinePaymentSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class BillingController extends Controller
{
    // ── GET /student/billing ─────────────────────────────────────────
    // (Keep your existing index() method here — unchanged.)
    // public function index(Request $request) { ... }


    // ── POST /student/billing/pay-online ─────────────────────────────
    public function payOnline(Request $request)
    {
        $student = Auth::user();

        $validated = $request->validate([
            'school_year'      => ['required', 'string', 'regex:/^\d{4}-\d{4}$/'],
            'semester'         => ['required', 'in:1,2,summer'],
            'payment_method'   => ['required', 'in:GCash,PayMaya,Bank Transfer'],
            'amount'           => ['required', 'numeric', 'min:1'],
            'reference_number' => ['required', 'string', 'max:100'],
            'proof_of_payment' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
            'notes'            => ['nullable', 'string', 'max:500'],
        ]);

        // Store proof of payment file
        $path = $request->file('proof_of_payment')
            ->store("online-payments/{$student->id}", 'public');

        OnlinePaymentSubmission::create([
            'student_id'       => $student->id,
            'school_year'      => $validated['school_year'],
            'semester'         => $validated['semester'],
            'payment_method'   => $validated['payment_method'],
            'amount'           => $validated['amount'],
            'reference_number' => $validated['reference_number'],
            'proof_of_payment' => $path,
            'notes'            => $validated['notes'] ?? null,
            'status'           => 'pending',
        ]);

        return redirect()
            ->route('student.billing', [
                'school_year' => $validated['school_year'],
                'semester'    => $validated['semester'],
            ])
            ->with('payment_submitted', true);
    }
}