<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Creates the parent_student pivot table.
     *
     * link_method:
     *   'auto_phone'   — matched automatically via father/mother/guardian_contact on registration
     *   'auto_name'    — matched via name field (legacy fallback, less reliable)
     *   'manual_admin' — linked manually by a cashier or admin
     *
     * This table is the single source of truth for parent ↔ student access.
     * The ParentController reads ONLY from this table (no more live phone scans).
     */
    public function up(): void
    {
        Schema::create('parent_student', function (Blueprint $table) {
            $table->id();

            $table->foreignId('parent_id')
                  ->constrained('users')
                  ->onDelete('cascade');

            $table->foreignId('student_id')
                  ->constrained('users')
                  ->onDelete('cascade');

            // How the link was established
            $table->enum('link_method', ['auto_phone', 'manual_admin'])
                  ->default('auto_phone');

            // Who created the link (null = system / registration auto-link)
            $table->foreignId('linked_by')
                  ->nullable()
                  ->constrained('users')
                  ->onDelete('set null');

            $table->timestamps();

            // A parent can only be linked to the same student once
            $table->unique(['parent_id', 'student_id'], 'unique_parent_student');

            $table->index('parent_id',  'idx_parent_student_parent');
            $table->index('student_id', 'idx_parent_student_student');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('parent_student');
    }
};