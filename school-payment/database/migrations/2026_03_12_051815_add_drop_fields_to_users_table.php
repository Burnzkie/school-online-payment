<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Adds student drop / status tracking columns to the users table.
     *
     * These fields are required by AdminController::studentDrop() and the
     * dropped-student login block in AuthenticatedSessionController::store().
     *
     * status         — 'active' (default) or 'dropped'
     * drop_reason    — selected reason (e.g. "Financial Reasons", "Transfer")
     * drop_notes     — optional free-text notes from the admin
     * dropped_at     — timestamp when the drop action was performed
     * dropped_by_name — full name of the admin who performed the drop
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'status')) {
                $table->string('status', 20)
                      ->default('active')
                      ->after('role')
                      ->comment('active | dropped');
            }

            if (!Schema::hasColumn('users', 'drop_reason')) {
                $table->string('drop_reason', 255)
                      ->nullable()
                      ->after('status');
            }

            if (!Schema::hasColumn('users', 'drop_notes')) {
                $table->text('drop_notes')
                      ->nullable()
                      ->after('drop_reason');
            }

            if (!Schema::hasColumn('users', 'dropped_at')) {
                $table->timestamp('dropped_at')
                      ->nullable()
                      ->after('drop_notes');
            }

            if (!Schema::hasColumn('users', 'dropped_by_name')) {
                $table->string('dropped_by_name', 255)
                      ->nullable()
                      ->after('dropped_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $columns = ['status', 'drop_reason', 'drop_notes', 'dropped_at', 'dropped_by_name'];

            foreach ($columns as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};