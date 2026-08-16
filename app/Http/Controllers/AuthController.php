<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    public function __construct(private readonly AuthService $authService) {}

    public function register(RegisterRequest $request): JsonResponse
    {
        $user = $this->authService->register($request->safe()->only([
            'name',
            'email',
            'password',
        ]));

        return response()->json([
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
        ], Response::HTTP_CREATED);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $token = $this->authService->attempt($request->validated());

        if ($token === null) {
            return response()->json([
                'message' => 'Invalid credentials.',
            ], Response::HTTP_UNAUTHORIZED);
        }

        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => $this->authService->expiresInSeconds(),
        ]);
    }
}
