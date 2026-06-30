<?php

namespace Tests\Unit\Models;

use App\Models\Game;
use App\Models\GameRound;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GameRoundTest extends TestCase
{
    use RefreshDatabase;

    public function test_game_round_is_overtime_defaults_to_false(): void
    {
        $game = Game::factory()->create();
        $round = GameRound::create([
            'game_id' => $game->id,
            'round_number' => 1,
        ]);

        $this->assertFalse($round->is_overtime);
    }
}
