<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Asset;
use App\Models\AssetHistory;
use App\Models\User;

class AssetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $admin = $users->where('role', 'admin')->first() ?? User::first();

        Asset::factory(50)->create()->each(function ($asset) use ($users, $admin) {
            // Give 50% an owner
            if (rand(0, 1)) {
                $owner = $users->random();
                $asset->update([
                    'owner_user_id' => $owner->id,
                    'status' => 'active',
                ]);

                AssetHistory::create([
                    'asset_id' => $asset->id,
                    'action' => 'assigned',
                    'performed_by' => $admin->id,
                    'new_value' => $owner->id,
                ]);
            } else {
                $asset->update([
                    'owner_user_id' => null,
                    'status' => 'in_stock',
                ]);
                
                AssetHistory::create([
                    'asset_id' => $asset->id,
                    'action' => 'created',
                    'performed_by' => $admin->id,
                ]);
            }
        });
    }
}
