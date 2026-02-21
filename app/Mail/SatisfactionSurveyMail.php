<?php

namespace App\Mail;

use App\Models\Ticket;
use App\Models\SatisfactionSurvey;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SatisfactionSurveyMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Ticket $ticket,
        public SatisfactionSurvey $survey
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "We'd love your feedback on Ticket #{$this->ticket->ticket_number}",
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.tickets.survey',
            with: [
                'surveyUrl' => url("/survey/{$this->survey->token}"),
            ],
        );
    }
}
