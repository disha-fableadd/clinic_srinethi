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
        Schema::create('diet_charts', function (Blueprint $table) {
            $table->id();
            $table->string('branch_id')->nullable();
            $table->string('name');
            $table->longText('title')->nullable();
            $table->longText('description')->nullable();
            $table->longText('image')->nullable();
            $table->longText('time')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('diet_charts');
    }
};
