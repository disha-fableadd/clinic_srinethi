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
       Schema::create('plans', function (Blueprint $table) {
            $table->id();

            $table->string('name');

            $table->decimal('price', 10, 2)->nullable();

            $table->string('duration')->default('month');

            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();

            $table->string('subtitle')->nullable();

            $table->integer('user_limit')->nullable();

            $table->integer('branch_limit')->nullable();

            $table->integer('storage_limit')->nullable();

            $table->boolean('is_active')->default(true);

            $table->unsignedBigInteger('sub_branch_id')->nullable();

            $table->longText('features')->nullable();

            $table->decimal('total_amount', 10, 2)->nullable();

            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
