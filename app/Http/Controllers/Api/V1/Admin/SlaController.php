<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\SlaPolicy;
use Illuminate\Http\Request;

class SlaController extends Controller
{
    public function index()
    {
        return response()->json([
            'data' => SlaPolicy::all(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'priority' => 'required|in:low,medium,high,critical|unique:sla_policies,priority',
            'response_time_minutes' => 'required|integer|min:1',
            'resolution_time_minutes' => 'required|integer|min:1',
            'business_hours_only' => 'boolean',
        ]);

        $policy = SlaPolicy::create($validated);

        return response()->json(['data' => $policy, 'message' => 'SLA Policy created.'], 201);
    }

    public function show(SlaPolicy $slaPolicy)
    {
        return response()->json(['data' => $slaPolicy]);
    }

    public function update(Request $request, SlaPolicy $slaPolicy)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'priority' => 'sometimes|required|in:low,medium,high,critical|unique:sla_policies,priority,' . $slaPolicy->id,
            'response_time_minutes' => 'sometimes|required|integer|min:1',
            'resolution_time_minutes' => 'sometimes|required|integer|min:1',
            'business_hours_only' => 'boolean',
        ]);

        $slaPolicy->update($validated);

        return response()->json(['data' => $slaPolicy, 'message' => 'SLA Policy updated.']);
    }

    public function destroy(SlaPolicy $slaPolicy)
    {
        $slaPolicy->delete();
        return response()->json(null, 204);
    }
}
