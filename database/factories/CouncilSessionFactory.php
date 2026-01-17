<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Council;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CouncilSession>
 */
class CouncilSessionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(),
            'date' => fake()->dateTimeBetween('-1 year', '+1 year'),
            'description' => fake()->paragraph(),
            'material' => fake()->url(),
            'council_id' => Council::factory(), // Defaults to creating a new one
        ];
    }
}
