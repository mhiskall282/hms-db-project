<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name'      => 'System Admin',
                'email'     => 'admin@hms.local',
                'password'  => Hash::make('password'),
                'is_active' => true,
                'role'      => 'admin',
            ],
            [
                'name'      => 'Hotel Manager',
                'email'     => 'manager@hms.local',
                'password'  => Hash::make('password'),
                'is_active' => true,
                'role'      => 'manager',
            ],
            [
                'name'      => 'Front Desk',
                'email'     => 'receptionist@hms.local',
                'password'  => Hash::make('password'),
                'is_active' => true,
                'role'      => 'receptionist',
            ],
            [
                'name'      => 'Housekeeper',
                'email'     => 'housekeeping@hms.local',
                'password'  => Hash::make('password'),
                'is_active' => true,
                'role'      => 'housekeeping',
            ],
            [
                'name'      => 'Cashier',
                'email'     => 'accountant@hms.local',
                'password'  => Hash::make('password'),
                'is_active' => true,
                'role'      => 'accountant',
            ],
        ];

        foreach ($users as $userData) {
            $role = $userData['role'];
            unset($userData['role']);

            $user = User::updateOrCreate(
                ['email' => $userData['email']],
                $userData
            );

            $user->syncRoles([$role]);
            $this->command->info("Created user: {$user->email} ({$role})");
        }
    }
}
