<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            ['id' => 1, 'name' => 'SuperAdmin', 'description' => 'Super admin access to all functionality.'],
            ['id' => 2, 'name' => 'Admin', 'description' => 'Admin access to clinic operations.'],
            ['id' => 3, 'name' => 'Doctor', 'description' => 'Doctor access with assigned permissions.'],
            ['id' => 4, 'name' => 'Receptionist', 'description' => 'Receptionist access for front desk operations.'],
            ['id' => 5, 'name' => 'Patient', 'description' => 'Patient portal access.'],
        ];

        foreach ($roles as $role) {
            DB::table('role')->updateOrInsert(
                ['id' => $role['id']],
                [
                    'name' => $role['name'],
                    'description' => $role['description'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
