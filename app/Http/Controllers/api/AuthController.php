<?php

namespace App\Http\Controllers\api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\AuthService;


class AuthController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        $result = $this->authService->login($credentials);

        if ($result) {
            return response()->json(['token' => $result['token']]);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    public function logout(Request $request)
    {
        $this->authService->logout($request->user());
        return response()->json(['message' => 'Logged out successfully']);
    }


    public function register(Request $request)
    {
        $result = $this->authService->register($request->all());

        return response()->json(['token' => $result['token']]);
    }

}
