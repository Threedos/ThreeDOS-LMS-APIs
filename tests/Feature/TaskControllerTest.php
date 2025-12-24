<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Services\TaskService;
use Mockery\MockInterface;

class TaskControllerTest extends TestCase
{
    /**
     * Test index method returns all tasks.
     */
    public function test_index_returns_all_tasks()
    {
        $mockTasks = [
            ['id' => 1, 'name' => 'Task 1'],
            ['id' => 2, 'name' => 'Task 2']
        ];

        $this->mock(TaskService::class, function (MockInterface $mock) use ($mockTasks) {
            $mock->shouldReceive('getAllTasks')
                ->once()
                ->andReturn($mockTasks);
        });

        $response = $this->getJson('/api/tasks');

        $response->assertStatus(200)
            ->assertJson($mockTasks);
    }

    /**
     * Test store method creates a task.
     */
    public function test_store_creates_new_task()
    {
        $taskData = ['name' => 'New Task'];
        $createdTask = ['id' => 1, 'name' => 'New Task'];

        $this->mock(TaskService::class, function (MockInterface $mock) use ($taskData, $createdTask) {
            $mock->shouldReceive('createTask')
                ->once()
                ->with($taskData)
                ->andReturn($createdTask);
        });

        $response = $this->postJson('/api/tasks', $taskData);

        $response->assertStatus(201)
            ->assertJson($createdTask);
    }

    /**
     * Test show method returns a specific task.
     */
    public function test_show_returns_task()
    {
        $taskId = '1';
        $task = ['id' => 1, 'name' => 'Task 1'];

        $this->mock(TaskService::class, function (MockInterface $mock) use ($taskId, $task) {
            $mock->shouldReceive('getTaskById')
                ->once()
                ->with($taskId)
                ->andReturn($task);
        });

        $response = $this->getJson("/api/tasks/{$taskId}");

        $response->assertStatus(200)
            ->assertJson($task);
    }

    /**
     * Test update method updates a task.
     */
    public function test_update_updates_task()
    {
        $taskId = '1';
        $updateData = ['name' => 'Updated Task'];

        $this->mock(TaskService::class, function (MockInterface $mock) use ($taskId, $updateData) {
            $mock->shouldReceive('updateTask')
                ->once()
                ->with($taskId, $updateData);
        });

        $response = $this->putJson("/api/tasks/{$taskId}", $updateData);

        $response->assertStatus(200)
            ->assertJson(['message' => 'Task updated successfully']);
    }

    /**
     * Test destroy method deletes a task.
     */
    public function test_destroy_deletes_task()
    {
        $taskId = '1';

        $this->mock(TaskService::class, function (MockInterface $mock) use ($taskId) {
            $mock->shouldReceive('deleteTask')
                ->once()
                ->with($taskId);
        });

        $response = $this->deleteJson("/api/tasks/{$taskId}");

        $response->assertStatus(200)
            ->assertJson(['message' => 'Task deleted successfully']);
    }
}
