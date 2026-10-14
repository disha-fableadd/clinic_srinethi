<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BranchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('branches')->updateOrInsert(
            ['id' => 1],
            [
                'name' => 'Main Branch',
                'email' => 'branch@clinic.com',
                'address' => 'Main Clinic Address',
                'city' => 'Surat',
                'state' => 'Gujarat',
                'country' => 'India',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }
}
