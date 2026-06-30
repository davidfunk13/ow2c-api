<?php

namespace Database\Factories;

use App\Models\GamePlayer;
use App\Models\GamePlayerHero;
use App\Models\Hero;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<GamePlayerHero>
 */
class GamePlayerHeroFactory extends Factory
{
    public function definition(): array
    {
        return [
            'game_player_id' => GamePlayer::factory(),
            'hero_id' => Hero::factory(),
            'is_primary' => true,
        ];
    }
}
