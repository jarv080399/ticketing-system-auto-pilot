<?php

namespace App\Exports;

use App\Models\Ticket;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class TicketsExport implements FromQuery, WithHeadings, WithMapping
{
    protected $startDate;
    protected $endDate;

    public function __construct($startDate, $endDate)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function query()
    {
        return Ticket::query()->whereBetween('created_at', [$this->startDate, $this->endDate]);
    }

    public function headings(): array
    {
        return [
            'ID',
            'Ticket Number',
            'Title',
            'Status',
            'Priority',
            'Requester',
            'Agent',
            'Created At',
            'Resolved At'
        ];
    }

    public function map($ticket): array
    {
        return [
            $ticket->id,
            $ticket->ticket_number,
            $ticket->title,
            $ticket->status,
            $ticket->priority,
            $ticket->requester->name ?? 'N/A',
            $ticket->agent->name ?? 'Unassigned',
            $ticket->created_at->toDateTimeString(),
            $ticket->resolved_at ? $ticket->resolved_at->toDateTimeString() : 'N/A',
        ];
    }
}
