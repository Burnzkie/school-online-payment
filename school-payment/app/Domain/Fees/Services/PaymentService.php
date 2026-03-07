<?php

namespace App\Domain\Fees\Services;

use App\Domain\Fees\Models\Payment;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class PaymentService
{
    public function recordManualPayment(array $data, UploadedFile $proof = null): Payment
    {
        $payment = Payment::create([
            'invoice_id'     => $data['invoice_id'],
            'payer_id'       => auth()->id(),
            'amount'         => $data['amount'],
            'payment_method' => 'manual',
            'status'         => 'pending',
            'notes'          => $data['notes'] ?? null,
        ]);

        if ($proof) {
            $path = $proof->store('proofs/' . now()->format('Y-m'), 'public');

            $payment->proof()->create([
                'file_path'   => $path,
                'file_name'   => $proof->getClientOriginalName(),
                'mime_type'   => $proof->getMimeType(),
                'uploaded_by' => auth()->id(),
            ]);
        }

        // Later: send notification to cashiers

        return $payment;
    }

    public function approvePayment(Payment $payment, string $notes = null): void
    {
        $payment->update([
            'status'      => 'approved',
            'verified_by' => auth()->id(),
            'verified_at' => now(),
            'notes'       => $notes,
        ]);

        // Later: update invoice status, send receipt
    }
}