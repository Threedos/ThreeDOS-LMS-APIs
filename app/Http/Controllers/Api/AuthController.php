<?php

namespace App\Http\Controllers\Api;
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
            return $this->unauthorizedResponse('Invalid credentials');
        }

        // Create JWT token
        $token = JWTAuth::fromUser($user);

        // Store token in database
        $user->update([
            'access_token' => $token,
            'revoked' => false,
        ]);
        $user = new UserProfileResource($user);
        return $this->successResponse([
            'user' => $user,
            'access_token' => $token,
            'expires_in' => JWTAuth::factory()->getTTL() * 60,
        ], 'Login successfully');
    }

    // Revoke token manually
    public function logout(Request $request)
    {
        $user = auth()->user();
        $user->update([
            'revoked' => true,
            'access_token' => null
        ]);

        return $this->successResponse(null, 'Logout successfully');
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
            ? $this->successResponse(null, 'Success')
            : $this->successResponse(null, 'Success');
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
            ? $this->successResponse(null, 'Password reset successfully')
            : $this->errorResponse('Invalid token', 422);
    }
    
    public function me()
    {
        $user = auth()->user();
        $user = new UserProfileResource($user);
        return $this->successResponse($user, 'User retrieved successfully');
    }
    // public function register(Request $request)
    // {
    //     $result = $this->authService->register($request->all());

    //     return response()->json(['token' => $result['token']]);
    // }


}
