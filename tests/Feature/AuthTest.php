<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register(): void
    {
        $this->postJson('/api/auth/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertCreated()
            ->assertExactJson([
                'data' => [
                    'id' => 1,
                    'name' => 'Test User',
                    'email' => 'test@example.com',
                ],
            ]);

        $user = User::query()->firstOrFail();

        $this->assertTrue(Hash::check('password123', $user->password));
    }

    public function test_registration_validates_unique_email_and_password_confirmation(): void
    {
        User::factory()->create(['email' => 'taken@example.com']);

        $this->postJson('/api/auth/register', [
            'name' => 'Test User',
            'email' => 'taken@example.com',
            'password' => 'password123',
            'password_confirmation' => 'different-password',
        ])->assertUnprocessable()
            ->assertJsonValidationErrors(['email', 'password']);
    }

    public function test_user_can_login_and_use_the_jwt(): void
    {
        User::factory()->create([
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $login = $this->postJson('/api/auth/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ])->assertOk()
            ->assertJson([
                'token_type' => 'bearer',
                'expires_in' => 3600,
            ])
            ->assertJsonStructure(['access_token', 'token_type', 'expires_in']);

        $this->withToken($login->json('access_token'))
            ->getJson('/api/products')
            ->assertOk();
    }

    public function test_login_rejects_invalid_credentials(): void
    {
        User::factory()->create([
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $this->postJson('/api/auth/login', [
            'email' => 'test@example.com',
            'password' => 'wrong-password',
        ])->assertUnauthorized()
            ->assertExactJson(['message' => 'Invalid credentials.']);
    }

    public function test_business_endpoints_require_a_valid_jwt(): void
    {
        $this->getJson('/api/products')->assertUnauthorized();
        $this->getJson('/api/purchases')->assertUnauthorized();
        $this->postJson('/api/sales')->assertUnauthorized();

        $this->get('/api/products')
            ->assertUnauthorized()
            ->assertJson(['message' => 'Unauthenticated.']);
    }

    public function test_expired_jwt_is_rejected(): void
    {
        $user = User::factory()->create();
        $token = auth('api')->login($user);
        Auth::forgetGuards();
        $this->travel(61)->minutes();

        $this->withToken($token)
            ->getJson('/api/products')
            ->assertUnauthorized();
    }
}
