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
    });

    // ─── Authenticated Routes ───
    Route::middleware('auth:sanctum')->group(function () {

        // Auth
        Route::get('/auth/me', [AuthController::class, 'me']);
        Route::post('/auth/logout', [AuthController::class, 'logout']);

        // Agent Routes
        Route::middleware(['auth:sanctum', 'role:agent'])->prefix('agent')->group(function () {
            Route::get('/tickets', [TicketController::class, 'index']); // I'll update TicketController to handle agent index
            Route::patch('/tickets/{ticket}', [\App\Http\Controllers\Api\V1\Agent\AgentTicketController::class, 'update']);
            Route::post('/tickets/{ticket}/comments', [\App\Http\Controllers\Api\V1\Agent\CommentController::class, 'store']);

            Route::apiResource('canned-responses', \App\Http\Controllers\Api\V1\Agent\CannedResponseController::class);
        });

        // Categories
        Route::get('/categories', \App\Http\Controllers\Api\V1\CategoryController::class);

        // Tickets
        Route::get('/tickets/my-tickets', [TicketController::class, 'index']);
        Route::post('/tickets/check-duplicate', [TicketController::class, 'checkDuplicate']);
        Route::apiResource('tickets', TicketController::class)->except(['index']);

        // Search
        Route::get('/search', \App\Http\Controllers\Api\V1\SearchController::class);

    });
});
