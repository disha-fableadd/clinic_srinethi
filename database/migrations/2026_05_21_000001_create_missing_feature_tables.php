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
        if (!Schema::hasTable('user_permissions')) {
            Schema::create('user_permissions', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->unsignedBigInteger('module_id')->nullable();
                $table->boolean('create')->default(false);
                $table->boolean('view')->default(false);
                $table->boolean('update')->default(false);
                $table->boolean('delete')->default(false);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('default_email_template')) {
            Schema::create('default_email_template', function (Blueprint $table) {
                $table->id();
                $table->string('name')->nullable();
                $table->longText('content')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('code_master')) {
            Schema::create('code_master', function (Blueprint $table) {
                $table->id();
                $table->string('code')->nullable();
                $table->unsignedBigInteger('branch_id')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('assessment_template')) {
            Schema::create('assessment_template', function (Blueprint $table) {
                $table->id();
                $table->string('name')->nullable();
                $table->longText('title')->nullable();
                $table->longText('description')->nullable();
                $table->longText('image')->nullable();
                $table->unsignedBigInteger('branch_id')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('diagnosis')) {
            Schema::create('diagnosis', function (Blueprint $table) {
                $table->id();
                $table->string('name')->nullable();
                $table->text('description')->nullable();
                $table->unsignedBigInteger('branch_id')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('expenses')) {
            Schema::create('expenses', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->unsignedBigInteger('branch_id')->nullable();
                $table->dateTime('date_time')->nullable();
                $table->decimal('amount', 12, 2)->default(0);
                $table->string('service')->nullable();
                $table->text('comment')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('home_advice')) {
            Schema::create('home_advice', function (Blueprint $table) {
                $table->id();
                $table->string('template_name')->nullable();
                $table->longText('title')->nullable();
                $table->longText('description')->nullable();
                $table->longText('image')->nullable();
                $table->unsignedBigInteger('branch_id')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('lab_tests')) {
            Schema::create('lab_tests', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('patient_id')->nullable();
                $table->unsignedBigInteger('doctor_id')->nullable();
                $table->unsignedBigInteger('service_id')->nullable();
                $table->text('test_description')->nullable();
                $table->date('test_date')->nullable();
                $table->text('result')->nullable();
                $table->string('status')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('machines')) {
            Schema::create('machines', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('branch_id')->nullable();
                $table->string('name')->nullable();
                $table->decimal('price', 12, 2)->nullable();
                $table->text('description')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('patient_assign_assessment')) {
            Schema::create('patient_assign_assessment', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('branch_id')->nullable();
                $table->unsignedBigInteger('patient_id')->nullable();
                $table->longText('template_id')->nullable();
                $table->longText('description')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('patient_assign_homeadvice')) {
            Schema::create('patient_assign_homeadvice', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('branch_id')->nullable();
                $table->unsignedBigInteger('patient_id')->nullable();
                $table->longText('template_id')->nullable();
                $table->longText('description')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('prescriptions')) {
            Schema::create('prescriptions', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('patient_id')->nullable();
                $table->unsignedBigInteger('doctor_id')->nullable();
                $table->unsignedBigInteger('medicine_id')->nullable();
                $table->string('dosage')->nullable();
                $table->string('frequency')->nullable();
                $table->string('duration')->nullable();
                $table->text('instruction')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('referal_doctors')) {
            Schema::create('referal_doctors', function (Blueprint $table) {
                $table->id();
                $table->string('doctor_name')->nullable();
                $table->string('specialist')->nullable();
                $table->string('email')->nullable();
                $table->string('phone_number')->nullable();
                $table->unsignedBigInteger('branch_id')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('soap')) {
            Schema::create('soap', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->unsignedBigInteger('branch_id')->nullable();
                $table->unsignedBigInteger('patient_id')->nullable();
                $table->date('date')->nullable();
                $table->longText('subjective')->nullable();
                $table->longText('objective')->nullable();
                $table->longText('assessment')->nullable();
                $table->longText('plan')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('taxes')) {
            Schema::create('taxes', function (Blueprint $table) {
                $table->id();
                $table->string('tax_name')->nullable();
                $table->decimal('tax_rate', 12, 2)->default(0);
                $table->string('status')->default('active');
                $table->unsignedBigInteger('branch_id')->nullable();
                $table->boolean('isDeleted')->default(false);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('treatment_booking')) {
            Schema::create('treatment_booking', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('patient_id')->nullable();
                $table->unsignedBigInteger('branch_id')->nullable();
                $table->unsignedBigInteger('treatment_id')->nullable();
                $table->longText('machine_id')->nullable();
                $table->text('plan')->nullable();
                $table->decimal('remain_amount', 12, 2)->default(0);
                $table->string('status')->default('pending');
                $table->date('payment_date')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('treatment_payment_history')) {
            Schema::create('treatment_payment_history', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->unsignedBigInteger('treatment_booking_id')->nullable();
                $table->decimal('amount', 12, 2)->default(0);
                $table->string('payment_mode')->nullable();
                $table->string('paid_type')->nullable();
                $table->decimal('cash', 12, 2)->default(0);
                $table->decimal('online', 12, 2)->default(0);
                $table->decimal('paid_amount', 12, 2)->default(0);
                $table->decimal('remain_amount', 12, 2)->default(0);
                $table->unsignedBigInteger('branch_id')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('treatment_payment_history');
        Schema::dropIfExists('treatment_booking');
        Schema::dropIfExists('taxes');
        Schema::dropIfExists('soap');
        Schema::dropIfExists('referal_doctors');
        Schema::dropIfExists('prescriptions');
        Schema::dropIfExists('patient_assign_homeadvice');
        Schema::dropIfExists('patient_assign_assessment');
        Schema::dropIfExists('machines');
        Schema::dropIfExists('lab_tests');
        Schema::dropIfExists('home_advice');
        Schema::dropIfExists('expenses');
        Schema::dropIfExists('diagnosis');
        Schema::dropIfExists('assessment_template');
        Schema::dropIfExists('code_master');
        Schema::dropIfExists('default_email_template');
        Schema::dropIfExists('user_permissions');
    }
};
