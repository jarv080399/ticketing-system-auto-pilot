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
        $role = $request->get('role');
        
        $query = User::withCount('assets');

        if ($q) {
            $query->where(function ($builder) use ($q) {
                $builder->where('name', 'like', "%{$q}%")
                        ->orWhere('email', 'like', "%{$q}%");
            });
        }
        
        if ($role) {
            $query->where('role', $role);
        }

        if ($request->has('page')) {
            return response()->json($query->paginate(15));
        }

        return response()->json([
            'data' => $query->limit(50)->get(['id', 'name', 'email', 'role'])
        ]);
    }
}
