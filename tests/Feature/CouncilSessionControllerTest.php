<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Role;
use App\Models\Council;
use App\Models\CouncilSession;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Enums\RolesEnum;
use Illuminate\Support\Carbon;

class CouncilSessionControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $adminRole; // Using Head as admin-like for sessions
    protected $council;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminRole = Role::create(['name' => RolesEnum::Head->value]);
        $this->council = Council::create(['name' => 'General Council', 'description' => 'Desc']);
    }

    private function authenticateUser($user)
    {
        $token = JWTAuth::fromUser($user);
        return $this->withHeaders(['Authorization' => "Bearer $token"]);
    }

    public function test_sessions_can_be_listed()
    {
        $user = User::factory()->create([
            'role_id' => $this->adminRole->id,
            'council_id' => $this->council->id
        ]);

        CouncilSession::factory()->count(3)->create([
            'council_id' => $this->council->id
        ]);

        $response = $this->authenticateUser($user)->getJson('/api/sessions?pageIndex=1&pageSize=10');

        $response->assertStatus(200)
            ->assertJsonStructure(['data']);
    }

    public function test_session_can_be_created()
    {
        $user = User::factory()->create([
            'role_id' => $this->adminRole->id,
            'council_id' => $this->council->id
        ]);

        $sessionData = [
            'title' => 'New Session',
            'date' => Carbon::now()->addDays(1)->format('Y-m-d H:i:s'),
            'description' => 'Session Desc',
            'material' => 'http://example.com/material',
            'council_id' => $this->council->id
        ];

        $response = $this->authenticateUser($user)->postJson('/api/sessions', $sessionData);

        $response->assertStatus(201);
        $this->assertDatabaseHas('CouncilSession', ['title' => 'New Session']);
    }

    public function test_session_can_be_updated()
    {
        $user = User::factory()->create([
            'role_id' => $this->adminRole->id,
            'council_id' => $this->council->id
        ]);

        $session = CouncilSession::factory()->create([
            'council_id' => $this->council->id
        ]);

        $updateData = ['title' => 'Updated Session Title'];

        $response = $this->authenticateUser($user)->putJson("/api/sessions/{$session->id}", $updateData);

        $response->assertStatus(200);
        $this->assertDatabaseHas('CouncilSession', ['id' => $session->id, 'title' => 'Updated Session Title']);
    }

    public function test_session_can_be_deleted()
    {
        $user = User::factory()->create([
            'role_id' => $this->adminRole->id,
            'council_id' => $this->council->id
        ]);

        $session = CouncilSession::factory()->create([
            'council_id' => $this->council->id
        ]);

        $response = $this->authenticateUser($user)->deleteJson("/api/sessions/{$session->id}");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('CouncilSession', ['id' => $session->id]);
    }
}
