<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class AuthService
{
    public function login(array $credentials)
    {
        $user = User::where('email', $credentials['email'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return null;
        }

        $token = JWTAuth::fromUser($user);

        $user->update([
            'access_token' => $token,
            'revoked' => false,
            'last_active' => now(),
            'status' => 'active',
        ]);

        return [
            'user' => $user,
            'access_token' => $token,
            'expires_in' => JWTAuth::factory()->getTTL() * 60,
        ];
    }

    public function logout($user)
    {
        if ($user) {
            $user->update([
                'revoked' => true,
                'access_token' => null,
                'last_active' => now(),
                'status' => 'inactive',
            ]);
        }
    }

    public function forgetPassword(string $email)
    {
        $user = User::where('email', $email)->first();
        if (!$user) {
            return 'USER_NOT_FOUND';
        }

        return Password::sendResetLink(['email' => $email]);
    }

    public function resetPassword(array $data)
    {
        return Password::reset(
            $data,
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                    'access_token' => null,
                    'revoked' => true,
                ])->save();
            }
        );
    }
}
