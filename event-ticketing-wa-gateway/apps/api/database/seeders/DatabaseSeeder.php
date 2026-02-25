<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create Admin User
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        // Create Staff User
        User::create([
            'name' => 'Staff User',
            'email' => 'staff@example.com',
            'password' => Hash::make('password'),
            'role' => 'staff',
            'email_verified_at' => now(),
        ]);

        // Create Sample Event
        Event::create([
            'name' => 'Tech Conference 2026',
            'venue' => 'Jakarta Convention Center',
            'date_start' => now()->addDays(7)->setTime(9, 0),
            'date_end' => now()->addDays(7)->setTime(17, 0),
        ]);

        Event::create([
            'name' => 'Music Festival',
            'venue' => 'Gelora Bung Karno',
            'date_start' => now()->addDays(14)->setTime(18, 0),
            'date_end' => now()->addDays(14)->setTime(23, 0),
        ]);

        $this->command->info('Seeding completed!');
        $this->command->info('Admin: admin@example.com / password');
        $this->command->info('Staff: staff@example.com / password');
    }
}