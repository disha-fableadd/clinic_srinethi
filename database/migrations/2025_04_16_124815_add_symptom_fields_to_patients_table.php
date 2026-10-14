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
        if (Schema::hasColumn('patients', 'symptoms')) {
            return;
        }

        Schema::table('patients', function (Blueprint $table) {
            $table->json('symptoms')->nullable();
            $table->json('improvement_history')->nullable();
            $table->json('symptom_images')->nullable();
            $table->text('symptom_remarks')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasColumn('patients', 'symptoms')) {
            return;
        }

        Schema::table('patients', function (Blueprint $table) {
            $table->dropColumn(['symptoms', 'improvement_history', 'symptom_images', 'symptom_remarks']);
        });
    }
};
