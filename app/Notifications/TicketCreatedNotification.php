<?php

namespace App\Notifications;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TicketCreatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $ticket;

    /**
     * Create a new notification instance.
     */
    public function __construct(Ticket $ticket)
    {
        $this->ticket = $ticket;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        $channels = ['database'];
        
        if ($notifiable->prefersNotification('ticket_created', 'email')) {
            $channels[] = 'mail';
        }

        if ($notifiable->prefersNotification('ticket_created', 'slack')) {
            $channels[] = 'slack';
        }

        return $channels;
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $domain = explode('@', config('mail.from.address'))[1] ?? 'company.com';
        $replyTo = "ticket-{$this->ticket->ticket_number}@{$domain}";

        return (new MailMessage)
                    ->subject("Ticket Acknowledgement: {$this->ticket->ticket_number}")
                    ->replyTo($replyTo)
                    ->greeting("Hello {$notifiable->name},")
                    ->line("Thanks for reaching out! We've received your ticket: **{$this->ticket->title}**.")
                    ->line("Your ticket number is **{$this->ticket->ticket_number}**. An agent will review it shortly.")
                    ->action('View Ticket Details', url('/tickets/' . $this->ticket->ticket_number))
                    ->line('Thank you for using our service!');
    }

    /**
     * Get the Slack representation of the notification.
     */
    public function toSlack(object $notifiable)
    {
        return (new \Illuminate\Notifications\Slack\SlackMessage)
            ->text("New Ticket Created: #{$this->ticket->ticket_number}")
            ->headerBlock("🎫 New Ticket: {$this->ticket->ticket_number}")
            ->sectionBlock(function ($section) {
                $section->text("*Title:* {$this->ticket->title}\n*Priority:* {$this->ticket->priority}");
            })
            ->actionsBlock(function ($actions) {
                $actions->button('View Ticket')->url(url('/agent/tickets/' . $this->ticket->ticket_number));
            });
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'ticket_id' => $this->ticket->id,
            'ticket_number' => $this->ticket->ticket_number,
            'title' => $this->ticket->title,
            'message' => 'Your ticket has been created successfully.',
        ];
    }
}
