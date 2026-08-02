<?php

namespace Database\Seeders;

use App\Models\RoomType;
use Illuminate\Database\Seeder;

class RoomTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            ['name' => 'Single',  'base_rate' => 80.00,  'capacity' => 1, 'description' => 'Comfortable single room with one bed, ensuite bathroom, and work desk.'],
            ['name' => 'Double',  'base_rate' => 130.00, 'capacity' => 2, 'description' => 'Spacious double room with a king bed, ensuite bathroom, and sitting area.'],
            ['name' => 'Suite',   'base_rate' => 220.00, 'capacity' => 4, 'description' => 'Luxury suite with separate living room, king bed, kitchenette, and premium amenities.'],
            ['name' => 'Twin',    'base_rate' => 120.00, 'capacity' => 2, 'description' => 'Twin room with two single beds, perfect for colleagues or friends.'],
            ['name' => 'Deluxe',  'base_rate' => 180.00, 'capacity' => 2, 'description' => 'Deluxe room with premium furnishings, a balcony, and superior bath facilities.'],
        ];

        foreach ($types as $type) {
            RoomType::updateOrCreate(['name' => $type['name']], $type);
        }

        $this->command->info('Room types seeded: ' . count($types));
    }
}
