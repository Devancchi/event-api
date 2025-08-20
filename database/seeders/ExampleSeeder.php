<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Event;

class ExampleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1 admin
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('123'),
            'role' => 'admin',
        ]);

        // 2 organizers
        $organizer1 = User::create([
            'name' => 'Organizer One',
            'email' => 'organizer1@example.com',
            'password' => Hash::make('123'),
            'role' => 'organizer',
        ]);

        $organizer2 = User::create([
            'name' => 'Organizer Two',
            'email' => 'organizer2@example.com',
            'password' => Hash::make('123'),
            'role' => 'organizer',
        ]);

        // 1 Event (Mei–Agustus)
        Event::create([
            'title' => 'Beach Party',
            'description' => 'Event mulai dari Mei sampai Agustus',
            'venue' => 'Central Beach',
            'start_datetime' => '2025-05-01 09:00:00',
            'end_datetime' => '2025-08-31 18:00:00',
            'status' => 'published',
            'organizer_id' => $organizer1->id,
        ]);
    }
}
