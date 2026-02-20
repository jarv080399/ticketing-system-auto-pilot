<?php

namespace App\Jobs;

use App\Exports\TicketsExport;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Str;

class ExportReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public array $params,
        public User $user
    ) {}

    public function handle(): void
    {
        $startDate = $this->params['start_date'];
        $endDate = $this->params['end_date'];
        $format = $this->params['format'] ?? 'csv';
        
        $fileName = 'exports/tickets_' . Str::slug($this->user->name) . '_' . now()->timestamp . '.' . $format;
        
        Excel::store(new TicketsExport($startDate, $endDate), $fileName, 'public');

        // Note: In a real app, you'd notify the user here via Notification/Event
        // or update a 'reports' table with the download link.
    }
}
