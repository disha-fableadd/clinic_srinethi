<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('user')) {
            return;
        }

        Schema::table('user', function (Blueprint $table) {
            if (Schema::hasColumn('user', 'fullname')) {
                $table->text('fullname')->nullable()->change();
            }

            if (Schema::hasColumn('user', 'email')) {
                $table->text('email')->nullable()->change();
            }

            if (Schema::hasColumn('user', 'phone')) {
                $table->text('phone')->nullable()->change();
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('user')) {
            return;
        }

        Schema::table('user', function (Blueprint $table) {
            if (Schema::hasColumn('user', 'fullname')) {
                $table->string('fullname', 100)->nullable()->change();
            }

            if (Schema::hasColumn('user', 'email')) {
                $table->string('email', 100)->nullable()->change();
            }

            if (Schema::hasColumn('user', 'phone')) {
                $table->string('phone', 50)->nullable()->change();
            }
        });
    }
};
