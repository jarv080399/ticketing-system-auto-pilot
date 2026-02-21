<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\NotificationPreference;
use Illuminate\Database\Seeder;

class NotificationPreferenceSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $channels = ['email', 'in_app', 'slack'];
        $eventTypes = [
            'ticket_created',
            'ticket_assigned',
            'ticket_updated',
            'sla_warning',
            'ticket_resolved',
            'ticket_closed'
        ];

        foreach ($users as $user) {
            foreach ($channels as $channel) {
                foreach ($eventTypes as $event) {
                    NotificationPreference::updateOrCreate([
                        'user_id' => $user->id,
                        'channel' => $channel,
                        'event_type' => $event,
                    ], [
                        'is_enabled' => true,
                    ]);
                }
            }
        }
    }
}
