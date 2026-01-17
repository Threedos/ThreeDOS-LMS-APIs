<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Role;
use App\Models\Council;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Enums\RolesEnum;
use Illuminate\Support\Carbon;

class TaskControllerTest extends TestCase
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

        $this->council = Council::create(['name' => 'General Council', 'description' => 'Desc']);
    }

    private function authenticateUser($user)
    {
        $token = JWTAuth::fromUser($user);
        return $this->withHeaders(['Authorization' => "Bearer $token"]);
    }

    public function test_tasks_can_be_listed()
    {
        $user = User::factory()->create([
            'role_id' => $this->headRole->id,
            'council_id' => $this->council->id
        ]);

        Task::factory()->count(3)->create([
            'council_id' => $this->council->id
        ]);

        // Requires pagination params
        $response = $this->authenticateUser($user)->getJson('/api/tasks?pageIndex=1&pageSize=10');

        $response->assertStatus(200)
            ->assertJsonStructure([
                '*' => ['id', 'title', 'council_id']
            ]);
    }

    public function test_head_can_create_task()
    {
        $head = User::factory()->create([
            'role_id' => $this->headRole->id,
            'council_id' => $this->council->id
        ]);

        $taskData = [
            'title' => 'New Task',
            'description' => 'Task Description',
            'due_date' => Carbon::now()->addDays(2)->toDateTimeString(),
            'status' => 'Pending',
            'council_id' => $this->council->id,
            'CouncilSession_id' => null
        ];

        $response = $this->authenticateUser($head)->postJson('/api/tasks', $taskData);

        $response->assertStatus(201);
        $this->assertDatabaseHas('tasks', ['title' => 'New Task']);
    }

    public function test_delegate_cannot_create_task()
    {
        $delegate = User::factory()->create([
            'role_id' => $this->delegateRole->id,
            'council_id' => $this->council->id
        ]);

        $taskData = [
            'title' => 'Unauthorized Task',
            'description' => 'Desc',
            'due_date' => Carbon::now()->toDateTimeString(),
            'status' => 'Pending',
            'council_id' => $this->council->id
        ];

        $response = $this->authenticateUser($delegate)->postJson('/api/tasks', $taskData);

        $response->assertStatus(403);
    }
}
