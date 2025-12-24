<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
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



    public function SendVerificationEmail(User $user){
        $user->currentAccessToken()->delete();
        $user->currentAccessToken()->create([
            'name' => 'auth-token',
            'token' => Str::random(60),
        ]);


    }

    public function VerifyEmail($email, $token){
        $user = User::where('email', $email)->first();
        if (!$user) {
            return false;
        }

        if($token != $user->email_verification_token){
            return false;
        }

        $user->email_verified_at = now();
        $user->save();
        return true;
    }
}
