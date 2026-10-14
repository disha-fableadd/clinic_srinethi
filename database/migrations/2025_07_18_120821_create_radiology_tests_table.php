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
        Schema::create('radiology_tests', function (Blueprint $table) {
            $table->id();
            $table->string('branch_id')->nullable();
            $table->string('test_name');
            $table->string('test_code')->unique();
            $table->string('body_part');
            $table->decimal('cost', 8, 2);
            $table->string('gst_option');
            $table->text('product_gst')->nullable();
            $table->string('report_format')->default('Digital Image'); // or DICOM
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('radiology_tests');
    }
};
