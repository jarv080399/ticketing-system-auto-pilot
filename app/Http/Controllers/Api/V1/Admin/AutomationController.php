<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\AutomationRule;
use Illuminate\Http\Request;

class AutomationController extends Controller
{
    public function index()
    {
        return response()->json([
            'data' => AutomationRule::orderBy('priority', 'desc')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'trigger_event' => 'required|in:ticket_created,ticket_updated,sla_approaching,schedule',
            'condition_json' => 'required|array',
            'action_json' => 'required|array',
            'priority' => 'integer',
            'is_active' => 'boolean',
        ]);

        $rule = AutomationRule::create($validated);

        return response()->json(['data' => $rule, 'message' => 'Automation rule created.'], 201);
    }

    public function show(AutomationRule $automationRule)
    {
        return response()->json(['data' => $automationRule]);
    }

    public function update(Request $request, AutomationRule $automationRule)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'trigger_event' => 'sometimes|required|in:ticket_created,ticket_updated,sla_approaching,schedule',
            'condition_json' => 'sometimes|required|array',
            'action_json' => 'sometimes|required|array',
            'priority' => 'integer',
            'is_active' => 'boolean',
        ]);

        $automationRule->update($validated);

        return response()->json(['data' => $automationRule, 'message' => 'Automation rule updated.']);
    }

    public function destroy(AutomationRule $automationRule)
    {
        $automationRule->delete();
        return response()->json(null, 204);
    }
}
