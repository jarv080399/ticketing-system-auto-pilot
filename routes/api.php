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

// ─── API v1 ───
Route::prefix('v1')->group(function () {

    // ─── Auth (public) ───
    Route::prefix('auth')->group(function () {
        // These will be implemented in Module 1
        // Route::post('/login', [AuthController::class, 'login']);
        // Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
        // Route::post('/reset-password', [AuthController::class, 'resetPassword']);
        // Route::get('/sso/{provider}/redirect', [AuthController::class, 'ssoRedirect']);
        // Route::get('/sso/{provider}/callback', [AuthController::class, 'ssoCallback']);
    });

    // ─── Authenticated Routes ───
    Route::middleware('auth:sanctum')->group(function () {

        // Auth
        Route::get('/auth/me', function (Request $request) {
            return response()->json([
                'data' => $request->user(),
                'message' => 'Success',
                'status' => 200,
            ]);
        });

        // Stubs for future modules:
        // Route::apiResource('tickets', TicketController::class);
        // Route::apiResource('kb/articles', KbArticleController::class);
        // Route::apiResource('assets', AssetController::class);

    });
});
