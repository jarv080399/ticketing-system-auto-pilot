<?php

namespace App\Jobs;

use App\Mail\DailyMetricsReport;
use App\Models\User;
use App\Repositories\ReportRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class SendDailyMetricsReport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(ReportRepository $repository): void
    {
        $startDate = Carbon::yesterday()->startOfDay()->toDateTimeString();
        $endDate = Carbon::yesterday()->endOfDay()->toDateTimeString();
        
        $metrics = $repository->getPerformanceMetrics($startDate, $endDate);
        
        $managers = User::whereIn('role', ['admin', 'agent'])->get();
        
        foreach ($managers as $manager) {
            Mail::to($manager->email)->send(new DailyMetricsReport($metrics));
        }
    }
}
