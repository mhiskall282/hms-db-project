<?php

namespace Database\Seeders;

use App\Models\Guest;
use Illuminate\Database\Seeder;

class GuestSeeder extends Seeder
{
    public function run(): void
    {
        $guests = [
            [
                'name'        => 'Kwame Asante',
                'phone'       => '+233244123456',
                'email'       => 'kwame.asante@email.com',
                'id_number'   => 'GHA-0001234',
                'nationality' => 'Ghanaian',
                'notes'       => 'Prefers quiet rooms.',
            ],
            [
                'name'        => 'Abena Mensah',
                'phone'       => '+233201987654',
                'email'       => 'abena.mensah@gmail.com',
                'id_number'   => 'GHA-0005678',
                'nationality' => 'Ghanaian',
                'notes'       => null,
            ],
            [
                'name'        => 'John Smith',
                'phone'       => '+44207123456',
                'email'       => 'j.smith@corp.co.uk',
                'id_number'   => 'UK-GX0001234',
                'nationality' => 'British',
                'notes'       => 'Business traveller.',
            ],
            [
                'name'        => 'Fatima Al-Rashid',
                'phone'       => '+971501234567',
                'email'       => 'fatima.alrashid@mail.ae',
                'id_number'   => 'AE-12345678',
                'nationality' => 'Emirati',
                'notes'       => 'Allergic to feather pillows.',
            ],
            [
                'name'        => 'Yaw Darko',
                'phone'       => '+233277654321',
                'email'       => null,
                'id_number'   => 'GHA-0009876',
                'nationality' => 'Ghanaian',
                'notes'       => 'Walk-in guest.',
            ],
            [
                'name'        => 'Emily Chen',
                'phone'       => '+1-555-0134',
                'email'       => 'emily.chen@us.org',
                'id_number'   => 'US-A12345678',
                'nationality' => 'American',
                'notes'       => 'Conference attendee.',
            ],
        ];

        foreach ($guests as $data) {
            Guest::updateOrCreate(['id_number' => $data['id_number']], $data);
        }

        $this->command->info('Guests seeded: ' . count($guests));
    }
}
