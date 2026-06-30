<?php

namespace Database\Factories;

use App\Enums\Role;
use App\Enums\TeamSide;
use App\Models\Game;
use App\Models\GamePlayer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<GamePlayer>
 */
class GamePlayerFactory extends Factory
{
    public function definition(): array
    {
        return [
            'game_id' => Game::factory(),
            'team_side' => fake()->randomElement(TeamSide::cases()),
            'role' => fake()->randomElement(Role::cases()),
            'player_name' => fake()->optional()->userName().'#'.fake()->numberBetween(1000, 9999),
            'slot_number' => fake()->numberBetween(1, 6),
        ];
    }
}
