<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Role;
use App\Models\Council;
use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Enums\RolesEnum;

class TeamControllerTest extends TestCase
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
        $this->council = Council::create(['name' => 'General Council', 'description' => 'Desc']);
    }

    private function authenticateUser($user)
    {
        $token = JWTAuth::fromUser($user);
        return $this->withHeaders(['Authorization' => "Bearer $token"]);
    }

    public function test_teams_can_be_listed()
    {
        $user = User::factory()->create([
            'role_id' => $this->headRole->id,
            'council_id' => $this->council->id
        ]);

        Team::factory()->count(3)->create([
            'council_id' => $this->council->id
        ]);

        $response = $this->authenticateUser($user)->getJson('/api/teams');

        $response->assertStatus(200);
    }

    public function test_team_can_be_created()
    {
        $user = User::factory()->create([
            'role_id' => $this->headRole->id,
            'council_id' => $this->council->id
        ]);

        $teamData = [
            'team_number' => '101',
            'council_id' => $this->council->id
        ];

        $response = $this->authenticateUser($user)->postJson('/api/teams', $teamData);

        $response->assertStatus(201);
        $this->assertDatabaseHas('teams', ['team_number' => '101']);
    }

    public function test_team_can_be_updated()
    {
        $user = User::factory()->create([
            'role_id' => $this->headRole->id,
            'council_id' => $this->council->id
        ]);

        $team = Team::factory()->create(['council_id' => $this->council->id]);

        $updateData = [
            'team_number' => '102',
            'council_id' => $this->council->id
        ];

        $response = $this->authenticateUser($user)->putJson("/api/teams/{$team->id}", $updateData);

        $response->assertStatus(200);
        $this->assertDatabaseHas('teams', ['id' => $team->id, 'team_number' => '102']);
    }

    public function test_team_can_be_deleted()
    {
        $user = User::factory()->create([
            'role_id' => $this->headRole->id,
            'council_id' => $this->council->id
        ]);

        $team = Team::factory()->create(['council_id' => $this->council->id]);

        $response = $this->authenticateUser($user)->deleteJson("/api/teams/{$team->id}");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('teams', ['id' => $team->id]);
    }
}
