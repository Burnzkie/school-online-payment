<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * This table tracks which specific fees are assigned to which students.
     * It's a many-to-many relationship between students and fee types.
     * Useful if you have standard fees that get assigned to multiple students.
     */
    public function up(): void
    {
        Schema::create('student_fees', function (Blueprint $table) {
            $table->id();
            
            // References
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('fee_id')->constrained('fees')->onDelete('cascade');
            
            // Payment tracking for this specific fee
            $table->decimal('amount_paid', 10, 2)->default(0);
            $table->boolean('is_fully_paid')->default(false);
            
            // Due date for this specific fee (optional)
            $table->date('due_date')->nullable();
            
            $table->timestamps();

            // Composite unique index to prevent duplicate fee assignments
            $table->unique(['student_id', 'fee_id'], 'unique_student_fee');
            
            // Index for quick lookups
            $table->index('student_id', 'idx_student_fees_student');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_fees');
    }
};