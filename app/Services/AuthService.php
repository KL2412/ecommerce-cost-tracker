<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use PHPOpenSourceSaver\JWTAuth\JWTGuard;

class AuthService
{
    /**
     * @param  array{name: string, email: string, password: string}  $attributes
     */
    public function register(array $attributes): User
    {
        return User::query()->create($attributes);
    }

    /**
     * @param  array{email: string, password: string}  $credentials
     */
    public function attempt(array $credentials): ?string
    {
        $token = $this->guard()->attempt($credentials);

        return $token === false ? null : $token;
    }

    public function expiresInSeconds(): int
    {
        return $this->guard()->factory()->getTTL() * 60;
    }

    private function guard(): JWTGuard
    {
        $guard = Auth::guard('api');

        if (! $guard instanceof JWTGuard) {
            throw new \LogicException('The API authentication guard must use the JWT driver.');
        }

        return $guard;
    }
}
