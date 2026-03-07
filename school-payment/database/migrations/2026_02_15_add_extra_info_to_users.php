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
        Schema::table('users', function (Blueprint $table) {
            // Add extra_info field for non-student roles (parent, treasurer, cashier)
            if (!Schema::hasColumn('users', 'extra_info')) {
                $table->string('extra_info', 255)->nullable()->after('guardian_contact');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'extra_info')) {
                $table->dropColumn('extra_info');
            }
        });
    }
};