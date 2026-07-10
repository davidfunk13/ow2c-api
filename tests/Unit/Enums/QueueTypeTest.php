<?php

namespace Tests\Unit\Enums;

use App\Enums\QueueType;
use PHPUnit\Framework\TestCase;

class QueueTypeTest extends TestCase
{
    public function test_queue_type_has_correct_labels(): void
    {
        $this->assertEquals('Competitive (Role Queue)', QueueType::CompetitiveRoleQueue->label());
        $this->assertEquals('Competitive (Open Queue)', QueueType::CompetitiveOpenQueue->label());
        $this->assertEquals('Quick Play', QueueType::QuickPlay->label());
        $this->assertEquals('Arcade', QueueType::Arcade->label());
        $this->assertEquals('Custom', QueueType::Custom->label());
    }

    public function test_queue_type_team_size(): void
    {
        $this->assertEquals(5, QueueType::CompetitiveRoleQueue->teamSize());
        $this->assertEquals(6, QueueType::CompetitiveOpenQueue->teamSize());
        $this->assertEquals(5, QueueType::QuickPlay->teamSize());
        $this->assertEquals(5, QueueType::Arcade->teamSize());
        $this->assertEquals(5, QueueType::Custom->teamSize());
    }
}
