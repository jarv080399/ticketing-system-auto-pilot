<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\SatisfactionSurvey;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SatisfactionSurveyController extends Controller
{
    public function show(Ticket $ticket)
    {
        $survey = $ticket->satisfactionSurvey;
        
        return response()->json(['data' => $survey]);
    }

    public function store(Request $request, $token)
    {
        $survey = SatisfactionSurvey::where('token', $token)->firstOrFail();

        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string'
        ]);

        $survey->update([
            'rating' => $request->rating,
            'comment' => $request->comment,
            'token' => null // Clear token after submission
        ]);

        return response()->json(['message' => 'Thank you for your feedback!']);
    }
}
