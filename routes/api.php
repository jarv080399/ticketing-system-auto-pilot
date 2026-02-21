<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| All routes are prefixed with /api automatically.
| Version 1 routes are grouped under /api/v1.
|
*/

// ─── Health Check (no auth) ───
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'timestamp' => now()->toIso8601String(),
    ]);
});

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\TicketController;

// ─── API v1 ───
Route::prefix('v1')->group(function () {

    // ─── Auth (public) ───
    Route::prefix('auth')->group(function () {
        Route::post('/login', [AuthController::class, 'login']);
        Route::get('/sso/{provider}/redirect', [AuthController::class, 'redirectToProvider']);
        Route::get('/sso/{provider}/callback', [AuthController::class, 'handleProviderCallback']);
        Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
        Route::post('/reset-password', [AuthController::class, 'resetPassword']);
    });

    // CSAT Survey (Public)
    Route::post('/surveys/{token}', [\App\Http\Controllers\Api\V1\SatisfactionSurveyController::class, 'store']);

    // ─── Authenticated Routes ───
    Route::middleware('auth:sanctum')->group(function () {

        // Auth
        Route::get('/auth/me', [AuthController::class, 'me']);
        Route::post('/auth/logout', [AuthController::class, 'logout']);
        Route::post('/auth/revoke-all', [AuthController::class, 'revokeAll']);

        // Agent Routes
        Route::middleware(['role:agent'])->prefix('agent')->group(function () {
            Route::get('/tickets', [\App\Http\Controllers\Api\V1\TicketController::class, 'index']);
            Route::patch('/tickets/{ticket}', [\App\Http\Controllers\Api\V1\Agent\AgentTicketController::class, 'update']);
            Route::post('/tickets/{ticket}/comments', [\App\Http\Controllers\Api\V1\Agent\CommentController::class, 'store']);
            Route::apiResource('canned-responses', \App\Http\Controllers\Api\V1\Agent\CannedResponseController::class);
        });

        // Admin Routes (Automation & Settings)
        Route::middleware('role:admin')->prefix('admin')->group(function () {
            Route::apiResource('automation-rules', \App\Http\Controllers\Api\V1\Admin\AutomationController::class);
            Route::apiResource('users', \App\Http\Controllers\Api\V1\Admin\UserController::class);
            Route::apiResource('sla-policies', \App\Http\Controllers\Api\V1\Admin\SlaController::class);
            Route::apiResource('escalation-tiers', \App\Http\Controllers\Api\V1\Admin\EscalationController::class);
            Route::apiResource('audit-logs', \App\Http\Controllers\Api\V1\Admin\AuditLogController::class)->only(['index', 'show']);

            // Module 9: System Settings
            Route::get('/settings', [\App\Http\Controllers\Api\V1\Admin\SettingsController::class, 'index']);
            Route::patch('/settings', [\App\Http\Controllers\Api\V1\Admin\SettingsController::class, 'update']);
            Route::apiResource('business-hours', \App\Http\Controllers\Api\V1\Admin\BusinessHourController::class)->only(['index']);
            Route::put('business-hours', [\App\Http\Controllers\Api\V1\Admin\BusinessHourController::class, 'update']);
            Route::apiResource('holidays', \App\Http\Controllers\Api\V1\Admin\HolidayController::class);
            Route::apiResource('custom-fields', \App\Http\Controllers\Api\V1\Admin\CustomFieldController::class);
            Route::get('/system-health', [\App\Http\Controllers\Api\V1\Admin\SystemHealthController::class, 'index']);
            Route::post('/system-health/clear-cache', [\App\Http\Controllers\Api\V1\Admin\SystemHealthController::class, 'clearCache']);
            Route::post('/system-health/restart-workers', [\App\Http\Controllers\Api\V1\Admin\SystemHealthController::class, 'restartWorkers']);
            Route::post('/system-health/run-migrations', [\App\Http\Controllers\Api\V1\Admin\SystemHealthController::class, 'runMigrationsAction']);
            Route::post('/system-health/run-tests', [\App\Http\Controllers\Api\V1\Admin\SystemHealthController::class, 'runTests']);
            Route::get('/activity-log', [\App\Http\Controllers\Api\V1\Admin\ActivityLogController::class, 'index']);
        });

        // Categories
        Route::get('/categories', \App\Http\Controllers\Api\V1\CategoryController::class);

        // Users search (for assignments)
        Route::get('/search/users', \App\Http\Controllers\Api\V1\UserController::class);

        // Tickets
        Route::get('/tickets/my-tickets', [TicketController::class, 'index']);
        Route::post('/tickets/check-duplicate', [TicketController::class, 'checkDuplicate']);
        Route::get('/tickets/{ticket}/survey', [\App\Http\Controllers\Api\V1\SatisfactionSurveyController::class, 'show']);
        Route::post('/tickets/{ticket}/comments', [\App\Http\Controllers\Api\V1\Agent\CommentController::class, 'store']);
        Route::apiResource('tickets', TicketController::class)->except(['index']);

        // Search
        Route::get('/search', \App\Http\Controllers\Api\V1\SearchController::class);

        // Knowledge Base
        Route::prefix('kb')->group(function () {
            // Public KB endpoints (read-only for all authenticated users)
            Route::get('/articles', [\App\Http\Controllers\Api\V1\KbArticleController::class, 'index']);
            Route::get('/suggest', [\App\Http\Controllers\Api\V1\KbArticleController::class, 'suggest']);
            Route::get('/articles/{slug}', [\App\Http\Controllers\Api\V1\KbArticleController::class, 'show']);
            Route::get('/articles/{slug}/versions', [\App\Http\Controllers\Api\V1\KbArticleController::class, 'versions']);
            Route::post('/articles/{slug}/feedback', [\App\Http\Controllers\Api\V1\KbArticleController::class, 'feedback']);
            
            // KB Category Management (Admin/Agent)
            Route::middleware(['role:admin,agent'])->group(function () {
                Route::apiResource('categories', \App\Http\Controllers\Api\V1\KbCategoryController::class);
                
                // Article creation/modification (Admin/Agent)
                Route::post('/articles', [\App\Http\Controllers\Api\V1\KbArticleController::class, 'store']);
                Route::put('/articles/{slug}', [\App\Http\Controllers\Api\V1\KbArticleController::class, 'update']);
                Route::delete('/articles/{slug}', [\App\Http\Controllers\Api\V1\KbArticleController::class, 'destroy']);
            });
        });

        // Asset Tracking
        Route::middleware('role:admin,agent')->group(function () {
            Route::apiResource('assets', \App\Http\Controllers\Api\V1\AssetController::class);
            Route::post('assets/{asset}/assign', [\App\Http\Controllers\Api\V1\AssetController::class, 'assign']);
            Route::post('assets/{asset}/unassign', [\App\Http\Controllers\Api\V1\AssetController::class, 'unassign']);
            Route::get('assets/{asset}/history', [\App\Http\Controllers\Api\V1\AssetController::class, 'history']);

            // Reports
            Route::prefix('reports')->group(function () {
                Route::get('/performance', [\App\Http\Controllers\Api\V1\ReportController::class, 'performance']);
                Route::get('/heatmap', [\App\Http\Controllers\Api\V1\ReportController::class, 'heatmap']);
                Route::get('/trends', [\App\Http\Controllers\Api\V1\ReportController::class, 'trends']);
                Route::get('/agents', [\App\Http\Controllers\Api\V1\ReportController::class, 'agentLeaderboard']);
                Route::post('/export', [\App\Http\Controllers\Api\V1\ReportController::class, 'export']);
            });
        });
        
        // User Data & Notifications
        Route::prefix('user')->group(function () {
            Route::get('/notifications', [\App\Http\Controllers\Api\V1\NotificationController::class, 'index']);
            Route::post('/notifications/{id}/read', [\App\Http\Controllers\Api\V1\NotificationController::class, 'markAsRead']);
            Route::post('/notifications/read-all', [\App\Http\Controllers\Api\V1\NotificationController::class, 'markAllAsRead']);
            Route::get('/notification-preferences', [\App\Http\Controllers\Api\V1\NotificationController::class, 'preferences']);
            Route::patch('/notification-preferences', [\App\Http\Controllers\Api\V1\NotificationController::class, 'updatePreferences']);
            Route::get('/assets', [\App\Http\Controllers\Api\V1\AssetController::class, 'userAssets']);
        });

    });
});
