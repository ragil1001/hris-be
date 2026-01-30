<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Handle user login.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Services\AuthService $authService
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request, AuthService $authService)
    {
        $request->validate([
            'username' => 'required|string|max:255',
            'password' => 'required|string',
        ]);

        $user = $authService->attemptLogin(
            trim($request->input('username')),
            $request->input('password')
        );

        // Dispatch standard Laravel Login event
        event(new Login('sanctum', $user, false));

        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'data' => [
                'access_token' => $token,
                'token_type' => 'Bearer',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'username' => $user->username,
                    'role_id' => $user->role_id,
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

    /**
     * Handle user logout.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        $user = $request->user();

        // Dispatch standard Laravel Logout event
        if ($user) {
            event(new Logout('sanctum', $user));
        }

        $user->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully',
        ], 200);
    }

    /**
     * Get current user information.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function me(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'username' => $user->username,
                    'role_id' => $user->role_id,
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
