<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WeeklySlaReport extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public array $slaData)
    {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Weekly SLA Compliance Summary',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.reports.weekly_sla',
        );
    }
}
