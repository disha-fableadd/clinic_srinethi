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
        if (Schema::hasColumn('patients', 'login_patient_id')) {
            return;
        }

        Schema::table('patients', function (Blueprint $table) {
              $table->unsignedBigInteger('login_patient_id')->after('user_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasColumn('patients', 'login_patient_id')) {
            return;
        }

        Schema::table('patients', function (Blueprint $table) {
                        $table->dropColumn('login_patient_id');
        });
    }
};
