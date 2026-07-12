<?php

namespace Tests\Feature;

use App\Models\Game;
use App\Models\Hero;
use App\Models\Map;
use App\Models\PlaySession;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GameControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function completePayload(array $overrides = []): array
    {
        $heroes = Hero::factory()->count(2)->create();

        return array_merge([
            'queue_type' => 'competitive_role_queue',
            'result' => 'win',
            'role_played' => 'tank',
            'heroes' => $heroes->pluck('id')->all(),
        ], $overrides);
    }

    public function test_index_returns_paginated_games(): void
    {
        Game::factory()->count(3)->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user)->getJson('/api/games');

        $response->assertOk();
        $response->assertJsonCount(3, 'data');
        $response->assertJsonStructure([
            'data' => ['*' => ['id', 'queue_type', 'result', 'status', 'played_at']],
            'links',
            'meta',
        ]);
    }

    public function test_index_only_returns_own_games(): void
    {
        Game::factory()->count(2)->create(['user_id' => $this->user->id]);
        Game::factory()->count(3)->create();

        $response = $this->actingAs($this->user)->getJson('/api/games');

        $response->assertOk();
        $response->assertJsonCount(2, 'data');
    }

    public function test_index_ordered_by_played_at_desc(): void
    {
        Game::factory()->create(['user_id' => $this->user->id, 'played_at' => now()->subDay(), 'notes' => 'older']);
        Game::factory()->create(['user_id' => $this->user->id, 'played_at' => now(), 'notes' => 'newer']);

        $response = $this->actingAs($this->user)->getJson('/api/games');

        $this->assertEquals('newer', $response->json('data.0.notes'));
        $this->assertEquals('older', $response->json('data.1.notes'));
    }

    public function test_index_requires_authentication(): void
    {
        $this->getJson('/api/games')->assertUnauthorized();
    }

    public function test_index_filters_by_result(): void
    {
        Game::factory()->create(['user_id' => $this->user->id, 'result' => 'win']);
        Game::factory()->count(2)->create(['user_id' => $this->user->id, 'result' => 'loss']);

        $response = $this->actingAs($this->user)->getJson('/api/games?result=win');

        $response->assertOk();
        $response->assertJsonCount(1, 'data');
        $response->assertJsonPath('data.0.result', 'win');
    }

    public function test_index_filters_by_role(): void
    {
        Game::factory()->create(['user_id' => $this->user->id, 'role_played' => 'tank']);
        Game::factory()->create(['user_id' => $this->user->id, 'role_played' => 'support']);

        $response = $this->actingAs($this->user)->getJson('/api/games?role=tank');

        $response->assertOk();
        $response->assertJsonCount(1, 'data');
        $response->assertJsonPath('data.0.role_played', 'tank');
    }

    public function test_index_filters_by_hero(): void
    {
        $hero = Hero::factory()->create();
        $withHero = Game::factory()->create(['user_id' => $this->user->id]);
        $withHero->gameHeroes()->create(['hero_id' => $hero->id, 'is_primary' => true]);
        Game::factory()->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user)->getJson("/api/games?hero_id={$hero->id}");

        $response->assertOk();
        $response->assertJsonCount(1, 'data');
        $response->assertJsonPath('data.0.id', $withHero->id);
    }

    public function test_index_searches_notes(): void
    {
        Game::factory()->create(['user_id' => $this->user->id, 'notes' => 'great comeback']);
        Game::factory()->create(['user_id' => $this->user->id, 'notes' => 'rough one']);

        $response = $this->actingAs($this->user)->getJson('/api/games?search=comeback');

        $response->assertOk();
        $response->assertJsonCount(1, 'data');
    }

    public function test_store_creates_complete_game_with_heroes(): void
    {
        $map = Map::factory()->create();
        $payload = $this->completePayload(['map_id' => $map->id, 'notes' => 'gg']);

        $response = $this->actingAs($this->user)->postJson('/api/games', $payload);

        $response->assertCreated();
        $response->assertJsonPath('data.result', 'win');
        $response->assertJsonPath('data.status', 'complete');
        $response->assertJsonCount(2, 'data.heroes');
        $this->assertDatabaseHas('games', [
            'user_id' => $this->user->id,
            'result' => 'win',
            'status' => 'complete',
            'map_id' => $map->id,
        ]);
        $this->assertDatabaseCount('game_heroes', 2);
    }

    public function test_store_marks_first_hero_primary(): void
    {
        $heroes = Hero::factory()->count(3)->create();
        $ids = $heroes->pluck('id')->all();

        $this->actingAs($this->user)->postJson('/api/games', [
            'queue_type' => 'competitive_role_queue',
            'result' => 'loss',
            'heroes' => $ids,
        ])->assertCreated();

        $this->assertDatabaseHas('game_heroes', ['hero_id' => $ids[0], 'is_primary' => true]);
        $this->assertDatabaseHas('game_heroes', ['hero_id' => $ids[1], 'is_primary' => false]);
    }

    public function test_store_honors_explicit_primary_hero(): void
    {
        $heroes = Hero::factory()->count(2)->create();
        $ids = $heroes->pluck('id')->all();

        $this->actingAs($this->user)->postJson('/api/games', [
            'queue_type' => 'competitive_role_queue',
            'result' => 'win',
            'heroes' => $ids,
            'primary_hero_id' => $ids[1],
        ])->assertCreated();

        $this->assertDatabaseHas('game_heroes', ['hero_id' => $ids[1], 'is_primary' => true]);
        $this->assertDatabaseHas('game_heroes', ['hero_id' => $ids[0], 'is_primary' => false]);
    }

    public function test_store_saves_in_progress_game_without_result_or_heroes(): void
    {
        $response = $this->actingAs($this->user)->postJson('/api/games', [
            'queue_type' => 'competitive_role_queue',
            'status' => 'in_progress',
        ]);

        $response->assertCreated();
        $response->assertJsonPath('data.status', 'in_progress');
        $response->assertJsonPath('data.result', null);
        $this->assertDatabaseHas('games', ['user_id' => $this->user->id, 'status' => 'in_progress', 'result' => null]);
    }

    public function test_store_defaults_status_to_complete(): void
    {
        $this->actingAs($this->user)->postJson('/api/games', $this->completePayload())
            ->assertCreated()
            ->assertJsonPath('data.status', 'complete');
    }

    public function test_store_complete_game_requires_result(): void
    {
        $payload = $this->completePayload();
        unset($payload['result']);

        $this->actingAs($this->user)->postJson('/api/games', $payload)
            ->assertStatus(422)
            ->assertJsonValidationErrors('result');
    }

    public function test_store_complete_game_requires_heroes(): void
    {
        $this->actingAs($this->user)->postJson('/api/games', [
            'queue_type' => 'competitive_role_queue',
            'result' => 'win',
        ])->assertStatus(422)->assertJsonValidationErrors('heroes');
    }

    public function test_store_requires_queue_type(): void
    {
        $payload = $this->completePayload();
        unset($payload['queue_type']);

        $this->actingAs($this->user)->postJson('/api/games', $payload)
            ->assertStatus(422)->assertJsonValidationErrors('queue_type');
    }

    public function test_store_rejects_invalid_result(): void
    {
        $this->actingAs($this->user)->postJson('/api/games', $this->completePayload(['result' => 'flawless']))
            ->assertStatus(422)->assertJsonValidationErrors('result');
    }

    public function test_store_rejects_other_users_play_session(): void
    {
        $otherSession = PlaySession::factory()->create();

        $this->actingAs($this->user)->postJson('/api/games', $this->completePayload(['play_session_id' => $otherSession->id]))
            ->assertStatus(422)->assertJsonValidationErrors('play_session_id');
    }

    public function test_store_accepts_own_play_session(): void
    {
        $session = PlaySession::factory()->create(['user_id' => $this->user->id]);

        $this->actingAs($this->user)->postJson('/api/games', $this->completePayload(['play_session_id' => $session->id]))
            ->assertCreated()->assertJsonPath('data.play_session_id', $session->id);
    }

    public function test_store_requires_authentication(): void
    {
        $this->postJson('/api/games', $this->completePayload())->assertUnauthorized();
    }

    public function test_show_returns_own_game_with_heroes(): void
    {
        $game = Game::factory()->create(['user_id' => $this->user->id]);
        $hero = Hero::factory()->create();
        $game->gameHeroes()->create(['hero_id' => $hero->id, 'is_primary' => true]);

        $response = $this->actingAs($this->user)->getJson("/api/games/{$game->id}");

        $response->assertOk();
        $response->assertJsonPath('data.id', $game->id);
        $response->assertJsonPath('data.heroes.0.hero_id', $hero->id);
    }

    public function test_show_returns_404_for_other_users_game(): void
    {
        $game = Game::factory()->create();

        $this->actingAs($this->user)->getJson("/api/games/{$game->id}")->assertNotFound();
    }

    public function test_update_modifies_game(): void
    {
        $game = Game::factory()->create(['user_id' => $this->user->id, 'result' => 'loss']);

        $this->actingAs($this->user)->putJson("/api/games/{$game->id}", ['result' => 'win', 'notes' => 'comeback'])
            ->assertOk()->assertJsonPath('data.result', 'win');

        $this->assertDatabaseHas('games', ['id' => $game->id, 'result' => 'win', 'notes' => 'comeback']);
    }

    public function test_update_syncs_heroes(): void
    {
        $game = Game::factory()->create(['user_id' => $this->user->id]);
        $old = Hero::factory()->create();
        $game->gameHeroes()->create(['hero_id' => $old->id, 'is_primary' => true]);
        $new = Hero::factory()->count(2)->create();

        $this->actingAs($this->user)->putJson("/api/games/{$game->id}", ['heroes' => $new->pluck('id')->all()])
            ->assertOk();

        $this->assertDatabaseMissing('game_heroes', ['game_id' => $game->id, 'hero_id' => $old->id]);
        $this->assertDatabaseCount('game_heroes', 2);
    }

    public function test_update_can_finish_in_progress_game(): void
    {
        $game = Game::factory()->create(['user_id' => $this->user->id, 'status' => 'in_progress', 'result' => null]);

        $this->actingAs($this->user)->putJson("/api/games/{$game->id}", ['status' => 'complete', 'result' => 'win'])
            ->assertOk()->assertJsonPath('data.status', 'complete');
    }

    public function test_update_returns_404_for_other_users_game(): void
    {
        $game = Game::factory()->create();

        $this->actingAs($this->user)->putJson("/api/games/{$game->id}", ['result' => 'win'])->assertNotFound();
    }

    public function test_destroy_deletes_game(): void
    {
        $game = Game::factory()->create(['user_id' => $this->user->id]);

        $this->actingAs($this->user)->deleteJson("/api/games/{$game->id}")->assertOk();

        $this->assertDatabaseMissing('games', ['id' => $game->id]);
    }

    public function test_destroy_returns_404_for_other_users_game(): void
    {
        $game = Game::factory()->create();

        $this->actingAs($this->user)->deleteJson("/api/games/{$game->id}")->assertNotFound();
    }

    public function test_show_requires_authentication(): void
    {
        $game = Game::factory()->create(['user_id' => $this->user->id]);

        $this->getJson("/api/games/{$game->id}")->assertUnauthorized();
    }

    public function test_update_requires_authentication(): void
    {
        $game = Game::factory()->create(['user_id' => $this->user->id]);

        $this->putJson("/api/games/{$game->id}", ['result' => 'win'])->assertUnauthorized();
    }

    public function test_destroy_requires_authentication(): void
    {
        $game = Game::factory()->create(['user_id' => $this->user->id]);

        $this->deleteJson("/api/games/{$game->id}")->assertUnauthorized();
    }

    public function test_update_rejects_an_invalid_result(): void
    {
        $game = Game::factory()->create(['user_id' => $this->user->id]);

        $this->actingAs($this->user)->putJson("/api/games/{$game->id}", ['result' => 'flawless'])
            ->assertStatus(422)->assertJsonValidationErrors('result');
    }

    public function test_update_cannot_complete_a_game_without_a_result(): void
    {
        $game = Game::factory()->create(['user_id' => $this->user->id, 'status' => 'in_progress', 'result' => null]);

        $this->actingAs($this->user)->putJson("/api/games/{$game->id}", ['status' => 'complete'])
            ->assertStatus(422)->assertJsonValidationErrors('result');
    }

    public function test_update_cannot_null_the_result_of_a_complete_game(): void
    {
        $game = Game::factory()->create(['user_id' => $this->user->id, 'status' => 'complete', 'result' => 'win']);

        $this->actingAs($this->user)->putJson("/api/games/{$game->id}", ['result' => null])
            ->assertStatus(422)->assertJsonValidationErrors('result');
    }

    public function test_update_cannot_clear_all_heroes_on_a_complete_game(): void
    {
        $game = Game::factory()->create(['user_id' => $this->user->id, 'status' => 'complete', 'result' => 'win']);
        $game->gameHeroes()->create(['hero_id' => Hero::factory()->create()->id, 'is_primary' => true]);

        $this->actingAs($this->user)->putJson("/api/games/{$game->id}", ['heroes' => []])
            ->assertStatus(422)->assertJsonValidationErrors('heroes');
    }
}
