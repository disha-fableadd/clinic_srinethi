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

        if (! Schema::hasColumn('user', 'created_by')) {
            Schema::table('user', function (Blueprint $table) {
                $table->unsignedBigInteger('created_by')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('user') || ! Schema::hasColumn('user', 'created_by')) {
            return;
        }

        Schema::table('user', function (Blueprint $table) {
            $table->dropColumn('created_by');
        });
    }
};
