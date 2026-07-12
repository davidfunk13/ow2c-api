<?php

namespace Tests\Feature;

use App\Models\Game;
use App\Models\Map;
use App\Models\PlaySession;
use App\Models\User;
use Database\Seeders\MapSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PlaySessionControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_index_returns_paginated_sessions(): void
    {
        PlaySession::factory()->count(3)->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user)->getJson('/api/play-sessions');

        $response->assertOk();
        $response->assertJsonCount(3, 'data');
        $response->assertJsonStructure([
            'data' => [
                '*' => ['id', 'title', 'notes', 'started_at', 'ended_at', 'games_count'],
            ],
        ]);
    }

    public function test_index_only_returns_own_sessions(): void
    {
        PlaySession::factory()->count(2)->create(['user_id' => $this->user->id]);
        PlaySession::factory()->count(3)->create(); // other user

        $response = $this->actingAs($this->user)->getJson('/api/play-sessions');

        $response->assertOk();
        $response->assertJsonCount(2, 'data');
    }

    public function test_index_ordered_by_started_at_desc(): void
    {
        PlaySession::factory()->create([
            'user_id' => $this->user->id,
            'title' => 'Older',
            'started_at' => now()->subDays(2),
        ]);
        PlaySession::factory()->create([
            'user_id' => $this->user->id,
            'title' => 'Newer',
            'started_at' => now(),
        ]);

        $response = $this->actingAs($this->user)->getJson('/api/play-sessions');

        $response->assertOk();
        $this->assertEquals('Newer', $response->json('data.0.title'));
        $this->assertEquals('Older', $response->json('data.1.title'));
    }

    public function test_index_requires_authentication(): void
    {
        $response = $this->getJson('/api/play-sessions');

        $response->assertUnauthorized();
    }

    public function test_store_creates_session(): void
    {
        $response = $this->actingAs($this->user)->postJson('/api/play-sessions', [
            'title' => 'Evening Session',
            'notes' => 'Ranked grind',
        ]);

        $response->assertCreated();
        $response->assertJson(['data' => [
            'title' => 'Evening Session',
            'notes' => 'Ranked grind',
        ]]);
        $this->assertDatabaseHas('play_sessions', [
            'user_id' => $this->user->id,
            'title' => 'Evening Session',
        ]);
    }

    public function test_store_defaults_started_at_to_now(): void
    {
        $response = $this->actingAs($this->user)->postJson('/api/play-sessions', []);

        $response->assertCreated();
        $this->assertNotNull($response->json('data.started_at'));
    }

    public function test_store_accepts_custom_started_at(): void
    {
        $date = '2026-03-15T18:00:00.000000Z';

        $response = $this->actingAs($this->user)->postJson('/api/play-sessions', [
            'started_at' => $date,
        ]);

        $response->assertCreated();
        $this->assertEquals($date, $response->json('data.started_at'));
    }

    public function test_store_validates_ended_at_after_started_at(): void
    {
        $response = $this->actingAs($this->user)->postJson('/api/play-sessions', [
            'started_at' => '2026-03-15T20:00:00Z',
            'ended_at' => '2026-03-15T18:00:00Z',
        ]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors('ended_at');
    }

    public function test_store_ignores_client_supplied_user_id(): void
    {
        $otherUser = User::factory()->create();

        $response = $this->actingAs($this->user)->postJson('/api/play-sessions', [
            'title' => 'Mine',
            'user_id' => $otherUser->id,
        ]);

        $response->assertCreated();
        $this->assertDatabaseHas('play_sessions', [
            'id' => $response->json('data.id'),
            'user_id' => $this->user->id,
        ]);
        $this->assertDatabaseMissing('play_sessions', [
            'id' => $response->json('data.id'),
            'user_id' => $otherUser->id,
        ]);
    }

    public function test_store_validates_title_max_length(): void
    {
        $response = $this->actingAs($this->user)->postJson('/api/play-sessions', [
            'title' => str_repeat('a', 256),
        ]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors('title');
    }

    public function test_show_returns_session_with_games(): void
    {
        $this->seed(MapSeeder::class);
        $map = Map::first();

        $session = PlaySession::factory()->create(['user_id' => $this->user->id]);
        Game::factory()->create([
            'user_id' => $this->user->id,
            'play_session_id' => $session->id,
            'map_id' => $map->id,
        ]);

        $response = $this->actingAs($this->user)->getJson("/api/play-sessions/{$session->id}");

        $response->assertOk();
        $response->assertJsonPath('data.id', $session->id);
        $response->assertJsonCount(1, 'data.games');
        $response->assertJsonStructure([
            'data' => [
                'id', 'title', 'notes', 'started_at', 'ended_at', 'games_count',
                'games' => [
                    '*' => ['id', 'map', 'result', 'heroes', 'rounds'],
                ],
            ],
        ]);
    }

    public function test_show_returns_404_for_other_users_session(): void
    {
        $otherSession = PlaySession::factory()->create();

        $response = $this->actingAs($this->user)->getJson("/api/play-sessions/{$otherSession->id}");

        $response->assertNotFound();
    }

    public function test_update_modifies_session(): void
    {
        $session = PlaySession::factory()->create([
            'user_id' => $this->user->id,
            'title' => 'Old Title',
        ]);

        $response = $this->actingAs($this->user)->putJson("/api/play-sessions/{$session->id}", [
            'title' => 'New Title',
            'notes' => 'Updated notes',
        ]);

        $response->assertOk();
        $response->assertJson(['data' => ['title' => 'New Title', 'notes' => 'Updated notes']]);
    }

    public function test_update_returns_404_for_other_users_session(): void
    {
        $otherSession = PlaySession::factory()->create();

        $response = $this->actingAs($this->user)->putJson("/api/play-sessions/{$otherSession->id}", [
            'title' => 'Hacked',
        ]);

        $response->assertNotFound();
    }

    public function test_update_validates_ended_at_after_started_at(): void
    {
        $session = PlaySession::factory()->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user)->putJson("/api/play-sessions/{$session->id}", [
            'started_at' => '2026-03-15T20:00:00Z',
            'ended_at' => '2026-03-15T18:00:00Z',
        ]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors('ended_at');
    }

    public function test_update_rejects_ended_at_before_the_stored_started_at(): void
    {
        $session = PlaySession::factory()->create([
            'user_id' => $this->user->id,
            'started_at' => '2026-03-15T20:00:00Z',
            'ended_at' => null,
        ]);

        $response = $this->actingAs($this->user)->putJson("/api/play-sessions/{$session->id}", [
            'ended_at' => '2026-03-15T18:00:00Z',
        ]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors('ended_at');
    }

    public function test_destroy_deletes_session(): void
    {
        $session = PlaySession::factory()->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user)->deleteJson("/api/play-sessions/{$session->id}");

        $response->assertOk();
        $this->assertDatabaseMissing('play_sessions', ['id' => $session->id]);
    }

    public function test_destroy_returns_404_for_other_users_session(): void
    {
        $otherSession = PlaySession::factory()->create();

        $response = $this->actingAs($this->user)->deleteJson("/api/play-sessions/{$otherSession->id}");

        $response->assertNotFound();
    }

    public function test_end_sets_ended_at(): void
    {
        $session = PlaySession::factory()->create([
            'user_id' => $this->user->id,
            'ended_at' => null,
        ]);

        $response = $this->actingAs($this->user)->patchJson("/api/play-sessions/{$session->id}/end");

        $response->assertOk();
        $this->assertNotNull($response->json('data.ended_at'));
        $session->refresh();
        $this->assertNotNull($session->ended_at);
    }

    public function test_end_returns_404_for_other_users_session(): void
    {
        $otherSession = PlaySession::factory()->create();

        $response = $this->actingAs($this->user)->patchJson("/api/play-sessions/{$otherSession->id}/end");

        $response->assertNotFound();
    }

    public function test_index_includes_games_count(): void
    {
        $this->seed(MapSeeder::class);
        $map = Map::first();

        $session = PlaySession::factory()->create(['user_id' => $this->user->id]);
        Game::factory()->count(3)->create([
            'user_id' => $this->user->id,
            'play_session_id' => $session->id,
            'map_id' => $map->id,
        ]);

        $response = $this->actingAs($this->user)->getJson('/api/play-sessions');

        $response->assertOk();
        $this->assertEquals(3, $response->json('data.0.games_count'));
    }
}
