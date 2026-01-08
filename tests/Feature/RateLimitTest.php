<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Cache;

class RateLimitTest extends TestCase
{
    use RefreshDatabase;

    public function test_custom_rate_limiting_enforcement()
    {
        // Setup Role
        // We use try-catch or explicit check properly, but RefreshDatabase handles cleanup.
        // Assuming strict foreign keys, we need a role.
        // Assuming Role model exists and has 'name'.
        // If Role doesn't use UUIDs but User does, verify referencing.
        // For safety, let's create a role.

        $role = null;
        if (class_exists(Role::class)) {
            try {
                $role = Role::create(['name' => 'TestRole']);
            } catch (\Exception $e) {
                // Maybe name unique constraint or something, or it already exists?
                // RefreshDatabase should have cleared it.
                // Maybe Role setup is complex.
                // We will try creating user without role_id first in a safe way if this fails.
            }
        }

        $council = \App\Models\Council::create([
            'name' => 'Test Council',
            'description' => 'A test council'
        ]);

        $userOverride = [
            'role_id' => $role ? $role->id : null,
            'council_id' => $council->id,
        ];

        // Remove nulls
        $userOverride = array_filter($userOverride);

        $user = User::factory()->create($userOverride);

        // Manually set the rate limit on the user instance for the test
        // This simulates a user having a specific limit
        $user->rate_limit = 2;

        $this->actingAs($user, 'api');

        // Clear limits for this user to ensure clean state
        $key = "rate_limit:user:{$user->id}:minute";
        try {
            if (Redis::connection()) {
                Redis::del($key);
            }
        } catch (\Exception $e) {
            // Redis might not be configured
        }
        Cache::forget($key);
        Cache::forget($key . ':timer');

        // Request 1: Allowed
        $response = $this->getJson('/api/notifications');
        $response->assertStatus(200);
        $response->assertHeader('X-RateLimit-Remaining');

        // Request 2: Allowed
        $response = $this->getJson('/api/notifications');
        $response->assertStatus(200);

        // Request 3: Blocked (Limit is 2)
        $response = $this->getJson('/api/notifications');
        $response->assertStatus(429);
        $response->assertJsonFragment(['message' => 'Rate limit exceeded']);

        // Check Retry-After header is present
        $response->assertHeader('Retry-After');
    }
}
