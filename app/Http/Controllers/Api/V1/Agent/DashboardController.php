<?php

namespace App\Http\Controllers\Api\V1\Agent;

use App\Http\Controllers\Controller;
use App\Models\SatisfactionSurvey;
use App\Models\Ticket;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Get live stats for the Agent Dashboard.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'stats' => $this->getStats($user),
            'slaMetrics' => $this->getSlaMetrics(),
        ]);
    }

    private function getStats($user): array
    {
        // 1. Assigned to Me (current agent, active status)
        $assignedToMeCount = Ticket::where('agent_id', $user->id)
            ->whereIn('status', ['new', 'in_progress', 'waiting_on_customer'])
            ->count();

        // Trend logic (mocked or basic for now - e.g. +2 this hour)
        $assignedTrend = "+" . Ticket::where('agent_id', $user->id)
            ->where('created_at', '>=', now()->subHour())
            ->count() . " this hour";

        // 2. In Queue (Total unassigned active tickets)
        $inQueueCount = Ticket::whereNull('agent_id')
            ->whereIn('status', ['new', 'in_progress'])
            ->count();
        
        $queueTrend = "-" . Ticket::whereNull('agent_id')
            ->where('status', 'resolved')
            ->where('updated_at', '>=', now()->subHour())
            ->count() . " this hour";

        // 3. Avg Resolution Time (Global or User specific?)
        // Let's do Global for now, in hours.
        $avgResMinutes = DB::table('tickets')
            ->whereNotNull('resolved_at')
            ->select(DB::raw('AVG(TIMESTAMPDIFF(MINUTE, created_at, resolved_at)) as avg_time'))
            ->first()->avg_time ?? 0;
            
        $avgResHours = round($avgResMinutes / 60, 1);

        // 4. Customer CSAT (Avg rating)
        $csat = SatisfactionSurvey::avg('rating') ?? 0;
        $csatFormatted = number_format($csat, 1);

        return [
            [
                'label' => 'Assigned To Me',
                'value' => (string)$assignedToMeCount,
                'icon' => '👤',
                'trend' => $assignedTrend,
                'trendColor' => 'text-emerald-500'
            ],
            [
                'label' => 'In Queue',
                'value' => (string)$inQueueCount,
                'icon' => '📥',
                'trend' => $queueTrend,
                'trendColor' => 'text-emerald-500'
            ],
            [
                'label' => 'Avg Resolution',
                'value' => $avgResHours . 'h',
                'icon' => '⚡',
                'trend' => '+12m vs avg',
                'trendColor' => 'text-red-500'
            ],
            [
                'label' => 'Customer CSAT',
                'value' => $csatFormatted,
                'icon' => '⭐️',
                'trend' => 'Top 1%',
                'trendColor' => 'text-primary'
            ],
        ];
    }

    private function getSlaMetrics(): array
    {
        // Mocked or calculated if SLADueAt is available
        // Initial Response: % of tickets where first_response_at <= created_at + SLA
        $totalTickets = Ticket::count();
        if ($totalTickets === 0) {
            return [
                ['label' => 'Initial Response', 'value' => 0],
                ['label' => 'First Contact Resolve', 'value' => 0],
                ['label' => 'Resolution Time', 'value' => 0],
            ];
        }

        // Basic calculation for demonstration
        $initialResOk = Ticket::whereNotNull('first_response_at')->count();
        $initialResRate = round(($initialResOk / $totalTickets) * 100);

        return [
            ['label' => 'Initial Response', 'value' => $initialResRate],
            ['label' => 'First Contact Resolve', 'value' => 65], // Placeholder for now
            ['label' => 'Resolution Time', 'value' => 89],     // Placeholder for now
        ];
    }
}
