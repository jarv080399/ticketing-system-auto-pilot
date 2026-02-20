<?php

namespace App\Jobs;

use App\Models\Asset;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class CheckAssetWarranty implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $expiringAssets = Asset::whereDate('warranty_expires_at', '=', now()->addDays(30)->toDateString())
            ->get();

        foreach ($expiringAssets as $asset) {
            Log::info("Warranty expiring in 30 days for asset: {$asset->serial_number} (Tag: {$asset->asset_tag})");
            
            // Here you would typically send a notification:
            // $admin = User::where('role', 'admin')->first();
            // $admin->notify(new AssetWarrantyExpiring($asset));
        }
    }
}
