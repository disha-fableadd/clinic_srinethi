<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserDetails;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::all()->first(function (User $user) {
            return strtolower((string) $user->email) === 'admin@gmail.com';
        });

        if (!$admin) {
            $admin = new User();
        }

        $admin->role_id = 2;
        $admin->fullname = 'Admin';
        $admin->email = 'admin@gmail.com';
        $admin->phone = '9999999999';
        $admin->branch_id = 1;
        $admin->clinic_name = 'Clinic Manage System';
        $admin->status = 'active';
        $admin->is_deleted = false;
        $admin->password = Hash::make('12345678');
        $admin->save();

        UserDetails::updateOrCreate(
            ['user_id' => $admin->id],
            [
                'address' => 'Main Clinic Address',
                'state' => 'Gujarat',
                'city' => 'Surat',
                'gender' => 'Other',
                'birth_date' => '2000-01-01',
                'shift' => 'FullDay',
                'salary' => null,
            ]
        );
    }
}
