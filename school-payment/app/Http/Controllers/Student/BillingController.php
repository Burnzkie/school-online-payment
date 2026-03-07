<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BillingController extends Controller
{
    public function index(Request $request)
    {
        $student = Auth::user();
        
        // Get selected year and semester from request
        $selectedYear = $request->input('school_year', '2025-2026');
        $selectedSemester = $request->input('semester', '2');
        
        // Get available years for dropdown
        $availableYears = DB::table('fees')
            ->where('id', $student->id)
            ->distinct()
            ->pluck('school_year')
            ->toArray();
        
        if (empty($availableYears)) {
            $availableYears = ['2025-2026', '2024-2025'];
        }
        
        // Get fees for selected semester
        $fees = DB::table('fees')
            ->where('id', $student->id)
            ->where('school_year', $selectedYear)
            ->where('semester', $selectedSemester)
            ->get();
        
        // Get payments for selected semester
        $payments = DB::table('payments')
            ->where('student_id', $student->id)
            ->where('school_year', $selectedYear)
            ->where('semester', $selectedSemester)
            ->orderBy('payment_date', 'asc')
            ->get();
        
        // Build ledger items
        $ledgerItems = [];
        
        // Add fees as charges
        foreach ($fees as $fee) {
            $ledgerItems[] = [
                'description' => $fee->fee_name . ($fee->description ? ' - ' . $fee->description : ''),
                'charge' => $fee->amount,
                'payment' => 0,
            ];
        }
        
        // Add payments
        foreach ($payments as $payment) {
            $ledgerItems[] = [
                'description' => 'PAYMENT - OR: ' . ($payment->or_number ?? $payment->reference_number) . ' - ' . strtoupper($payment->payment_method),
                'charge' => 0,
                'payment' => $payment->amount,
            ];
        }
        
        // Calculate totals
        $totalCharges = $fees->sum('amount');
        $totalPaid = $payments->sum('amount');
        $balance = $totalCharges - $totalPaid;
        
        return view('students.college.billing', [
            'selectedYear' => $selectedYear,
            'selectedSemester' => $selectedSemester,
            'availableYears' => $availableYears,
            'balance' => $balance,
            'paid' => $totalPaid,
            'totalCharges' => $totalCharges,
            'ledgerItems' => $ledgerItems,
        ]);
    }
}