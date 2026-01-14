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
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

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

public function forgetPassword(Request $request)
{
    $request->validate([
        'email' => 'required|email',
    ]);

    $status = Password::sendResetLink(
        $request->only('email')
    );

    // Do NOT reveal if email exists (security best practice)
    return $status === Password::RESET_LINK_SENT
        ? response()->json(['message' => 'Reset link sent to email'])
        : response()->json(['message' => 'Reset link sent to email']);
}

    


public function resetPassword(Request $request)
{
    $request->validate([
        'token' => 'required',
        'email' => 'required|email',
        'password' => 'required|min:8|confirmed',
    ]);

    $status = Password::reset(
        $request->only(
            'email',
            'password',
            'password_confirmation',
            'token'
        ),
        function (User $user, string $password) {
            $user->forceFill([
                'password' => Hash::make($password),
                'remember_token' => Str::random(60),
                'access_token' => null,
                'revoked' => true, // revoke JWT sessions
            ])->save();
        }
    );

    return $status === Password::PASSWORD_RESET
        ? response()->json(['message' => 'Password reset successfully'])
        : response()->json(['error' => 'Invalid or expired token'], 422);
}

    // public function register(Request $request)
    // {
    //     $result = $this->authService->register($request->all());

    //     return response()->json(['token' => $result['token']]);
    // }


}
