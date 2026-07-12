<?php

namespace Tests\Feature;

use App\Models\Game;
use App\Models\Hero;
use App\Models\HeroSrSnapshot;
use App\Models\RankSnapshot;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GameSnapshotControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    private function game(): Game
    {
        return Game::factory()->create(['user_id' => $this->user->id]);
    }

    public function test_sync_requires_authentication(): void
    {
        $game = $this->game();

        $this->putJson("/api/games/{$game->id}/snapshots")->assertUnauthorized();
    }

    public function test_sync_returns_404_for_another_users_game(): void
    {
        $game = Game::factory()->create();

        $this->actingAs($this->user)
            ->putJson("/api/games/{$game->id}/snapshots", ['ranks' => []])
            ->assertNotFound();
    }

    public function test_sync_creates_ranks_and_hero_srs_and_derives_rank_value(): void
    {
        $game = $this->game();
        $hero = Hero::factory()->create();

        $this->actingAs($this->user)
            ->putJson("/api/games/{$game->id}/snapshots", [
                'ranks' => [['role' => 'tank', 'tier' => 'gold', 'division' => 3, 'progress_percent' => 40]],
                'hero_srs' => [['hero_id' => $hero->id, 'sr_value' => 3200]],
            ])
            ->assertOk()
            ->assertJsonPath('data.rank_snapshots.0.tier', 'gold')
            ->assertJsonPath('data.rank_snapshots.0.rank_value', 315)
            ->assertJsonPath('data.hero_srs.0.sr_value', 3200);

        $this->assertDatabaseHas('rank_snapshots', [
            'game_id' => $game->id,
            'user_id' => $this->user->id,
            'role' => 'tank',
            'tier' => 'gold',
        ]);
        $this->assertDatabaseHas('hero_sr_snapshots', [
            'game_id' => $game->id,
            'user_id' => $this->user->id,
            'hero_id' => $hero->id,
            'sr_value' => 3200,
        ]);
    }

    public function test_sync_replaces_existing_snapshots(): void
    {
        $game = $this->game();
        RankSnapshot::factory()->create([
            'game_id' => $game->id,
            'user_id' => $this->user->id,
            'role' => 'damage',
        ]);

        $this->actingAs($this->user)
            ->putJson("/api/games/{$game->id}/snapshots", [
                'ranks' => [['role' => 'support', 'tier' => 'diamond']],
            ])
            ->assertOk()
            ->assertJsonCount(1, 'data.rank_snapshots')
            ->assertJsonPath('data.rank_snapshots.0.role', 'support');

        $this->assertDatabaseCount('rank_snapshots', 1);
    }

    public function test_sync_leaves_omitted_collections_untouched(): void
    {
        $game = $this->game();
        HeroSrSnapshot::factory()->create([
            'game_id' => $game->id,
            'user_id' => $this->user->id,
            'sr_value' => 3200,
        ]);

        $this->actingAs($this->user)
            ->putJson("/api/games/{$game->id}/snapshots", [
                'ranks' => [['role' => 'tank', 'tier' => 'gold']],
            ])
            ->assertOk()
            ->assertJsonCount(1, 'data.hero_srs');

        $this->assertDatabaseCount('hero_sr_snapshots', 1);
        $this->assertDatabaseHas('hero_sr_snapshots', ['game_id' => $game->id, 'sr_value' => 3200]);
    }

    public function test_sync_clears_a_collection_when_sent_empty(): void
    {
        $game = $this->game();
        HeroSrSnapshot::factory()->create(['game_id' => $game->id, 'user_id' => $this->user->id]);

        $this->actingAs($this->user)
            ->putJson("/api/games/{$game->id}/snapshots", ['hero_srs' => []])
            ->assertOk()
            ->assertJsonCount(0, 'data.hero_srs');

        $this->assertDatabaseCount('hero_sr_snapshots', 0);
    }

    public function test_sync_rejects_an_invalid_tier(): void
    {
        $game = $this->game();

        $this->actingAs($this->user)
            ->putJson("/api/games/{$game->id}/snapshots", [
                'ranks' => [['role' => 'tank', 'tier' => 'wood']],
            ])
            ->assertStatus(422);
    }

    public function test_sync_requires_an_sr_value_for_each_hero(): void
    {
        $game = $this->game();
        $hero = Hero::factory()->create();

        $this->actingAs($this->user)
            ->putJson("/api/games/{$game->id}/snapshots", [
                'hero_srs' => [['hero_id' => $hero->id]],
            ])
            ->assertStatus(422);
    }

    public function test_game_show_includes_snapshots(): void
    {
        $game = $this->game();
        RankSnapshot::factory()->create([
            'game_id' => $game->id,
            'user_id' => $this->user->id,
            'role' => 'tank',
            'tier' => 'master',
        ]);
        HeroSrSnapshot::factory()->create([
            'game_id' => $game->id,
            'user_id' => $this->user->id,
        ]);

        $this->actingAs($this->user)
            ->getJson("/api/games/{$game->id}")
            ->assertOk()
            ->assertJsonPath('data.rank_snapshots.0.tier', 'master')
            ->assertJsonCount(1, 'data.hero_srs');
    }
}
