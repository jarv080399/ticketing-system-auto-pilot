<?php

namespace App\Notifications;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Slack\SlackMessage;
use Illuminate\Notifications\Notification;

class SlaWarningNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(protected Ticket $ticket, protected string $threshold = '30 minutes')
    {}

    public function via(object $notifiable): array
    {
        $channels = ['database'];
        
        if ($notifiable->prefersNotification('sla_warning', 'email')) {
            $channels[] = 'mail';
        }

        if ($notifiable->prefersNotification('sla_warning', 'slack')) {
            $channels[] = 'slack';
        }

        return $channels;
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->error()
            ->subject("⚠️ SLA Warning: Ticket #{$this->ticket->ticket_number}")
            ->line("Ticket **{$this->ticket->title}** is approaching its SLA deadline in {$this->threshold}.")
            ->action('Critical: Review Ticket', url('/agent/tickets/' . $this->ticket->ticket_number));
    }

    public function toSlack(object $notifiable): SlackMessage
    {
        return (new SlackMessage)
            ->warning()
            ->text("⚠️ SLA Warning: Ticket #{$this->ticket->ticket_number}")
            ->headerBlock("🚨 SLA Breach Imminent")
            ->sectionBlock(function ($section) {
                $section->text("*Ticket:* {$this->ticket->title}\n*Deadline Info:* Approaching breach in {$this->threshold}.");
            })
            ->actionsBlock(function ($actions) {
                $actions->button('Take Action')->url(url('/agent/tickets/' . $this->ticket->ticket_number));
            });
    }

    public function toArray(object $notifiable): array
    {
        return [
            'ticket_id' => $this->ticket->id,
            'ticket_number' => $this->ticket->ticket_number,
            'title' => $this->ticket->title,
            'message' => "SLA deadline approaching in {$this->threshold}.",
            'level' => 'warning',
        ];
    }
}
