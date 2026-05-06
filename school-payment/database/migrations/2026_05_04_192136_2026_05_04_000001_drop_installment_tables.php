<?php
// database/migrations/2026_05_04_000001_drop_installment_tables.php
// Run: php artisan migrate

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Drop child table first (foreign key constraint)
        Schema::dropIfExists('installment_schedules');
        Schema::dropIfExists('installment_plans');
    }

    public function down(): void
    {
        // Nothing — we don't want to restore these tables
    }
};