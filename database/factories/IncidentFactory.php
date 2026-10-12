<?php

namespace Database\Factories;

use App\Models\Incident;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Incident>
 */
class IncidentFactory extends Factory
{
    protected $model = Incident::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::query()->inRandomOrder()->value('id') ?? User::factory()->create()->id,
            'type' => fake()->randomElement(['bug', 'request', 'security', 'infra']),
            'title' => fake()->sentence(4),
            'description' => fake()->paragraph(),
            'data' => now(),
            'acting_officer' => fake()->name(),
        ];
    }
}
