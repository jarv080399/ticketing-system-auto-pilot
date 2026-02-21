<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Admin\UpdateSettingsRequest;
use App\Services\SettingsService;
use Illuminate\Http\JsonResponse;

class SettingsController extends Controller
{
    public function __construct(
        protected SettingsService $settingsService
    ) {}

    /**
     * Get all system settings grouped by group.
     */
    public function index(): JsonResponse
    {
        return response()->json([
            'data' => $this->settingsService->getAllGrouped(),
            'message' => 'Settings retrieved successfully',
        ]);
    }

    /**
     * Update multiple settings.
     */
    public function update(UpdateSettingsRequest $request): JsonResponse
    {
        $this->settingsService->updateBatch(
            $request->validated('settings'),
            $request->user()->id
        );

        return response()->json([
            'message' => 'Settings updated successfully',
        ]);
    }
}
