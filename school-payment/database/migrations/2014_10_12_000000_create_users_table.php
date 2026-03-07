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
        Schema::create('users', function (Blueprint $table) {
            // Primary Key
            $table->id();
            
            // Authentication
            $table->string('email', 255)->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password', 255);
            $table->rememberToken();
            
            // Role
            $table->string('role', 50)->nullable(); // student, parent, treasurer, cashier
            
            // Personal Information
            $table->string('name', 255); // Used as first name in form
            $table->string('middle_name', 100)->nullable();
            $table->string('last_name', 100)->nullable();
            $table->string('suffix', 20)->nullable();
            $table->date('birth_date')->nullable();
            $table->tinyInteger('age')->unsigned()->nullable();
            $table->string('gender', 20)->nullable();
            $table->string('nationality', 100)->nullable()->default('Filipino');
            $table->string('phone', 30)->nullable(); // Mobile number
            
            // Student Identification
            $table->string('student_id', 50)->nullable()->unique();
            
            // Enrollment Information
            $table->string('level_group', 50)->nullable(); // Kinder, Elementary, Junior High, Senior High, College
            $table->string('year_level', 100)->nullable();
            $table->string('strand', 50)->nullable(); // For Senior High School
            $table->string('department', 150)->nullable(); // For College
            $table->string('program', 150)->nullable(); // Course/Program for College
            
            // Address Information
            $table->string('street', 255)->nullable();
            $table->string('barangay', 255)->nullable();
            $table->string('municipality', 255)->nullable();
            $table->string('city', 255)->nullable();
            
            // Parent Information - Father
            $table->string('father_name', 255)->nullable();
            $table->string('father_occupation', 255)->nullable();
            $table->string('father_contact', 30)->nullable();
            
            // Parent Information - Mother
            $table->string('mother_name', 255)->nullable();
            $table->string('mother_occupation', 255)->nullable();
            $table->string('mother_contact', 30)->nullable();
            
            // Guardian Information
            $table->string('guardian_name', 255)->nullable();
            $table->string('guardian_relationship', 100)->nullable();
            $table->string('guardian_contact', 30)->nullable();
            
            // Timestamps
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};