<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Extend invoices.type enum to include appointment, therapy, discharge.
     */
    public function up(): void
    {
        $driver = DB::getDriverName();
        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE invoices MODIFY COLUMN type ENUM('service', 'treatment', 'medicine', 'followup', 'appointment', 'therapy', 'discharge') NOT NULL");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = DB::getDriverName();
        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE invoices MODIFY COLUMN type ENUM('service', 'treatment', 'medicine') NOT NULL");
        }
    }
};
