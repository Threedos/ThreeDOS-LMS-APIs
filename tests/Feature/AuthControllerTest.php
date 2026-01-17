<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Role;
use App\Models\Council;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Seed roles and council as they are required for User creation
        $this->role = Role::create(['name' => 'Head']);
        $this->council = Council::create(['name' => 'Test Council', 'description' => 'Test Desc']);
    }

    public function test_user_can_login_with_valid_credentials()
    {
        $password = 'password123';
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make($password),
            'role_id' => $this->role->id,
            'council_id' => $this->council->id,
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => $password,
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'user',
                'access_token',
                'expires_in',
            ]);
    }

    public function test_user_cannot_login_with_invalid_credentials()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
            'role_id' => $this->role->id,
            'council_id' => $this->council->id,
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(401)
            ->assertJson(['error' => 'Invalid credentials']);
    }

    public function test_user_can_logout()
    {
        $user = User::factory()->create([
            'role_id' => $this->role->id,
            'council_id' => $this->council->id,
        ]);

        $token = JWTAuth::fromUser($user);

        $response = $this->withHeaders(['Authorization' => "Bearer $token"])
            ->postJson('/api/logout');

        $response->assertStatus(200)
            ->assertJson(['message' => 'Token revoked']);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'revoked' => 1, // Assuming logout sets revoked to true/1
        ]);
    }
}
