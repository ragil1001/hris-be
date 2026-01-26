<?php

namespace App\Services;

use App\Models\User;
use App\Events\UserLoggedIn;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

/**
 * Service class for authentication logic.
 *
 * @package App\Services
 */
class AuthService
{
    /**
     * Attempt to authenticate user by username and password.
     *
     * @param string $username
     * @param string $password
     * @return User
     * @throws ValidationException
     */
    public function attemptLogin(string $username, string $password): User
    {
        // Sanitize input
        $username = trim($username);
        $password = trim($password);

        $user = User::where('username', $username)->first();

        if (!$user || !Hash::check($password, $user->password)) {
            throw ValidationException::withMessages([
                'username' => ['The provided credentials are incorrect.'],
            ]);
        }

        if (!$user->is_active) {
            throw ValidationException::withMessages([
                'username' => ['Your account has been deactivated.'],
            ]);
        }

        // Fire login event
        event(new UserLoggedIn($user, request()->ip(), request()->userAgent()));
        return $user;
    }
}
