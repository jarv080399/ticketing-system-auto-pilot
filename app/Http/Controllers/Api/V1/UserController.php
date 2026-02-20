<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Handle the incoming request to search/list users for agents.
     */
    public function __invoke(Request $request)
    {
        // Simple search for agents/admins to find users to assign assets to
        $q = $request->get('q');
        
        $query = User::query();

        if ($q) {
            $query->where('name', 'like', "%{$q}%")
                  ->orWhere('email', 'like', "%{$q}%");
        }

        return $query->limit(50)->get(['id', 'name', 'email']);
    }
}
