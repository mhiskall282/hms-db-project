<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = ['admin', 'manager', 'receptionist', 'housekeeping', 'accountant'];

        foreach ($roles as $role) {
            Role::findOrCreate($role, 'web');
        }

        $this->command->info('Roles created: ' . implode(', ', $roles));
    }
}
