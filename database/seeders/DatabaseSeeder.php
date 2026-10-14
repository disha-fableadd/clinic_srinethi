<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            BranchSeeder::class,
            ModulesTableSeeder::class,
            CategorySeeder::class,
            DefaultEmailTemplateSeeder::class,
            AdminUserSeeder::class,
        ]);
    }
}
