<?php

namespace Database\Factories;

use App\Models\CustomStatDefinition;
use App\Models\Game;
use App\Models\GameCustomStat;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<GameCustomStat>
 */
class GameCustomStatFactory extends Factory
{
    public function definition(): array
    {
        return [
            'game_id' => Game::factory(),
            'custom_stat_definition_id' => CustomStatDefinition::factory(),
            'numeric_value' => fake()->randomFloat(2, 0, 100),
        ];
    }
}
