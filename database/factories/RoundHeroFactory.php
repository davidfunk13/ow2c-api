<?php

namespace Database\Factories;

use App\Models\GameRound;
use App\Models\Hero;
use App\Models\RoundHero;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<RoundHero>
 */
class RoundHeroFactory extends Factory
{
    public function definition(): array
    {
        return [
            'game_round_id' => GameRound::factory(),
            'hero_id' => Hero::factory(),
        ];
    }
}
