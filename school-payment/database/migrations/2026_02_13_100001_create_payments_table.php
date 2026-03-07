<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            
            // Student & Cashier References
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('cashier_id')->nullable()->constrained('users')->onDelete('set null');
            
            // Payment Details
            $table->decimal('amount', 10, 2);
            $table->date('payment_date');                         // Date payment was made
            
            // Academic Period Tracking
            $table->string('school_year', 20);                    // e.g. '2025-2026'
            $table->enum('semester', ['1', '2', 'summer']);       // Which semester this payment is for
            
            // Official Receipt & Reference
            $table->string('or_number', 50)->nullable();          // Official Receipt number
            $table->string('payment_method');                     // e.g., 'Cash', 'GCash', 'PayMaya', 'Bank Transfer'
            $table->string('reference_number')->nullable();       // Transaction reference (for digital payments)
            
            // Payment Status
            $table->enum('status', ['pending', 'completed', 'failed', 'refunded'])->default('pending');
            
            // Additional Notes
            $table->text('notes')->nullable();                    // Optional notes about the payment
            
            $table->timestamps();

            // Composite index for common queries (student payments by semester)
            $table->index(['student_id', 'school_year', 'semester'], 'idx_payments_student_semester');
            
            // Index for OR number lookups
            $table->index('or_number', 'idx_or_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};