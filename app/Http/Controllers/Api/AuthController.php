<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Events\UserLoggedOut;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

// ...existing code...
class AuthController extends Controller
{
    // ...existing code...
    public function login(Request $request, AuthService $authService)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required',
        ]);

        $user = $authService->attemptLogin($request->username, $request->password);
        $user->tokens()->delete();
        $token = $user->createToken('auth_token')->plainTextToken;
        $user->load('role.permissions');

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'username' => $user->username,
                    'role' => $user->role ? [
                        'id' => $user->role->id,
                        'name' => $user->role->name,
                        'display_name' => $user->role->display_name,
                    ] : null,
                    'permissions' => $user->role
                        ? $user->role->permissions->pluck('name')->toArray()
                        : [],
                ],
                'token' => $token,
                'token_type' => 'Bearer',
            ],
        ], 200);
    }

    // ...existing code...
    public function logout(Request $request)
    {
        // Revoke current token
        $user = $request->user();
        $user->currentAccessToken()->delete();
        event(new UserLoggedOut($user, $request->ip(), $request->userAgent()));

        return response()->json([
            'success' => true,
            'message' => 'Logout successful',
        ], 200);
    }

    // ...existing code...
    public function me(Request $request)
    {
        $user = $request->user();
        $user->load('role.permissions');

        return response()->json([
            'success' => true,
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'username' => $user->username,
                    'is_active' => $user->is_active,
                    'created_at' => $user->created_at,
                    'role' => $user->role ? [
                        'id' => $user->role->id,
                        'name' => $user->role->name,
                        'display_name' => $user->role->display_name,
                        'description' => $user->role->description,
                    ] : null,
                    'permissions' => $user->role
                        ? $user->role->permissions->pluck('name')->toArray()
                        : [],
                ],
            ],
        ], 200);
    }
}
