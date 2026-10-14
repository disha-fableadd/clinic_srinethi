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
        Schema::create('medicines', function (Blueprint $table) {
            $table->id();
            $table->string('branch_id')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('category_id');
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('unit');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->integer('quantity')->default(0);
            $table->string('medicine_unit');
            $table->string('batch_no');
            $table->string('gst_option');
            $table->text('product_gst')->nullable();
            $table->date('manufacture_date');
            $table->date('expiry_date');
            $table->text('image')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medicines');
    }
};
