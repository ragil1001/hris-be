<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    /**
     * Attempt to authenticate the user.
     *
     * @param string $username
     * @param string $password
     * @return \App\Models\User
     * @throws \Illuminate\Validation\ValidationException
     */
    public function attemptLogin(string $username, string $password): User
    {
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

        return $user;
    }
}
