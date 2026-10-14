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
        Schema::table('followups', function (Blueprint $table) {
            $table->enum('followup_type', [
                'Regular followup ',
                'Appointment related',
                'Medicine related',
                'Discharge related ',
                'Report related',
                'other'
            ])->nullable()->after('followup_update'); // or choose the column after which you want to add
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('followups', function (Blueprint $table) {
            $table->dropColumn('followup_type');
        });
    }
};
