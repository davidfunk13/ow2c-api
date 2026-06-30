<?php

namespace Database\Factories;

use App\Enums\GameResult;
use App\Models\Game;
use App\Models\GameRound;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<GameRound>
 */
class GameRoundFactory extends Factory
{
    public function definition(): array
    {
        return [
            'game_id' => Game::factory(),
            'round_number' => fake()->numberBetween(1, 5),
            'map_submap_id' => null,
            'result' => fake()->randomElement(GameResult::cases()),
            'side' => fake()->optional()->randomElement(['attack', 'defense']),
            'score_team' => null,
            'score_enemy' => null,
            'distance_meters' => null,
            'checkpoints_reached' => null,
            'is_overtime' => false,
        ];
    }
}
