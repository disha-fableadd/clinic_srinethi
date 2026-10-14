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
        Schema::table('services', function (Blueprint $table) {
            $table->text('description')->nullable()->change();

            if (!Schema::hasColumn('services', 'patient_id')) {
                $table->unsignedBigInteger('patient_id')->after('user_id')->nullable();
            }
            if (!Schema::hasColumn('services', 'department')) {
                $table->string('department')->after('patient_id')->nullable();
            }
            if (!Schema::hasColumn('services', 'service_id')) {
                $table->unsignedBigInteger('service_id')->after('department')->nullable();
            }
            if (!Schema::hasColumn('services', 'cost')) {
                $table->decimal('cost', 10, 2)->after('description')->nullable();
            }
        });

        foreach (['name', 'type', 'price', 'status'] as $column) {
            if (Schema::hasColumn('services', $column)) {
                Schema::table('services', function (Blueprint $table) use ($column) {
                    $table->dropColumn($column);
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            if (!Schema::hasColumn('services', 'name')) {
                $table->string('name')->nullable();
            }
            if (!Schema::hasColumn('services', 'type')) {
                $table->string('type')->nullable();
            }
            if (!Schema::hasColumn('services', 'price')) {
                $table->decimal('price', 10, 2)->nullable();
            }
            if (!Schema::hasColumn('services', 'status')) {
                $table->string('status')->nullable();
            }
        });
    }
};
