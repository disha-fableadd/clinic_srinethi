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
        Schema::table('patient_medicine', function (Blueprint $table) {
            $table->unsignedBigInteger('treatment_id')->nullable()->after('medicine_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('patient_medicine', function (Blueprint $table) {
            $table->dropColumn('treatment_id');
        });
    }
};
