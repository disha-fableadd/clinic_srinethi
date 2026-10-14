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
        Schema::create('ot_procedures', function (Blueprint $table) {
             $table->id(); // Primary Key

            $table->unsignedBigInteger('patient_id'); // FK to patients
            $table->unsignedBigInteger('doctor_id');  // FK to doctors

            $table->string('procedure_name');
            $table->date('procedure_date');
            $table->text('operation_notes')->nullable();
            $table->enum('status', ['scheduled', 'completed'])->default('scheduled');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ot_procedures');
    }
};
