<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Admin Account
        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password123'),
            'role' => 'admin',
        ]);

        // Agent Account
        User::factory()->create([
            'name' => 'Agent Smith',
            'email' => 'agent@example.com',
            'password' => bcrypt('password123'),
            'role' => 'agent',
        ]);

        // End User
        User::factory()->create([
            'name' => 'Jane Doe',
            'email' => 'user@example.com',
            'password' => bcrypt('password123'),
            'role' => 'user',
        ]);

        User::factory(50)->create();

        $this->call(TicketSeeder::class);
        $this->call(AutomationSeeder::class);
        $this->call(KbSeeder::class);
        $this->call(SystemSettingsSeeder::class);
        $this->call(BusinessHoursSeeder::class);
        $this->call(HolidaySeeder::class);
    }
}
