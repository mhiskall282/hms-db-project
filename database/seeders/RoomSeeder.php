<?php

namespace Database\Seeders;

use App\Models\Room;
use App\Models\RoomType;
use Illuminate\Database\Seeder;

class RoomSeeder extends Seeder
{
    public function run(): void
    {
        $types = RoomType::all()->keyBy('name');

        $rooms = [
            // Floor 1 — Singles
            ['room_number' => '101', 'room_type' => 'Single',  'floor' => 1, 'status' => 'available'],
            ['room_number' => '102', 'room_type' => 'Single',  'floor' => 1, 'status' => 'available'],
            ['room_number' => '103', 'room_type' => 'Twin',    'floor' => 1, 'status' => 'available'],
            ['room_number' => '104', 'room_type' => 'Twin',    'floor' => 1, 'status' => 'maintenance'],
            // Floor 2 — Doubles
            ['room_number' => '201', 'room_type' => 'Double',  'floor' => 2, 'status' => 'available'],
            ['room_number' => '202', 'room_type' => 'Double',  'floor' => 2, 'status' => 'available'],
            ['room_number' => '203', 'room_type' => 'Double',  'floor' => 2, 'status' => 'dirty'],
            ['room_number' => '204', 'room_type' => 'Deluxe',  'floor' => 2, 'status' => 'available'],
            // Floor 3 — Premium
            ['room_number' => '301', 'room_type' => 'Deluxe',  'floor' => 3, 'status' => 'available'],
            ['room_number' => '302', 'room_type' => 'Deluxe',  'floor' => 3, 'status' => 'available'],
            ['room_number' => '303', 'room_type' => 'Suite',   'floor' => 3, 'status' => 'available'],
            ['room_number' => '304', 'room_type' => 'Suite',   'floor' => 3, 'status' => 'available'],
        ];

        foreach ($rooms as $roomData) {
            $typeName = $roomData['room_type'];
            unset($roomData['room_type']);

            Room::updateOrCreate(
                ['room_number' => $roomData['room_number']],
                array_merge($roomData, ['room_type_id' => $types[$typeName]->id])
            );
        }

        $this->command->info('Rooms seeded: ' . count($rooms));
    }
}
