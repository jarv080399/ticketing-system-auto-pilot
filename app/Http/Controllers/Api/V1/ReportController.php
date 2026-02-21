<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Repositories\ReportRepository;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReportController extends Controller
{
    protected $reportRepository;

    public function __construct(ReportRepository $reportRepository)
    {
        $this->reportRepository = $reportRepository;
    }

    public function performance(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->subDays(30)->toDateString());
        $endDate = $request->get('end_date', Carbon::now()->toDateString());

        $metrics = $this->reportRepository->getPerformanceMetrics($startDate, $endDate);

        return response()->json(['data' => $metrics]);
    }

    public function heatmap(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->subDays(30)->toDateString());
        $endDate = $request->get('end_date', Carbon::now()->toDateString());

        $heatmap = $this->reportRepository->getHeatmapData($startDate, $endDate);

        return response()->json(['data' => $heatmap]);
    }

    public function trends(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->subDays(30)->toDateString());
        $endDate = $request->get('end_date', Carbon::now()->toDateString());

        $trends = $this->reportRepository->getTrendAnalysis($startDate, $endDate);

        return response()->json(['data' => $trends]);
    }

    public function agentLeaderboard(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->subDays(30)->toDateString());
        $endDate = $request->get('end_date', Carbon::now()->toDateString());

        $leaderboard = $this->reportRepository->getAgentLeaderboard($startDate, $endDate);

        return response()->json(['data' => $leaderboard]);
    }

    public function export(Request $request)
    {
        // Placeholder for Excel export
        return response()->json(['message' => 'Export job dispatched', 'download_url' => '#']);
    }
}
