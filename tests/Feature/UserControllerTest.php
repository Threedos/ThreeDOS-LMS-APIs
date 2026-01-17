<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Role;
use App\Models\Council;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Enums\RolesEnum;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $headRole;
    protected $instructorRole;
    protected $delegateRole;
    protected $council;

    protected function setUp(): void
    {
        parent::setUp();

        $this->headRole = Role::create(['name' => RolesEnum::Head->value]);
        $this->instructorRole = Role::create(['name' => RolesEnum::Instructor->value]);
        $this->delegateRole = Role::create(['name' => RolesEnum::Delegate->value]);

        $this->council = Council::create(['name' => 'General Council', 'description' => 'Test']);
    }

    private function authenticateUser($user)
    {
        $token = JWTAuth::fromUser($user);
        return $this->withHeaders(['Authorization' => "Bearer $token"]);
    }

    public function test_users_can_be_listed()
    {
        $user = User::factory()->create([
            'role_id' => $this->headRole->id,
            'council_id' => $this->council->id
        ]);
        User::factory()->count(3)->create([
            'role_id' => $this->delegateRole->id,
            'council_id' => $this->council->id
        ]);

        $response = $this->authenticateUser($user)->getJson('/api/users?pageIndex=1&pageSize=10');

        $response->assertStatus(200)
            ->assertJsonStructure(['data']);
    }

    public function test_head_can_create_user()
    {
        $head = User::factory()->create([
            'role_id' => $this->headRole->id,
            'council_id' => $this->council->id
        ]);

        $userData = [
            'name' => 'New User',
            'email' => 'newuser@example.com',
            'password' => 'password123',
            'role_id' => $this->delegateRole->id,
            'council_id' => $this->council->id,
        ];

        $response = $this->authenticateUser($head)->postJson('/api/users', $userData);

        $response->assertStatus(201);
        $this->assertDatabaseHas('users', ['email' => 'newuser@example.com']);
    }

    public function test_delegate_cannot_create_user()
    {
        $delegate = User::factory()->create([
            'role_id' => $this->delegateRole->id,
            'council_id' => $this->council->id
        ]);

        $userData = [
            'name' => 'Unauthorized User',
            'email' => 'unauth@example.com',
            'password' => 'password123',
            'role_id' => $this->delegateRole->id,
            'council_id' => $this->council->id,
        ];

        $response = $this->authenticateUser($delegate)->postJson('/api/users', $userData);

        $response->assertStatus(403);
    }

    public function test_head_can_delete_user()
    {
        $head = User::factory()->create([
            'role_id' => $this->headRole->id,
            'council_id' => $this->council->id
        ]);

        $targetUser = User::factory()->create([
            'role_id' => $this->delegateRole->id,
            'council_id' => $this->council->id
        ]);

        $response = $this->authenticateUser($head)->deleteJson("/api/users/{$targetUser->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('users', ['id' => $targetUser->id]);
    }

    public function test_delegate_can_update_self()
    {
        $delegate = User::factory()->create([
            'role_id' => $this->delegateRole->id,
            'council_id' => $this->council->id
        ]);

        $updateData = ['name' => 'Updated Name'];

        $response = $this->authenticateUser($delegate)->putJson("/api/users/{$delegate->id}", $updateData);

        $response->assertStatus(200);
        $this->assertDatabaseHas('users', ['id' => $delegate->id, 'name' => 'Updated Name']);
    }
}
