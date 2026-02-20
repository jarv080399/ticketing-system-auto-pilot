<?php

namespace Database\Seeders;

use App\Models\CannedResponse;
use App\Models\Ticket;
use App\Models\TicketComment;
use App\Models\User;
use Illuminate\Database\Seeder;

class AgentWorkspaceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $agent = User::where('role', 'agent')->first();
        $user = User::where('role', 'user')->first();
        $admin = User::where('role', 'admin')->first();

        // 1. Canned Responses
        $templates = [
            [
                'title' => 'Resolution Confirmation',
                'body' => "Hello,\n\nWe believe the issue you reported has been resolved. Please let us know if you require further assistance.\n\nBest regards,\nSupport Team",
                'category' => 'General',
                'is_shared' => true,
            ],
            [
                'title' => 'Hardware Out of Stock',
                'body' => "Hi,\n\nUnfortunately, the hardware requested is currently backordered. We expect new stock in 2 weeks.\n\nThanks for your patience.",
                'category' => 'Hardware',
                'is_shared' => true,
            ],
            [
                'title' => 'Password Reset Link',
                'body' => "Your password has been reset. Use the following link to set a new one: [LINK]\n\nSecurity Team",
                'category' => 'Access',
                'is_shared' => false,
            ]
        ];

        foreach ($templates as $tpl) {
            CannedResponse::create(array_merge($tpl, ['created_by' => $agent->id]));
        }

        // 2. Ticket Comments thread
        $ticket = Ticket::first();

        if ($ticket) {
            // Customer reply
            TicketComment::create([
                'ticket_id' => $ticket->id,
                'user_id' => $user->id,
                'body' => 'I already tried restarting it, but the error persists.',
                'is_internal' => false,
            ]);

            // Agent internal note
            TicketComment::create([
                'ticket_id' => $ticket->id,
                'user_id' => $agent->id,
                'body' => 'Checking server logs now. Might be a database lock issue.',
                'is_internal' => true,
            ]);

            // Agent public reply
            TicketComment::create([
                'ticket_id' => $ticket->id,
                'user_id' => $agent->id,
                'body' => 'Thank you for the update. I am investigating the server logs now.',
                'is_internal' => false,
            ]);
            
            $ticket->update([
                'agent_id' => $agent->id,
                'status' => 'in_progress',
                'first_response_at' => now()->subMinutes(30),
            ]);
        }
    }
}
