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
        Schema::create('assigned_therapies', function (Blueprint $table) {
            $table->id();
            $table->string('branch_id')->nullable();
            $table->unsignedBigInteger('user_id');     // Assigned by
            $table->unsignedBigInteger('patient_id');  // Therapy given to
            $table->unsignedBigInteger('therapy_id');  // Therapy type
            $table->unsignedBigInteger('doctor_id');   // Responsible doctor

            $table->enum('type', ['virtual', 'inperson'])->default('inperson');

            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();

            $table->enum('status', ['scheduled', 'ongoing', 'completed', 'cancelled'])->default('scheduled');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assigned_therapies');
    }
};
