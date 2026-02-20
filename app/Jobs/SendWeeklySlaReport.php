<?php

namespace App\Jobs;

use App\Mail\WeeklySlaReport;
use App\Models\User;
use App\Repositories\ReportRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class SendWeeklySlaReport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(ReportRepository $repository): void
    {
        $startDate = Carbon::now()->subDays(7)->startOfDay()->toDateTimeString();
        $endDate = Carbon::now()->toDateTimeString();
        
        $slaRate = $repository->getSlaCompliance($startDate, $endDate);
        
        $managers = User::whereIn('role', ['admin', 'agent'])->get();
        
        foreach ($managers as $manager) {
            Mail::to($manager->email)->send(new WeeklySlaReport(['rate' => $slaRate, 'period' => 'Last 7 Days']));
        }
    }
}
