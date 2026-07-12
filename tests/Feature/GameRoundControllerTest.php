<?php

namespace Tests\Feature;

use App\Models\Game;
use App\Models\GameRound;
use App\Models\Hero;
use App\Models\Map;
use App\Models\MapSubmap;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GameRoundControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    private function gameWithMap(): Game
    {
        $map = Map::factory()->create();

        return Game::factory()->create(['user_id' => $this->user->id, 'map_id' => $map->id]);
    }

    public function test_store_requires_authentication(): void
    {
        $game = $this->gameWithMap();

        $this->postJson("/api/games/{$game->id}/rounds")->assertUnauthorized();
    }

    public function test_store_creates_a_round_with_auto_incrementing_round_number(): void
    {
        $game = $this->gameWithMap();

        $this->actingAs($this->user)
            ->postJson("/api/games/{$game->id}/rounds", ['result' => 'win'])
            ->assertCreated()
            ->assertJsonPath('data.round_number', 1)
            ->assertJsonPath('data.result', 'win');

        $this->actingAs($this->user)
            ->postJson("/api/games/{$game->id}/rounds", ['result' => 'loss'])
            ->assertCreated()
            ->assertJsonPath('data.round_number', 2);

        $this->assertDatabaseCount('game_rounds', 2);
    }

    public function test_store_syncs_round_heroes(): void
    {
        $game = $this->gameWithMap();
        $heroes = Hero::factory()->count(2)->create();

        $this->actingAs($this->user)
            ->postJson("/api/games/{$game->id}/rounds", [
                'result' => 'win',
                'heroes' => $heroes->pluck('id')->all(),
            ])
            ->assertCreated()
            ->assertJsonCount(2, 'data.heroes');

        $this->assertDatabaseCount('round_heroes', 2);
    }

    public function test_store_accepts_a_submap_from_the_games_map(): void
    {
        $game = $this->gameWithMap();
        $submap = MapSubmap::factory()->create(['map_id' => $game->map_id]);

        $this->actingAs($this->user)
            ->postJson("/api/games/{$game->id}/rounds", ['map_submap_id' => $submap->id])
            ->assertCreated()
            ->assertJsonPath('data.map_submap_id', $submap->id);
    }

    public function test_store_rejects_a_submap_from_a_different_map(): void
    {
        $game = $this->gameWithMap();
        $otherSubmap = MapSubmap::factory()->create();

        $this->actingAs($this->user)
            ->postJson("/api/games/{$game->id}/rounds", ['map_submap_id' => $otherSubmap->id])
            ->assertStatus(422);
    }

    public function test_store_rejects_an_invalid_side(): void
    {
        $game = $this->gameWithMap();

        $this->actingAs($this->user)
            ->postJson("/api/games/{$game->id}/rounds", ['side' => 'sideways'])
            ->assertStatus(422);
    }

    public function test_store_returns_404_for_another_users_game(): void
    {
        $game = Game::factory()->create();

        $this->actingAs($this->user)
            ->postJson("/api/games/{$game->id}/rounds", ['result' => 'win'])
            ->assertNotFound();
    }

    public function test_update_modifies_a_round_and_resyncs_heroes(): void
    {
        $game = $this->gameWithMap();
        $round = GameRound::factory()->create(['game_id' => $game->id, 'result' => 'loss']);
        $hero = Hero::factory()->create();

        $this->actingAs($this->user)
            ->putJson("/api/rounds/{$round->id}", ['result' => 'win', 'heroes' => [$hero->id]])
            ->assertOk()
            ->assertJsonPath('data.result', 'win')
            ->assertJsonCount(1, 'data.heroes');

        $this->assertDatabaseHas('game_rounds', ['id' => $round->id, 'result' => 'win']);
    }

    public function test_update_returns_404_for_another_users_round(): void
    {
        $round = GameRound::factory()->create();

        $this->actingAs($this->user)
            ->putJson("/api/rounds/{$round->id}", ['result' => 'win'])
            ->assertNotFound();
    }

    public function test_destroy_deletes_a_round(): void
    {
        $game = $this->gameWithMap();
        $round = GameRound::factory()->create(['game_id' => $game->id]);

        $this->actingAs($this->user)
            ->deleteJson("/api/rounds/{$round->id}")
            ->assertOk();

        $this->assertDatabaseMissing('game_rounds', ['id' => $round->id]);
    }

    public function test_destroy_returns_404_for_another_users_round(): void
    {
        $round = GameRound::factory()->create();

        $this->actingAs($this->user)
            ->deleteJson("/api/rounds/{$round->id}")
            ->assertNotFound();
    }

    public function test_update_requires_authentication(): void
    {
        $round = GameRound::factory()->create();

        $this->putJson("/api/rounds/{$round->id}", ['result' => 'win'])->assertUnauthorized();
    }

    public function test_destroy_requires_authentication(): void
    {
        $round = GameRound::factory()->create();

        $this->deleteJson("/api/rounds/{$round->id}")->assertUnauthorized();
    }

    public function test_update_rejects_an_invalid_side(): void
    {
        $game = $this->gameWithMap();
        $round = GameRound::factory()->create(['game_id' => $game->id]);

        $this->actingAs($this->user)
            ->putJson("/api/rounds/{$round->id}", ['side' => 'sideways'])
            ->assertStatus(422)->assertJsonValidationErrors('side');
    }

    public function test_game_show_includes_its_rounds(): void
    {
        $game = $this->gameWithMap();
        $round = GameRound::factory()->create(['game_id' => $game->id, 'result' => 'win']);

        $this->actingAs($this->user)
            ->getJson("/api/games/{$game->id}")
            ->assertOk()
            ->assertJsonPath('data.rounds.0.id', $round->id)
            ->assertJsonPath('data.rounds.0.result', 'win');
    }
}
