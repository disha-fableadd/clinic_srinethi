<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DefaultEmailTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('default_email_template')->updateOrInsert(
            ['id' => 1],
            [
                'name' => 'Welcome Template',
                'content' => '<html><body><h2>Welcome</h2><p>Thank you for choosing our clinic.</p></body></html>',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }
}
