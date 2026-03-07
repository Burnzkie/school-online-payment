<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;

class StatementController extends Controller
{
    public function index()
    {
        // Later: real payment history query
        $statements = collect([
            ['date' => '2025-08-10', 'description' => 'Tuition Fee – 1st Sem', 'amount' => 21000, 'paid' => true],
            ['date' => '2025-12-15', 'description' => 'Miscellaneous Fee',     'amount' => 5000,  'paid' => true],
            ['date' => '2026-03-01', 'description' => 'Tuition Fee – 2nd Sem', 'amount' => 21000, 'paid' => false],
        ]);

        return view('students.college.statements', compact('statements'));
    }
}