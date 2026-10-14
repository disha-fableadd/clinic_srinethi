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
        Schema::create('inventory', function (Blueprint $table) {
            $table->id();
            $table->string('branch_id')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->string('item_name');
            $table->integer('quantity');
            $table->unsignedBigInteger('supplier_id');
            $table->date('purchase_date');
            $table->enum('status', ['active', 'inactive', 'expired']); // Example status
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory');
    }
};
