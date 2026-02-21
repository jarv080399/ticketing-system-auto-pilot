<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of the users.
     */
    public function index(Request $request): JsonResponse
    {
        $query = User::query();

        if ($request->has('q')) {
            $q = $request->get('q');
            $query->where(function ($builder) use ($q) {
                $builder->where('name', 'like', "%{$q}%")
                        ->orWhere('email', 'like', "%{$q}%");
            });
        }

        if ($request->has('role')) {
            $query->where('role', $request->get('role'));
        }

        if ($request->has('department')) {
            $query->where('department', $request->get('department'));
        }

        $users = $query->latest()->paginate($request->get('per_page', 15));

        return response()->json($users);
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'role' => ['required', Rule::in(['user', 'agent', 'admin'])],
            'department' => 'nullable|string|max:255',
            'manager_id' => 'nullable|exists:users,id',
            'timezone' => 'nullable|string',
            'locale' => 'nullable|string|max:10',
        ]);

        $validated['password'] = Hash::make(Str::random(16));

        $user = User::create($validated);

        return response()->json([
            'data' => $user,
            'message' => 'User created successfully',
            'status' => 201,
        ], 201);
    }

    /**
     * Display the specified user.
     */
    public function show(User $user): JsonResponse
    {
        return response()->json([
            'data' => $user->load(['manager', 'directReports']),
            'status' => 200,
        ]);
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, User $user): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => ['sometimes', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'role' => ['sometimes', Rule::in(['user', 'agent', 'admin'])],
            'department' => 'nullable|string|max:255',
            'manager_id' => 'nullable|exists:users,id',
            'timezone' => 'nullable|string',
            'locale' => 'nullable|string|max:10',
        ]);

        $user->update($validated);

        return response()->json([
            'data' => $user,
            'message' => 'User updated successfully',
            'status' => 200,
        ]);
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user): JsonResponse
    {
        $user->delete();

        return response()->json([
            'message' => 'User soft-deleted successfully',
            'status' => 200,
        ]);
    }
}
