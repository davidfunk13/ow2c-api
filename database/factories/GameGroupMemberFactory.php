<?php

namespace Database\Factories;

use App\Models\Game;
use App\Models\GameGroupMember;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<GameGroupMember>
 */
class GameGroupMemberFactory extends Factory
{
    public function definition(): array
    {
        return [
            'game_id' => Game::factory(),
            'battletag' => fake()->userName().'#'.fake()->numberBetween(1000, 9999),
            'user_id' => null,
        ];
    }
}
