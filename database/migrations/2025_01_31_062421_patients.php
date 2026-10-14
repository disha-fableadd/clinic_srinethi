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
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->string('branch_id')->nullable();
            $table->text('patient_unique_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('login_patient_id')->nullable();
            $table->string('fullname')->nullable();
            $table->text('email')->nullable();
            $table->string('password')->nullable();
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->string('age')->nullable();
            $table->date('birthdate')->nullable();
            $table->string('gender')->nullable();
            $table->enum('patient_type', ['All', 'Cosmetic', 'OPD', 'IPD', 'Home'])->nullable();
            $table->string('referral_source')->nullable();
            $table->text('source_details')->nullable();
            $table->string('state')->nullable();
            $table->string('city')->nullable();
            $table->string('diagnosis_id')->nullable();
            $table->text('last_appointment')->nullable();
            $table->text('profile')->nullable();
            $table->string('note_type')->nullable();
            $table->string('note')->nullable();
            $table->string('note_interval', 500)->nullable();
            $table->timestamps();
            $table->string('symptoms', 1000)->nullable();
            $table->string('improvement_history', 500)->nullable();
            $table->string('symptom_images', 500)->nullable();
            $table->text('symptom_remarks')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};
