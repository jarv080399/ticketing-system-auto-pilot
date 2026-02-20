<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

use Illuminate\Support\Facades\Schedule;
use App\Jobs\CheckSlaBreaches;
use App\Jobs\AutoCloseResolvedTickets;
use App\Jobs\SendCustomerNudges;
use App\Jobs\FlagStaleKbArticles;
use App\Jobs\CheckAssetWarranty;

Schedule::job(new CheckSlaBreaches)->everyFiveMinutes();
Schedule::job(new AutoCloseResolvedTickets)->everyFifteenMinutes();
Schedule::job(new SendCustomerNudges)->everyThirtyMinutes();
Schedule::job(new FlagStaleKbArticles)->weekly();
Schedule::job(new CheckAssetWarranty)->daily();
