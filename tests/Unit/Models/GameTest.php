<?php

namespace Tests\Unit\Models;

use App\Enums\DataSource;
use App\Models\Game;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GameTest extends TestCase
{
    use RefreshDatabase;

    public function test_game_is_placement_defaults_to_false(): void
    {
        $user = User::factory()->create();
        $game = Game::create([
            'user_id' => $user->id,
            'queue_type' => 'competitive_role_queue',
            'result' => 'win',
            'played_at' => now(),
        ]);

        $this->assertFalse($game->is_placement);
    }

    public function test_game_data_source_defaults_to_manual(): void
    {
        $user = User::factory()->create();
        $game = Game::create([
            'user_id' => $user->id,
            'queue_type' => 'competitive_role_queue',
            'result' => 'win',
            'played_at' => now(),
        ]);

        $this->assertEquals(DataSource::Manual, $game->data_source);
    }
}
