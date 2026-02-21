<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Artisan;

class SystemHealthController extends Controller
{
    /**
     * Check system health status.
     */
    public function index(): JsonResponse
    {
        $health = [
            'database' => $this->checkDatabase(),
            'redis' => $this->checkRedis(),
            'queue' => $this->checkQueue(),
            'disk' => $this->checkDisk(),
            'migrations' => $this->checkMigrations(),
        ];

        $allOk = !in_array('error', array_column($health, 'status'));

        return response()->json([
            'data' => $health,
            'status' => $allOk ? 'healthy' : 'unhealthy',
            'message' => $allOk ? 'System is healthy' : 'Some services are experiencing issues',
        ]);
    }

    protected function checkDatabase(): array
    {
        try {
            DB::connection()->getPdo();
            return ['status' => 'ok', 'message' => 'Connected'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    protected function checkRedis(): array
    {
        try {
            Redis::connection()->ping();
            return ['status' => 'ok', 'message' => 'Connected'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    protected function checkQueue(): array
    {
        try {
            $count = DB::table('jobs')->count();
            return ['status' => 'ok', 'message' => "$count pending jobs"];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => 'Queue table check failed'];
        }
    }

    protected function checkDisk(): array
    {
        $free = disk_free_space('/');
        $total = disk_total_space('/');
        $usedPercent = round((($total - $free) / $total) * 100, 2);

        return [
            'status' => $usedPercent < 90 ? 'ok' : 'warning',
            'message' => "$usedPercent% used",
            'details' => [
                'free' => round($free / 1024 / 1024 / 1024, 2) . ' GB',
                'total' => round($total / 1024 / 1024 / 1024, 2) . ' GB',
            ]
        ];
    }

    protected function checkMigrations(): array
    {
        Artisan::call('migrate:status');
        $output = Artisan::output();
        
        $pending = str_contains($output, 'Pending');

        return [
            'status' => !$pending ? 'ok' : 'warning',
            'message' => !$pending ? 'All migrations run' : 'Pending migrations found',
        ];
    }
}
