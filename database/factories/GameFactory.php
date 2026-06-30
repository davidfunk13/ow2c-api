<?php

namespace Database\Factories;

use App\Enums\DataSource;
use App\Enums\GameResult;
use App\Enums\QueueType;
use App\Enums\Role;
use App\Models\Game;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Game>
 */
class GameFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'play_session_id' => null,
            'map_id' => null,
            'queue_type' => fake()->randomElement(QueueType::cases()),
            'result' => fake()->randomElement(GameResult::cases()),
            'role_played' => fake()->randomElement(Role::cases()),
            'played_at' => fake()->dateTimeBetween('-30 days', 'now'),
            'duration_seconds' => fake()->optional()->numberBetween(300, 1800),
            'is_placement' => false,
            'data_source' => DataSource::Manual,
            'notes' => null,
        ];
    }
}
