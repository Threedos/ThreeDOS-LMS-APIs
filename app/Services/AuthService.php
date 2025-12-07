<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    public function login(array $credentials)
    {
        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $token = $user->createToken('auth-token')->plainTextToken;
            return ['token' => $token, 'user' => $user];
        }

        return null;
    }

    public function register(array $data)
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role_id' => $data['role_id'] ?? null,
            'council_id' => $data['council_id'] ?? null,
        ]);

        $token = $user->createToken('auth-token')->plainTextToken;

        return ['token' => $token, 'user' => $user];
    }

    public function logout($user)
    {
        $user->currentAccessToken()->delete();
    }
}
