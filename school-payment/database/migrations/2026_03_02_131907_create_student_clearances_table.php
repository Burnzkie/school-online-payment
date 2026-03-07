<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * student_clearances
     *  — Tracks finance clearance status per student per semester.
     *  — is_cleared = false means the student is on HOLD (cannot take exams, enroll, etc.)
     *  — hold_reason is a free-text explanation shown to the cashier.
     *  — cleared_by / cleared_at set when the treasurer manually clears the student.
     *
     * NOTE: Clearance is auto-computed (balance === 0) OR manually overridden.
     */
    public function up(): void
    {
        Schema::create('student_clearances', function (Blueprint $table) {
            $table->id();

            $table->foreignId('student_id')
                  ->constrained('users')
                  ->onDelete('cascade');

            $table->string('school_year', 20);
            $table->enum('semester', ['1', '2', 'summer']);

            $table->boolean('is_cleared')->default(false);
            $table->text('hold_reason')->nullable();     // e.g. "Balance of ₱4,500 unpaid"

            // Manual override fields
            $table->boolean('manual_override')->default(false);
            $table->text('override_note')->nullable();

            $table->foreignId('cleared_by')
                  ->nullable()
                  ->constrained('users')
                  ->onDelete('set null');

            $table->timestamp('cleared_at')->nullable();

            $table->timestamps();

            $table->unique(
                ['student_id', 'school_year', 'semester'],
                'unique_clearance_per_semester'
            );

            $table->index(['school_year', 'semester', 'is_cleared'], 'idx_clearance_period_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_clearances');
    }
};