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
        if (Schema::hasTable('daily_data')) {
            return;
        }

        Schema::create('daily_data', function (Blueprint $table) {
            $table->id();

            // Foreign keys
            $table->unsignedBigInteger('user_id'); // Logged-in user
            $table->unsignedBigInteger('patient_id');
            $table->unsignedBigInteger('treatment_id');
            $table->date('date');
            $table->decimal('amount', 10, 2);
            $table->enum('status', ['full', 'partial', 'pending'])->default('pending');
            $table->unsignedBigInteger('collect_by_id'); // User who collected the payment

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('daily_data')) {
            return;
        }

        Schema::dropIfExists('daily_data');
    }
};
