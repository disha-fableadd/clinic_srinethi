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
        Schema::create('payment_history', function (Blueprint $table) {
            $table->id();
            $table->string('branch_id')->nullable();
            $table->unsignedBigInteger('user_id');         // logged-in user
            $table->unsignedBigInteger('daily_id');        // reference to daily_data
            $table->decimal('amount', 10, 2);              // original amount
            $table->enum('payment_mode', ['cash', 'online', 'cash+online']);
            $table->enum('paid_type', ['fully', 'partial']);
            $table->integer('cash_amount')->nullable();
            $table->integer('online_amount')->nullable();
            $table->decimal('paid_amount', 10, 2);         // amount paid
            $table->decimal('remain_amount', 10, 2)->default(0); // remaining amount
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_history');
    }
};
