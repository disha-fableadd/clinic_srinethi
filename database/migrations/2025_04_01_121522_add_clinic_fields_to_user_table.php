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
        if (! Schema::hasTable('user')) {
            return;
        }

        Schema::table('user', function (Blueprint $table) {
            if (! Schema::hasColumn('user', 'clinic_name')) {
                $table->string('clinic_name')->nullable()->after('password');
            }
            if (! Schema::hasColumn('user', 'clinic_logo')) {
                $table->string('clinic_logo')->nullable()->after('clinic_name');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('user')) {
            return;
        }

        Schema::table('user', function (Blueprint $table) {
            $dropColumns = [];
            if (Schema::hasColumn('user', 'clinic_name')) {
                $dropColumns[] = 'clinic_name';
            }
            if (Schema::hasColumn('user', 'clinic_logo')) {
                $dropColumns[] = 'clinic_logo';
            }
            if (! empty($dropColumns)) {
                $table->dropColumn($dropColumns);
            }
        });
    }
};
