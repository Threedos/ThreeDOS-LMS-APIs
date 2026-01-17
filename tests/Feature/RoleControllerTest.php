<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Role;
use App\Models\Council;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Enums\RolesEnum;

class RoleControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $adminRole;
    protected $council;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminRole = Role::create(['name' => RolesEnum::Head->value]);
        $this->council = Council::create(['name' => 'Test Council', 'description' => 'Desc']);
    }

    private function authenticateUser($user)
    {
        $token = JWTAuth::fromUser($user);
        return $this->withHeaders(['Authorization' => "Bearer $token"]);
    }

    public function test_roles_can_be_listed()
    {
        $user = User::factory()->create([
            'role_id' => $this->adminRole->id,
            'council_id' => $this->council->id
        ]);

        $response = $this->authenticateUser($user)->getJson('/api/roles');

        $response->assertStatus(200);
    }

    public function test_role_can_be_created()
    {
        $user = User::factory()->create([
            'role_id' => $this->adminRole->id,
            'council_id' => $this->council->id
        ]);

        $roleData = ['name' => 'New Role'];

        $response = $this->authenticateUser($user)->postJson('/api/roles', $roleData);

        $response->assertStatus(201);
        $this->assertDatabaseHas('roles', ['name' => 'New Role']);
    }

    public function test_role_creation_requires_name()
    {
        $user = User::factory()->create([
            'role_id' => $this->adminRole->id,
            'council_id' => $this->council->id
        ]);

        $response = $this->authenticateUser($user)->postJson('/api/roles', []);

        $response->assertStatus(422)
            ->assertJsonStructure(['name']);
    }

    public function test_role_can_be_updated()
    {
        $user = User::factory()->create([
            'role_id' => $this->adminRole->id,
            'council_id' => $this->council->id
        ]);

        $role = Role::create(['name' => 'Old Name']);

        $updateData = ['name' => 'New Name'];

        $response = $this->authenticateUser($user)->putJson("/api/roles/{$role->id}", $updateData);

        $response->assertStatus(200);
        $this->assertDatabaseHas('roles', ['id' => $role->id, 'name' => 'New Name']);
    }

    public function test_role_can_be_deleted()
    {
        $user = User::factory()->create([
            'role_id' => $this->adminRole->id,
            'council_id' => $this->council->id
        ]);

        $role = Role::create(['name' => 'To Be Deleted']);

        $response = $this->authenticateUser($user)->deleteJson("/api/roles/{$role->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('roles', ['id' => $role->id]);
    }
}
