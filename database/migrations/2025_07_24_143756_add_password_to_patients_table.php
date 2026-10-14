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
        if (Schema::hasColumn('patients', 'password')) {
            return;
        }

        Schema::table('patients', function (Blueprint $table) {
              $table->string('password')->after('email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasColumn('patients', 'password')) {
            return;
        }

        Schema::table('patients', function (Blueprint $table) {
              $table->dropColumn('password');
        });
    }
};
