<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ModulesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $modules = [
            1 => 'Dashboard',
            2 => 'Followup',
            3 => 'User',
            4 => 'Medicines',
            5 => 'Patients',
            6 => 'Appointments',
            7 => 'Treatments',
            8 => 'Diagnostic Services',
            9 => 'Calender',
            10 => 'Discharge',
            11 => 'Inventory',
            12 => 'Supplier',
            13 => 'Medical Report',
            14 => 'Report',
            15 => 'Sms',
            16 => 'Email',
            17 => 'PatientMedicine',
            18 => 'Invoice',
            19 => 'Pathology',
            20 => 'Pathology Reports',
            21 => 'Radiology Tests',
            22 => 'Radiology Reports',
            23 => 'Opd Visits',
            24 => 'OT',
            25 => 'Ipd Admit',
            26 => 'Therapy',
            27 => 'Assigned Therapy',
            28 => 'Daily Data',
            29 => 'Payment History',
            30 => 'Branch',
            31 => 'Assessment',
            32 => 'Home Advice',
            33 => 'Soap',
            34 => 'Machine',
            35 => 'Tax Rate',
            36 => 'Referal Doctor',
        ];

        foreach ($modules as $id => $module) {
            DB::table('modules')->updateOrInsert(
                ['id' => $id],
                [
                    'name' => $module,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
