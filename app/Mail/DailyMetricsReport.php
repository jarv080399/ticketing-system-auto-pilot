<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DailyMetricsReport extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public array $metrics)
    {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Daily Support Performance Report',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.reports.daily',
        );
    }
}
