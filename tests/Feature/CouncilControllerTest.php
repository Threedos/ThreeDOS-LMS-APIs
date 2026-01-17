<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Role;
use App\Models\Council;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Enums\RolesEnum;

class CouncilControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $headRole;
    protected $delegateRole;
    protected $council;

    protected function setUp(): void
    {
        parent::setUp();

        $this->headRole = Role::create(['name' => RolesEnum::Head->value]);
        $this->delegateRole = Role::create(['name' => RolesEnum::Delegate->value]);

        $this->council = Council::create(['name' => 'Existing Council', 'description' => 'Test']);
    }

    private function authenticateUser($user)
    {
        $token = JWTAuth::fromUser($user);
        return $this->withHeaders(['Authorization' => "Bearer $token"]);
    }

    public function test_councils_can_be_listed()
    {
        $user = User::factory()->create([
            'role_id' => $this->delegateRole->id,
            'council_id' => $this->council->id
        ]);

        $response = $this->authenticateUser($user)->getJson('/api/councils?pageIndex=1&pageSize=10');

        $response->assertStatus(200)
            ->assertJsonStructure(['data']);
    }

    public function test_head_can_create_council()
    {
        $head = User::factory()->create([
            'role_id' => $this->headRole->id,
            'council_id' => $this->council->id
        ]);

        $councilData = [
            'name' => 'New Council',
            'description' => 'New Description',
        ];

        $response = $this->authenticateUser($head)->postJson('/api/councils', $councilData);

        $response->assertStatus(201);
        $this->assertDatabaseHas('councils', ['name' => 'New Council']);
    }

    public function test_delegate_cannot_create_council()
    {
        $delegate = User::factory()->create([
            'role_id' => $this->delegateRole->id,
            'council_id' => $this->council->id
        ]);

        $councilData = [
            'name' => 'Unauthorized Council',
            'description' => 'Desc',
        ];

        $response = $this->authenticateUser($delegate)->postJson('/api/councils', $councilData);

        $response->assertStatus(403);
    }

    public function test_head_can_update_own_council()
    {
        $head = User::factory()->create([
            'role_id' => $this->headRole->id,
            'council_id' => $this->council->id
        ]);

        $updateData = ['name' => 'Updated Council Name'];

        $response = $this->authenticateUser($head)->putJson("/api/councils/{$this->council->id}", $updateData);

        $response->assertStatus(200);
        $this->assertDatabaseHas('councils', ['id' => $this->council->id, 'name' => 'Updated Council Name']);
    }

    public function test_head_cannot_update_other_council()
    {
        $otherCouncil = Council::create(['name' => 'Other Council', 'description' => 'Desc']);
        $head = User::factory()->create([
            'role_id' => $this->headRole->id,
            'council_id' => $this->council->id // Different from otherCouncil
        ]);

        $updateData = ['name' => 'Hacked Name'];

        $response = $this->authenticateUser($head)->putJson("/api/councils/{$otherCouncil->id}", $updateData);

        $response->assertStatus(403);
    }
}
