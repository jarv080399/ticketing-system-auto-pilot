<?php

namespace Tests\Feature\Api\V1\Admin;

use App\Models\User;
use App\Models\SystemSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SettingsTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'admin']);
        
        SystemSetting::create([
            'key' => 'app_name',
            'value' => 'Old Name',
            'group' => 'general',
            'type' => 'string'
        ]);
    }

    public function test_admin_can_retrieve_settings(): void
    {
        $response = $this->actingAs($this->admin)
            ->getJson('/api/v1/admin/settings');

        $response->assertStatus(200)
            ->assertJsonPath('data.general.0.key', 'app_name');
    }

    public function test_admin_can_update_settings(): void
    {
        $response = $this->actingAs($this->admin)
            ->patchJson('/api/v1/admin/settings', [
                'settings' => [
                    'app_name' => 'New Awesome Name'
                ]
            ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('system_settings', [
            'key' => 'app_name',
            'value' => 'New Awesome Name'
        ]);
    }

    public function test_non_admin_cannot_access_settings(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($user)
            ->getJson('/api/v1/admin/settings');

        $response->assertStatus(403);
    }
}
