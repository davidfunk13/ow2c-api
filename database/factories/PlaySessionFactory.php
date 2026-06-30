<?php

namespace Database\Factories;

use App\Models\PlaySession;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PlaySession>
 */
class PlaySessionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'title' => fake()->sentence(3),
            'notes' => fake()->optional()->paragraph(),
            'started_at' => fake()->dateTimeBetween('-30 days', 'now'),
            'ended_at' => null,
        ];
    }
}
