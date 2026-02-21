<?php

namespace App\Notifications;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TicketUpdatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(protected Ticket $ticket, protected string $message = 'Your ticket has been updated.')
    {}

    public function via(object $notifiable): array
    {
        $channels = ['database'];
        
        if ($notifiable->prefersNotification('ticket_updated', 'email')) {
            $channels[] = 'mail';
        }

        return $channels;
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Update on Ticket #{$this->ticket->ticket_number}")
            ->line("Your ticket **{$this->ticket->title}** has a new update:")
            ->line($this->message)
            ->action('View Ticket Details', url('/tickets/' . $this->ticket->ticket_number));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'ticket_id' => $this->ticket->id,
            'ticket_number' => $this->ticket->ticket_number,
            'title' => $this->ticket->title,
            'message' => $this->message,
        ];
    }
}
