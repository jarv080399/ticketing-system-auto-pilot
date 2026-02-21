<?php

namespace App\Notifications;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TicketResolvedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(protected Ticket $ticket)
    {}

    public function via(object $notifiable): array
    {
        $channels = ['database'];
        
        if ($notifiable->prefersNotification('ticket_resolved', 'email')) {
            $channels[] = 'mail';
        }

        return $channels;
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->success()
            ->subject("Ticket Resolved: #{$this->ticket->ticket_number}")
            ->greeting("Hello {$notifiable->name},")
            ->line("Your ticket **{$this->ticket->title}** has been marked as **Resolved**.")
            ->line("If you feel the issue is still ongoing, you can reply to the email or click the link below to reopen it.")
            ->action('View Ticket Details', url('/tickets/' . $this->ticket->ticket_number))
            ->line('We hope your issue was resolved to your satisfaction.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'ticket_id' => $this->ticket->id,
            'ticket_number' => $this->ticket->ticket_number,
            'title' => $this->ticket->title,
            'message' => 'Your ticket has been resolved. Please provide feedback!',
            'type' => 'resolution',
        ];
    }
}
