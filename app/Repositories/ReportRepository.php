<?php

namespace App\Repositories;

use App\Models\Ticket;
use App\Models\SatisfactionSurvey;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportRepository
{
    public function getPerformanceMetrics($startDate, $endDate)
    {
        $tickets = Ticket::whereBetween('created_at', [$startDate, $endDate]);

        $avgFirstResponse = DB::table('tickets')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->whereNotNull('first_response_at')
            ->select(DB::raw('AVG(TIMESTAMPDIFF(MINUTE, created_at, first_response_at)) as avg_time'))
            ->first()->avg_time;

        $avgResolution = DB::table('tickets')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->whereNotNull('resolved_at')
            ->select(DB::raw('AVG(TIMESTAMPDIFF(MINUTE, created_at, resolved_at)) as avg_time'))
            ->first()->avg_time;

        $volumeByCategory = Ticket::whereBetween('created_at', [$startDate, $endDate])
            ->join('categories', 'tickets.category_id', '=', 'categories.id')
            ->select('categories.name', DB::raw('count(*) as count'))
            ->groupBy('categories.name')
            ->get();

        $volumeByPriority = Ticket::whereBetween('created_at', [$startDate, $endDate])
            ->select('priority', DB::raw('count(*) as count'))
            ->groupBy('priority')
            ->get();

        $slaCompliance = $this->getSlaCompliance($startDate, $endDate);

        $avgCsat = SatisfactionSurvey::whereBetween('created_at', [$startDate, $endDate])
            ->avg('rating');

        return [
            'avg_first_response_time' => round($avgFirstResponse, 2),
            'avg_resolution_time' => round($avgResolution, 2),
            'volume_by_category' => $volumeByCategory,
            'volume_by_priority' => $volumeByPriority,
            'sla_compliance_rate' => $slaCompliance,
            'avg_csat_score' => round($avgCsat, 2),
            'total_tickets' => (clone $tickets)->count(),
            'resolved_tickets' => (clone $tickets)->whereNotNull('resolved_at')->count(),
        ];
    }

    public function getSlaCompliance($startDate, $endDate)
    {
        $totalResolved = Ticket::whereBetween('created_at', [$startDate, $endDate])
            ->whereNotNull('resolved_at')
            ->count();

        if ($totalResolved === 0) return 100;

        $onTime = Ticket::whereBetween('created_at', [$startDate, $endDate])
            ->whereNotNull('resolved_at')
            ->whereColumn('resolved_at', '<=', 'sla_due_at')
            ->count();

        return round(($onTime / $totalResolved) * 100, 2);
    }

    public function getHeatmapData($startDate, $endDate)
    {
        // Group by day of week and hour
        return Ticket::whereBetween('created_at', [$startDate, $endDate])
            ->select(
                DB::raw('DAYOFWEEK(created_at) as day'),
                DB::raw('HOUR(created_at) as hour'),
                DB::raw('count(*) as count')
            )
            ->groupBy('day', 'hour')
            ->get();
    }

    public function getAgentLeaderboard($startDate, $endDate)
    {
        return DB::table('users')
            ->join('tickets', 'users.id', '=', 'tickets.agent_id')
            ->whereBetween('tickets.created_at', [$startDate, $endDate])
            ->whereNotNull('tickets.resolved_at')
            ->select(
                'users.name',
                DB::raw('count(tickets.id) as tickets_closed'),
                DB::raw('AVG(TIMESTAMPDIFF(MINUTE, tickets.created_at, tickets.resolved_at)) as avg_resolution_time'),
                DB::raw('(SELECT AVG(rating) FROM satisfaction_surveys WHERE ticket_id IN (SELECT id FROM tickets WHERE agent_id = users.id)) as avg_csat')
            )
            ->groupBy('users.id', 'users.name')
            ->orderBy('tickets_closed', 'desc')
            ->limit(10)
            ->get();
    }

    public function getTrendAnalysis($startDate, $endDate)
    {
        // Logic for week-over-week trends
        // This is a bit more complex, let's simplify for now to daily volume in range
        return Ticket::whereBetween('created_at', [$startDate, $endDate])
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('count(*) as count')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }
}
