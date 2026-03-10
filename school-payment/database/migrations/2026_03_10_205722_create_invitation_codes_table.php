<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invitation_codes', function (Blueprint $table) {
            $table->id();
            $table->string('code', 32)->unique();
            $table->enum('role', ['cashier', 'treasurer', 'parent']);
            $table->string('label')->nullable();          // e.g. "For Maria Santos - Parent"
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('used_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('used_at')->nullable();
            $table->timestamp('expires_at')->nullable();  // null = never expires
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invitation_codes');
    }
};