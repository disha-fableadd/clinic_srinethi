<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Antibiotic', 'description' => 'Used to treat bacterial infections.'],
            ['name' => 'Painkiller', 'description' => 'Used to relieve pain.'],
            ['name' => 'Vitamin', 'description' => 'Supplements for health and nutrition.'],
            ['name' => 'Antiseptic', 'description' => 'Prevents infection on wounds.'],
            ['name' => 'Antifungal', 'description' => 'Used to treat fungal infections.'],
            ['name' => 'Antiviral', 'description' => 'Used to treat viral infections.'],
            ['name' => 'Analgesic', 'description' => 'Medication to relieve pain.'],
            ['name' => 'Antipyretic', 'description' => 'Used to reduce fever.'],
            ['name' => 'Antihistamine', 'description' => 'Used to treat allergies.'],
            ['name' => 'Cough Suppressant', 'description' => 'Used to reduce coughing.'],
            ['name' => 'Expectorant', 'description' => 'Helps clear mucus from airways.'],
            ['name' => 'Laxative', 'description' => 'Used to relieve constipation.'],
            ['name' => 'Antacid', 'description' => 'Used to neutralize stomach acid.'],
            ['name' => 'Antidepressant', 'description' => 'Used to treat depression.'],
            ['name' => 'Sedative', 'description' => 'Used to calm or sedate patients.'],
        ];

        foreach ($categories as $category) {
            DB::table('categories')->updateOrInsert(
                ['name' => $category['name']],
                [
                    'description' => $category['description'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
