<?php

namespace Tests\Unit\Models;

use App\Models\RankSnapshot;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RankSnapshotTest extends TestCase
{
    use RefreshDatabase;

    public function test_rank_snapshot_computes_rank_value(): void
    {
        $user = User::factory()->create();

        $bronze5 = RankSnapshot::create([
            'user_id' => $user->id,
            'role' => 'tank',
            'tier' => 'bronze',
            'division' => 5,
            'recorded_at' => now(),
        ]);
        $this->assertEquals(105, $bronze5->rank_value);

        $gold1 = RankSnapshot::create([
            'user_id' => $user->id,
            'role' => 'support',
            'tier' => 'gold',
            'division' => 1,
            'recorded_at' => now(),
        ]);
        $this->assertEquals(325, $gold1->rank_value);

        $champion = RankSnapshot::create([
            'user_id' => $user->id,
            'role' => 'damage',
            'tier' => 'champion',
            'division' => null,
            'recorded_at' => now(),
        ]);
        $this->assertEquals(800, $champion->rank_value);
    }

    public function test_rank_snapshot_recomputes_rank_value_on_update(): void
    {
        $user = User::factory()->create();
        $snapshot = RankSnapshot::create([
            'user_id' => $user->id,
            'role' => 'tank',
            'tier' => 'bronze',
            'division' => 5,
            'recorded_at' => now(),
        ]);
        $this->assertEquals(105, $snapshot->rank_value);

        $snapshot->update(['tier' => 'gold', 'division' => 1]);

        $this->assertEquals(325, $snapshot->rank_value);
    }
}
