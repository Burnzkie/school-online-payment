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
        Schema::create('fees', function (Blueprint $table) {
            $table->id();
            
            // Student Reference
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            
            // Academic Period Tracking
            $table->string('school_year', 20);                    // e.g. '2025-2026'
            $table->enum('semester', ['1', '2', 'summer']);       // 1st, 2nd, or Summer
            
            // Fee Information
            $table->string('fee_name');                           // e.g., 'Tuition Fee', 'Lab Fee', 'Library Fee'
            $table->decimal('amount', 10, 2);                     // Fee amount
            $table->text('description')->nullable();              // Optional description
            
            // Status & Metadata
            $table->enum('status', ['active', 'waived', 'cancelled'])->default('active');
            $table->timestamps();

            // Composite index for common queries (student fees by semester)
            $table->index(['student_id', 'school_year', 'semester'], 'idx_fees_student_semester');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fees');
    }
};