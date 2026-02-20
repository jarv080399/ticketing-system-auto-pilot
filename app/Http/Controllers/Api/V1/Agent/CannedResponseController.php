<?php

namespace App\Http\Controllers\Api\V1\Agent;

use App\Http\Controllers\Controller;
use App\Models\CannedResponse;
use Illuminate\Http\Request;

class CannedResponseController extends Controller
{
    /**
     * Display a listing of canned responses.
     */
    public function index(Request $request)
    {
        $responses = CannedResponse::where('is_shared', true)
            ->orWhere('created_by', $request->user()->id)
            ->get();

        return response()->json(['data' => $responses]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'category' => 'nullable|string',
            'is_shared' => 'boolean',
        ]);

        $validated['created_by'] = $request->user()->id;

        $response = CannedResponse::create($validated);

        return response()->json(['data' => $response], 201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CannedResponse $cannedResponse)
    {
        if ($cannedResponse->created_by !== $request->user()->id && $request->user()->role !== 'admin') {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'body' => 'sometimes|string',
            'category' => 'nullable|string',
            'is_shared' => 'sometimes|boolean',
        ]);

        $cannedResponse->update($validated);

        return response()->json(['data' => $cannedResponse]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, CannedResponse $cannedResponse)
    {
        if ($cannedResponse->created_by !== $request->user()->id && $request->user()->role !== 'admin') {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $cannedResponse->delete();

        return response()->json(null, 204);
    }
}
