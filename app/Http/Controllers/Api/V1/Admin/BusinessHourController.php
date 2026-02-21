<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\BusinessHour;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BusinessHourController extends Controller
{
    /**
     * Get all business hours.
     */
    public function index(): JsonResponse
    {
        return response()->json([
            'data' => BusinessHour::orderBy('day_of_week')->get(),
            'message' => 'Business hours retrieved successfully',
        ]);
    }

    /**
     * Update business hours.
     */
    public function update(Request $request): JsonResponse
    {
        $request->validate([
            'hours' => 'required|array',
            'hours.*.day_of_week' => 'required|integer|between:0,6',
            'hours.*.is_working_day' => 'required|boolean',
            'hours.*.start_time' => 'required|date_format:H:i:s',
            'hours.*.end_time' => 'required|date_format:H:i:s',
        ]);

        foreach ($request->hours as $hourData) {
            BusinessHour::updateOrCreate(
                ['day_of_week' => $hourData['day_of_week']],
                [
                    'is_working_day' => $hourData['is_working_day'],
                    'start_time' => $hourData['start_time'],
                    'end_time' => $hourData['end_time'],
                ]
            );
        }

        return response()->json([
            'message' => 'Business hours updated successfully',
        ]);
    }
}
