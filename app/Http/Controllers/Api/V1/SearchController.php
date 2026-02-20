<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\TicketResource;
use App\Models\Ticket;
use App\Models\KbArticle;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $q = $request->get('q');
        
        if (!$q) {
            return response()->json(['data' => ['tickets' => [], 'knowledge_base' => []]]);
        }

        $tickets = Ticket::with(['category', 'requester'])
            ->where(function ($query) use ($request) {
                if ($request->user()->role === 'user') {
                    $query->where('requester_id', $request->user()->id);
                }
            })
            ->where(function ($query) use ($q) {
                $query->where('title', 'LIKE', "%{$q}%")
                      ->orWhere('ticket_number', 'LIKE', "%{$q}%")
                      ->orWhere('description', 'LIKE', "%{$q}%");
            })
            ->limit(5)
            ->get();

        $kbQuery = KbArticle::with('category')->where('status', 'published');
        if ($request->user() && $request->user()->role === 'user') {
            $kbQuery->where('visibility', 'public');
        }
        
        $knowledgeBase = $kbQuery->where(function($query) use ($q) {
                $query->whereRaw('MATCH(title, content) AGAINST(? IN BOOLEAN MODE)', [$q])
                      ->orWhere('title', 'LIKE', "%{$q}%");
            })
            ->limit(5)
            ->get();

        return response()->json([
            'data' => [
                'tickets' => TicketResource::collection($tickets),
                'knowledge_base' => $knowledgeBase
            ]
        ]);
    }
}
