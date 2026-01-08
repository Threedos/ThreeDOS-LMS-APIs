<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Role;
use App\Models\Council;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Cache;

class RateLimitComprehensiveTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Clear all rate limit keys before each test
        try {
            if (Redis::connection()) {
                Redis::flushdb();
            }
        } catch (\Exception $e) {
            // Redis might not be configured
        }
        Cache::flush();
    }

    public function test_rate_limit_headers_are_present()
    {
        $user = $this->createTestUser();
        $user->rate_limit = 10;

        $this->actingAs($user, 'api');

        $response = $this->getJson('/api/notifications');

        $response->assertStatus(200);
        $response->assertHeader('X-RateLimit-Limit');
        $response->assertHeader('X-RateLimit-Remaining');
        $response->assertHeader('X-RateLimit-Reset');

        // Verify the limit header matches user's rate limit
        $this->assertEquals(10, $response->headers->get('X-RateLimit-Limit'));
    }

    public function test_rate_limit_remaining_decrements()
    {
        $user = $this->createTestUser();
        $user->rate_limit = 5;

        $this->actingAs($user, 'api');

        // First request
        $response1 = $this->getJson('/api/notifications');
        $remaining1 = $response1->headers->get('X-RateLimit-Remaining');

        // Second request
        $response2 = $this->getJson('/api/notifications');
        $remaining2 = $response2->headers->get('X-RateLimit-Remaining');

        // Remaining should decrement
        $this->assertLessThan($remaining1, $remaining2);
    }

    public function test_rate_limit_blocks_after_limit_exceeded()
    {
        $user = $this->createTestUser();
        $user->rate_limit = 3;

        $this->actingAs($user, 'api');

        // Make requests up to the limit
        for ($i = 0; $i < 3; $i++) {
            $response = $this->getJson('/api/notifications');
            $response->assertStatus(200);
        }

        // Next request should be blocked
        $response = $this->getJson('/api/notifications');
        $response->assertStatus(429);
        $response->assertJson([
            'message' => 'Rate limit exceeded',
            'tier' => 'minute',
        ]);
    }

    public function test_rate_limit_response_includes_retry_after()
    {
        $user = $this->createTestUser();
        $user->rate_limit = 1;

        $this->actingAs($user, 'api');

        // Exhaust the limit
        $this->getJson('/api/notifications')->assertStatus(200);

        // Get blocked response
        $response = $this->getJson('/api/notifications');
        $response->assertStatus(429);
        $response->assertHeader('Retry-After');
        $response->assertJsonStructure([
            'message',
            'tier',
            'limit',
            'retry_after',
        ]);

        // Verify retry_after is a positive number
        $retryAfter = $response->json('retry_after');
        $this->assertGreaterThan(0, $retryAfter);
    }

    public function test_unauthenticated_requests_bypass_rate_limiting()
    {
        // Don't authenticate
        $response = $this->getJson('/api/notifications');

        // Should get 401 Unauthorized, not 429 Rate Limited
        $response->assertStatus(401);
    }

    public function test_different_users_have_separate_rate_limits()
    {
        $user1 = $this->createTestUser();
        $user1->rate_limit = 2;

        $user2 = $this->createTestUser();
        $user2->rate_limit = 2;

        // User 1 exhausts their limit
        $this->actingAs($user1, 'api');
        $this->getJson('/api/notifications')->assertStatus(200);
        $this->getJson('/api/notifications')->assertStatus(200);
        $this->getJson('/api/notifications')->assertStatus(429);

        // User 2 should still have their full limit
        $this->actingAs($user2, 'api');
        $response = $this->getJson('/api/notifications');
        $response->assertStatus(200);
    }

    public function test_multi_tier_rate_limiting_hour_limit()
    {
        $user = $this->createTestUser();
        $user->rate_limit = 1000; // High minute limit
        $user->hourly_limit = 2; // Low hour limit

        $this->actingAs($user, 'api');

        // First request - allowed
        $this->getJson('/api/notifications')->assertStatus(200);

        // Second request - allowed
        $this->getJson('/api/notifications')->assertStatus(200);

        // Third request - should be blocked by hourly limit
        $response = $this->getJson('/api/notifications');
        $response->assertStatus(429);
        $response->assertJson([
            'tier' => 'hour',
        ]);
    }

    public function test_rate_limit_uses_default_values_when_not_set()
    {
        $user = $this->createTestUser();
        // Don't set rate_limit, should use default (60)

        $this->actingAs($user, 'api');

        $response = $this->getJson('/api/notifications');
        $response->assertStatus(200);

        // Should have default limit of 60
        $this->assertEquals(60, $response->headers->get('X-RateLimit-Limit'));
    }

    private function createTestUser(): User
    {
        $role = null;
        if (class_exists(Role::class)) {
            try {
                $role = Role::create(['name' => 'TestRole_' . uniqid()]);
            } catch (\Exception $e) {
                // Role creation failed
            }
        }

        $council = Council::create([
            'name' => 'Test Council ' . uniqid(),
            'description' => 'A test council'
        ]);

        $userOverride = [
            'council_id' => $council->id,
        ];

        if ($role) {
            $userOverride['role_id'] = $role->id;
        }

        return User::factory()->create($userOverride);
    }
}
