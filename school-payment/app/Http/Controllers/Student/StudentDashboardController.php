<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class StudentDashboardController extends Controller
{
    public function index()
    {
        // Later: fetch real data from DB
        $data = [
            'balance'        => 12450.00,
            'nextDueDate'    => 'March 15, 2026',
            'progress'       => 68,
            'paid'           => 28500,
            'total'          => 42000,
            'recentPayments' => collect([
                (object) ['date' => now()->subDays(12), 'amount' => 5000, 'method' => 'GCash'],
                (object) ['date' => now()->subDays(45), 'amount' => 8000, 'method' => 'Bank'],
            ]),
        ];

        return view('students.college.dashboard', $data);
    }

    public function paymentCreate()
    {
        // Placeholder – later real payment initiation logic
        return view('students.college.payment.create', [
            'balance'   => 12450.00,
            'due_date'  => 'March 15, 2026',
        ]);
    }
}