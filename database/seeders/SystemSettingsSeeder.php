<?php

namespace Database\Seeders;

use App\Models\SystemSetting;
use Illuminate\Database\Seeder;

class SystemSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            [
                'key' => 'app_name',
                'value' => 'IT Helpdesk',
                'group' => 'general',
                'type' => 'string',
            ],
            [
                'key' => 'app_logo_url',
                'value' => null,
                'group' => 'branding',
                'type' => 'string',
            ],
            [
                'key' => 'default_ticket_priority',
                'value' => 'Medium',
                'group' => 'general',
                'type' => 'string',
            ],
            [
                'key' => 'auto_close_hours',
                'value' => '72',
                'group' => 'general',
                'type' => 'integer',
            ],
            [
                'key' => 'nudge_hours',
                'value' => '48',
                'group' => 'general',
                'type' => 'integer',
            ],
            [
                'key' => 'max_file_upload_mb',
                'value' => '20',
                'group' => 'general',
                'type' => 'integer',
            ],
            [
                'key' => 'allowed_file_types',
                'value' => 'jpg,png,pdf,doc,docx,xlsx,csv,zip',
                'group' => 'general',
                'type' => 'string',
            ],
        ];

        foreach ($settings as $setting) {
            SystemSetting::updateOrCreate(['key' => $setting['key']], $setting);
        }
    }
}
