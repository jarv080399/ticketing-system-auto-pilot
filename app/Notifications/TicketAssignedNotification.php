<?php

namespace App\Notifications;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Slack\SlackMessage;
use Illuminate\Notifications\Notification;

class TicketAssignedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(protected Ticket $ticket)
    {}

    public function via(object $notifiable): array
    {
        $channels = ['database'];
        
        if ($notifiable->prefersNotification('ticket_assigned', 'email')) {
            $channels[] = 'mail';
        }

        if ($notifiable->prefersNotification('ticket_assigned', 'slack')) {
            $channels[] = 'slack';
        }

        return $channels;
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Ticket Assigned: #{$this->ticket->ticket_number}")
            ->greeting("Hello {$notifiable->name},")
            ->line("The following ticket has been assigned to you: **{$this->ticket->title}**")
            ->action('View Ticket Details', url('/agent/tickets/' . $this->ticket->ticket_number))
            ->line('Please review the ticket at your earliest convenience.');
    }

    public function toSlack(object $notifiable): SlackMessage
    {
        return (new SlackMessage)
            ->text("Ticket Assigned: #{$this->ticket->ticket_number}")
            ->headerBlock("👤 Ticket Assigned to You")
            ->sectionBlock(function ($section) {
                $section->text("*Title:* {$this->ticket->title}\n*Priority:* {$this->ticket->priority}");
            })
            ->actionsBlock(function ($actions) {
                $actions->button('View Ticket')->url(url('/agent/tickets/' . $this->ticket->ticket_number));
            });
    }

    public function toArray(object $notifiable): array
    {
        return [
            'ticket_id' => $this->ticket->id,
            'ticket_number' => $this->ticket->ticket_number,
            'title' => $this->ticket->title,
            'message' => 'New ticket assigned to you.',
        ];
    }
}
