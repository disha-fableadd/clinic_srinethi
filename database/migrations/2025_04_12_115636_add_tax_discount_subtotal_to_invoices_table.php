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
        Schema::table('invoices', function (Blueprint $table) {
            $table->bigInteger('subtotal')->default(0)->after('grand_total');
            $table->bigInteger('tax')->default(0)->after('subtotal');
            $table->bigInteger('discount')->default(0)->after('tax');
          
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn(['tax', 'discount', 'subtotal']);

        });
    }
};
