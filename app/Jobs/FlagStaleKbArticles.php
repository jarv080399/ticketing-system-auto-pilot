<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\KbArticle;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class FlagStaleKbArticles implements ShouldQueue
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
        $staleThreshold = now()->subMonths(6);

        $staleArticles = KbArticle::where('updated_at', '<', $staleThreshold)
            ->where('status', 'published')
            ->with('author')
            ->get();

        foreach ($staleArticles as $article) {
            if ($article->author && $article->author->email) {
                Mail::raw("Your article '{$article->title}' hasn't been updated in 6 months. Please review it to ensure accuracy.", function ($message) use ($article) {
                    $message->to($article->author->email)
                            ->subject('Action Required: Review Your Stale KB Article');
                });
            } else {
                Log::warning("Stale KB Article '{$article->title}' has no valid author to email.");
            }
        }
    }
}
