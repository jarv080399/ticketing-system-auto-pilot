<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\EscalationTier;
use Illuminate\Http\Request;

class EscalationController extends Controller
{
    public function index()
    {
        return response()->json([
            'data' => EscalationTier::orderBy('level')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'level' => 'required|integer|unique:escalation_tiers,level|min:1',
            'assigned_team' => 'nullable|string',
            'sla_minutes' => 'nullable|integer',
            'notification_channels' => 'nullable|array',
        ]);

        $tier = EscalationTier::create($validated);

        return response()->json(['data' => $tier, 'message' => 'Escalation Tier created.'], 201);
    }

    public function show(EscalationTier $escalationTier)
    {
        return response()->json(['data' => $escalationTier]);
    }

    public function update(Request $request, EscalationTier $escalationTier)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'level' => 'sometimes|required|integer|min:1|unique:escalation_tiers,level,' . $escalationTier->id,
            'assigned_team' => 'nullable|string',
            'sla_minutes' => 'nullable|integer',
            'notification_channels' => 'nullable|array',
        ]);

        $escalationTier->update($validated);

        return response()->json(['data' => $escalationTier, 'message' => 'Escalation Tier updated.']);
    }

    public function destroy(EscalationTier $escalationTier)
    {
        $escalationTier->delete();
        return response()->json(null, 204);
    }
}
