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
        Schema::create('patient_assign_dietchart', function (Blueprint $table) {
            $table->id();

            $table->string('branch_id')->nullable();

            $table->unsignedBigInteger('patient_id');

            // Stores multiple diet template IDs as JSON
            $table->json('diet_template_id')->nullable();

            // Stores descriptions corresponding to the selected templates
            $table->json('description')->nullable();

            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patient_assign_dietchart');
    }
};
