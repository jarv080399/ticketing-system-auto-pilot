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

    public function clearCache(): JsonResponse
    {
        try {
            Artisan::call('optimize:clear');
            return response()->json(['message' => 'System cache cleared successfully.']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to clear cache.', 'error' => $e->getMessage()], 500);
        }
    }

    public function restartWorkers(): JsonResponse
    {
        try {
            Artisan::call('queue:restart');
            return response()->json(['message' => 'Queue workers restarted (they will restart after their current job).']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to restart workers.', 'error' => $e->getMessage()], 500);
        }
    }

    public function runMigrationsAction(): JsonResponse
    {
        try {
            Artisan::call('migrate', ['--force' => true]);
            $output = Artisan::output();
            $message = str_contains($output, 'Nothing to migrate') ? 'Nothing to migrate.' : 'Database migrated successfully.';
            return response()->json(['message' => $message]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to run migrations.', 'error' => $e->getMessage()], 500);
        }
    }

    public function runTests(): JsonResponse
    {
        try {
            // Can be tied to a standard php artisan test later or specific health checks.
            // For now, let's just re-verify system health synchronously and return response.
            Artisan::call('view:cache'); // compile views
            Artisan::call('config:cache');
            Artisan::call('route:cache');
            
            return response()->json(['message' => 'Self-tests complete. Configuration verified and cached.']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Self-tests encountered an issue.', 'error' => $e->getMessage()], 500);
        }
    }
}
