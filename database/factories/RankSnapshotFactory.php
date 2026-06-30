<?php

namespace Database\Factories;

use App\Enums\RankTier;
use App\Models\RankSnapshot;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<RankSnapshot>
 */
class RankSnapshotFactory extends Factory
{
    public function definition(): array
    {
        $tier = fake()->randomElement(RankTier::cases());
        $division = $tier === RankTier::Champion ? null : fake()->numberBetween(1, 5);

        return [
            'user_id' => User::factory(),
            'game_id' => null,
            'role' => fake()->randomElement(['tank', 'damage', 'support', 'open_queue']),
            'tier' => $tier,
            'division' => $division,
            'rank_value' => $tier->rankValue($division),
            'progress_percent' => null,
            'snapshot_type' => 'post_game',
            'season' => null,
            'recorded_at' => now(),
        ];
    }
}
