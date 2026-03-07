<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * scholarships
     *  — One record per student per semester.
     *  — discount_type: 'percent' reduces amount by X%, 'fixed' reduces by a fixed peso amount.
     *  — approved_by: references users (treasurer/cashier who approved it).
     */
    public function up(): void
    {
        Schema::create('scholarships', function (Blueprint $table) {
            $table->id();

            $table->foreignId('student_id')
                  ->constrained('users')
                  ->onDelete('cascade');

            $table->string('scholarship_name');           // e.g. "Academic Scholar", "Sibling Discount"
            $table->string('school_year', 20);
            $table->enum('semester', ['1', '2', 'summer']);

            $table->enum('discount_type', ['percent', 'fixed']);
            $table->decimal('discount_value', 10, 2);    // % or ₱ value
            $table->decimal('max_discount', 10, 2)->nullable(); // cap for percent discounts

            // Which fees this applies to — null means ALL active fees
            $table->string('applies_to_fee')->nullable(); // fee_name string or null

            $table->enum('status', ['active', 'revoked'])->default('active');
            $table->text('remarks')->nullable();

            $table->foreignId('approved_by')
                  ->nullable()
                  ->constrained('users')
                  ->onDelete('set null');

            $table->timestamps();

            $table->index(['student_id', 'school_year', 'semester'], 'idx_scholarships_student_sem');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scholarships');
    }
};