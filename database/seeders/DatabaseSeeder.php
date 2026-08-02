<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,       // Must be first — creates roles
            UserSeeder::class,       // Depends on roles
            RoomTypeSeeder::class,   // No dependencies
            RoomSeeder::class,       // Depends on room_types
            GuestSeeder::class,      // No dependencies
            BookingSeeder::class,    // Depends on users, rooms, guests
        ]);
    }
}
