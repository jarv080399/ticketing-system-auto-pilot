<?php

namespace App\Mailboxes;

use App\Models\Ticket;
use App\Models\User;
use App\Models\TicketComment;
use BeyondCode\Mailbox\InboundEmail;
use Illuminate\Support\Str;

class SupportMailbox
{
    public function __invoke(InboundEmail $email)
    {
        $from = $email->from();
        $subject = $email->subject();
        $body = $email->text();
        
        // Find or create user
        $user = User::firstOrCreate(
            ['email' => $from],
            ['name' => explode('@', $from)[0], 'password' => bcrypt(Str::random(16)), 'role' => 'user']
        );

        // Check if it's a reply to an existing ticket
        // Pattern: ticket-TKT-12345678@...
        $recipients = $email->to();
        if (!is_array($recipients)) {
            $recipients = [$recipients];
        }

        foreach ($recipients as $to) {
            if (preg_match('/ticket-(TKT-[A-Z0-9]{8})@/i', $to, $matches)) {
                $ticketNumber = $matches[1];
                $ticket = Ticket::where('ticket_number', $ticketNumber)->first();
                
                if ($ticket) {
                    $this->addComment($ticket, $user, $body);
                    return;
                }
            }
        }

        // Otherwise, create a new ticket
        $this->createNewTicket($user, $subject, $body);
    }

    protected function createNewTicket($user, $subject, $body)
    {
        Ticket::create([
            'requester_id' => $user->id,
            'title' => $subject ?: 'Email Ticket',
            'description' => $body,
            'status' => 'open',
            'priority' => 'medium',
            'source' => 'email',
            'category_id' => 1, // Assumption: Hardware/General is ID 1
        ]);
    }

    protected function addComment($ticket, $user, $body)
    {
        TicketComment::create([
            'ticket_id' => $ticket->id,
            'user_id' => $user->id,
            'body' => $body,
            'is_internal' => false,
        ]);

        // Reopen ticket if it was closed/resolved?
        if (in_array($ticket->status, ['closed', 'resolved'])) {
            $ticket->update(['status' => 'open']);
        }
    }
}
