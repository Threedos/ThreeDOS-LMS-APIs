<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\AuthService;
use App\Http\Requests\UserRequests\LoginRequest;
use App\Http\Resources\UserProfileResource;
use Illuminate\Support\Facades\Password;

class AuthController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Login
     *
     * Authenticate with email and password to obtain a JWT Bearer token.
     *
     * @unauthenticated
     * @tags Auth
     * @response 200 scenario="Success" {"status": "success", "message": "Login successfully", "data": {"user_name": "John Doe", "role": "Admin", "access_token": "eyJ...", "expires_in": 3600}}
     * @response 401 scenario="Invalid credentials" {"status": "error", "message": "Invalid credentials"}
     */
    public function login(LoginRequest $request)
    {
        $result = $this->authService->login($request->only('email', 'password'));

        if (!$result) {
            return $this->unauthorizedResponse('Invalid credentials');
        }

        return $this->successResponse([
            'user_name' => $result['user']->name,
            'role' => $result['user']->role->name,
            'access_token' => $result['access_token'],
            'expires_in' => $result['expires_in'],
        ], 'Login successfully');
    }

    /**
     * Logout
     *
     * Revoke the current JWT token and log the user out.
     *
     * @tags Auth
     * @response 200 scenario="Success" {"status": "success", "message": "Logout successfully", "data": null}
     */
    public function logout(Request $request)
    {
        $this->authService->logout(auth('api')->user());
        return $this->successResponse(null, 'Logout successfully');
    }

    /**
     * Forgot Password
     *
     * Send a password reset link to the provided email address.
     *
     * @unauthenticated
     * @tags Auth
     * @response 200 scenario="Reset link sent" {"status": "success", "message": "Success", "data": null}
     * @response 404 scenario="User not found" {"status": "error", "message": "User not found"}
     * @response 422 scenario="Failed" {"status": "error", "message": "Failed to send reset link"}
     */
    public function forgetPassword(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        $status = $this->authService->forgetPassword($request->email);

        if ($status === 'USER_NOT_FOUND') {
            return $this->errorResponse('User not found', 404);
        }

        return $status === Password::RESET_LINK_SENT
            ? $this->successResponse(null, 'Success')
            : $this->errorResponse('Failed to send reset link', 422);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $status = $this->authService->resetPassword($request->only(
            'email',
            'password',
            'password_confirmation',
            'token'
        ));

        return $status === Password::PASSWORD_RESET
            ? $this->successResponse(null, 'Password reset successfully')
            : $this->errorResponse('Invalid token', 422);
    }

    /**
     * Current User
     *
     * Retrieve the currently authenticated user's profile.
     *
     * @tags Auth
     * @response 200 scenario="Success" {"status": "success", "message": "User retrieved successfully", "data": {"id": 1, "name": "John Doe", "email": "john@example.com"}}
     */
    public function me()
    {
        $user = auth('api')->user();
        return $this->successResponse(new UserProfileResource($user), 'User retrieved successfully');
    }
}
