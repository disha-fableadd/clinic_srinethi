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
        if (Schema::hasTable('user')) {
            return;
        }

        Schema::create('user', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('role_id')->nullable();
            $table->string('username', 100)->nullable();
            $table->text('fullname')->nullable();
            $table->text('email')->nullable();
            $table->text('phone')->nullable();
            $table->text('profile')->nullable();
            $table->text('password')->nullable();
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->string('clinic_name')->nullable();
            $table->string('clinic_logo')->nullable();
            $table->string('reset_token')->nullable();
            $table->string('status')->default('active');
            $table->text('fcm_token')->nullable();
            $table->string('forget_pass_key')->nullable();
            $table->boolean('is_deleted')->default(false);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user');
    }
};
