<?php

namespace App\Http\Controllers\api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\AuthService;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Requests\UserRequests\LoginRequest;
use App\Http\Resources\UserProfileResource;

class AuthController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');

        $user = User::where('email', $credentials['email'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }

        // Create JWT token
        $token = JWTAuth::fromUser($user);

        // Store token in database
        $user->update([
            'access_token' => $token,
            'revoked' => false,
        ]);
        $user = new UserProfileResource($user);
        return response()->json([
            'user' => $user,
            'access_token' => $token,
            'expires_in' => JWTAuth::factory()->getTTL() * 60,
            // 'role' => $user->role->name
        ]);
    }

    // Revoke token manually
    public function logout(Request $request)
    {
        $user = auth()->user();
        $user->update([
            'revoked' => true,
            'access_token' => null
        ]);

        return response()->json(['message' => 'Token revoked']);
    }

    // public function register(Request $request)
    // {
    //     $result = $this->authService->register($request->all());

    //     return response()->json(['token' => $result['token']]);
    // }


}
