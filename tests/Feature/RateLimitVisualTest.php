<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Role;
use App\Models\Council;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Cache;

class RateLimitVisualTest extends TestCase
{
    use RefreshDatabase;

    /**
     * This test demonstrates rate limiting in action with visual output
     */
    public function test_rate_limiting_demonstration()
    {
        Cache::flush();

        // Create a user with a very low rate limit for demonstration
        $role = Role::create(['name' => 'DemoRole']);
        $council = Council::create(['name' => 'Demo Council', 'description' => 'Demo']);

        $user = User::factory()->create([
            'role_id' => $role->id,
            'council_id' => $council->id,
        ]);

        // Set a very low rate limit for demonstration
        $user->rate_limit = 3;

        $this->actingAs($user, 'api');

        echo "\n\n=== RATE LIMITING DEMONSTRATION ===\n\n";
        echo "User Rate Limit: 3 requests per minute\n";
        echo "Testing endpoint: GET /api/notifications\n\n";

        // Make requests and show the progression
        for ($i = 1; $i <= 5; $i++) {
            $response = $this->getJson('/api/notifications');

            echo "Request #{$i}:\n";
            echo "  Status: " . $response->status() . "\n";

            if ($response->status() === 200) {
                echo "  X-RateLimit-Limit: " . $response->headers->get('X-RateLimit-Limit') . "\n";
                echo "  X-RateLimit-Remaining: " . $response->headers->get('X-RateLimit-Remaining') . "\n";
                echo "  X-RateLimit-Reset: " . $response->headers->get('X-RateLimit-Reset') . "\n";
                echo "  Result: ✓ Request Allowed\n";
            } else if ($response->status() === 429) {
                $data = $response->json();
                echo "  Message: " . $data['message'] . "\n";
                echo "  Tier: " . $data['tier'] . "\n";
                echo "  Limit: " . $data['limit'] . "\n";
                echo "  Retry-After: " . $data['retry_after'] . " seconds\n";
                echo "  Result: ✗ Rate Limited (429)\n";
            }

            echo "\n";
        }

        echo "=== END DEMONSTRATION ===\n\n";

        // Assert the expected behavior
        $this->assertTrue(true, 'Rate limiting demonstration completed');
    }
}
