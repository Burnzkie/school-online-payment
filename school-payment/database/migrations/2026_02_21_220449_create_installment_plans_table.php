<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Creates two tables:
     *  - installment_plans   : one record per student per semester
     *  - installment_schedules: one row per payment installment
     */
    public function up(): void
    {
        // ── installment_plans ─────────────────────────────────────────────────
        Schema::create('installment_plans', function (Blueprint $table) {
            $table->id();

            $table->foreignId('student_id')
                  ->constrained('users')
                  ->onDelete('cascade');

            // Academic context
            $table->string('school_year', 20);                          // e.g. '2025-2026'
            $table->enum('semester', ['1', '2', 'summer']);

            // Plan choice
            $table->enum('plan_type', ['full', '2', '3', '4']);
            $table->unsignedTinyInteger('total_installments')->default(1);

            // Financial snapshot at confirmation time
            $table->decimal('total_amount', 10, 2);
            $table->decimal('amount_per_installment', 10, 2);

            // Lifecycle
            $table->enum('status', ['active', 'completed', 'cancelled'])->default('active');
            $table->timestamp('confirmed_at')->nullable();

            $table->timestamps();

            // One active plan per student per semester
            $table->unique(
                ['student_id', 'school_year', 'semester'],
                'unique_plan_per_semester'
            );

            $table->index(
                ['student_id', 'school_year', 'semester'],
                'idx_plan_student_semester'
            );
        });

        // ── installment_schedules ─────────────────────────────────────────────
        Schema::create('installment_schedules', function (Blueprint $table) {
            $table->id();

            $table->foreignId('installment_plan_id')
                  ->constrained('installment_plans')
                  ->onDelete('cascade');

            $table->foreignId('student_id')
                  ->constrained('users')
                  ->onDelete('cascade');

            // Which installment in the sequence (1, 2, 3…)
            $table->unsignedTinyInteger('installment_number');

            // Amount expected
            $table->decimal('amount_due', 10, 2);

            // Due date computed from semester start
            $table->date('due_date');

            // Payment tracking
            $table->decimal('amount_paid', 10, 2)->default(0);
            $table->boolean('is_paid')->default(false);
            $table->date('paid_at')->nullable();

            // Link to payments table when settled
            $table->foreignId('payment_id')
                  ->nullable()
                  ->constrained('payments')
                  ->onDelete('set null');

            // Overdue flag (set by scheduled Artisan command)
            $table->boolean('is_overdue')->default(false);

            $table->timestamps();

            $table->index(
                ['installment_plan_id', 'installment_number'],
                'idx_plan_installment'
            );
            $table->index(
                ['student_id', 'is_paid'],
                'idx_schedule_student_paid'
            );
            $table->index('due_date', 'idx_schedule_due_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('installment_schedules');
        Schema::dropIfExists('installment_plans');
    }
};