<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Holiday;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HolidayController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'data' => Holiday::orderBy('date')->get(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'date' => 'required|date',
            'is_recurring' => 'boolean',
        ]);

        $holiday = Holiday::create($validated);

        return response()->json([
            'data' => $holiday,
            'message' => 'Holiday created successfully',
        ], 210);
    }

    public function update(Request $request, Holiday $holiday): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'string|max:255',
            'date' => 'date',
            'is_recurring' => 'boolean',
        ]);

        $holiday->update($validated);

        return response()->json([
            'data' => $holiday,
            'message' => 'Holiday updated successfully',
        ]);
    }

    public function destroy(Holiday $holiday): JsonResponse
    {
        $holiday->delete();

        return response()->json([
            'message' => 'Holiday deleted successfully',
        ]);
    }
}
