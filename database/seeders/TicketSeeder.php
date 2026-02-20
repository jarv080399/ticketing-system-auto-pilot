<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Seeder;

class TicketSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create Default Categories
        $categories = [
            ['name' => 'Hardware Support', 'slug' => 'hardware', 'icon' => '💻', 'description' => 'Laptops, monitors, and peripherals.'],
            ['name' => 'Software & Apps', 'slug' => 'software', 'icon' => '💿', 'description' => 'Licensed software and internal tools.'],
            ['name' => 'Network & Wi-Fi', 'slug' => 'network', 'icon' => '📡', 'description' => 'Connectivity issues and VPN.'],
            ['name' => 'Account & Access', 'slug' => 'access', 'icon' => '🔐', 'description' => 'Permissions and password resets.'],
            ['name' => 'Mobile Devices', 'slug' => 'mobile', 'icon' => '📱', 'description' => 'Company phones and tablets.'],
            ['name' => 'General Inquiry', 'slug' => 'general', 'icon' => '❓', 'description' => 'Everything else.'],
        ];

        foreach ($categories as $cat) {
            Category::updateOrCreate(['slug' => $cat['slug']], $cat);
        }

        // 2. Create sample tickets if we have users
        $users = User::all();
        $dbCategories = Category::all();

        if ($users->count() > 0 && $dbCategories->count() > 0) {
            // Create 20 tickets for random users
            for ($i = 0; $i < 20; $i++) {
                Ticket::factory()->create([
                    'requester_id' => $users->random()->id,
                    'category_id' => $dbCategories->random()->id,
                ]);
            }
        }
    }
}
