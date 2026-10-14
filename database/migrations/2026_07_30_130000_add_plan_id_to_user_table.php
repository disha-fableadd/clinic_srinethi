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
        Schema::table('user', function (Blueprint $table) {
            // Add plan_id column as foreign key
            $table->unsignedBigInteger('plan_id')->nullable()->after('branch_id');
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user', function (Blueprint $table) {
            // Drop foreign key
            $table->dropForeign(['plan_id']);
            
            // Drop column
            $table->dropColumn('plan_id');
        });
    }
};
