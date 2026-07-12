<?php

namespace Tests\Unit\Enums;

use App\Enums\RankTier;
use PHPUnit\Framework\TestCase;

class RankTierTest extends TestCase
{
    public function test_rank_tier_rank_value_bronze_5(): void
    {
        $this->assertEquals(105, RankTier::Bronze->rankValue(5));
    }

    public function test_rank_tier_rank_value_bronze_1(): void
    {
        $this->assertEquals(125, RankTier::Bronze->rankValue(1));
    }

    public function test_rank_tier_rank_value_gold_1(): void
    {
        $this->assertEquals(325, RankTier::Gold->rankValue(1));
    }

    public function test_rank_tier_rank_value_grandmaster_1(): void
    {
        $this->assertEquals(725, RankTier::Grandmaster->rankValue(1));
    }

    public function test_rank_tier_rank_value_champion_null(): void
    {
        $this->assertEquals(800, RankTier::Champion->rankValue(null));
    }
}
