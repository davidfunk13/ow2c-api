<?php

namespace Tests\Unit\Enums;

use App\Enums\Role;
use App\Enums\SubRole;
use PHPUnit\Framework\TestCase;

class SubRoleTest extends TestCase
{
    public function test_sub_role_for_role_tank(): void
    {
        $tankSubRoles = SubRole::forRole(Role::Tank);
        $this->assertCount(3, $tankSubRoles);
        $this->assertEquals([SubRole::Bruiser, SubRole::Initiator, SubRole::Stalwart], $tankSubRoles);
    }

    public function test_sub_role_for_role_damage(): void
    {
        $damageSubRoles = SubRole::forRole(Role::Damage);
        $this->assertCount(4, $damageSubRoles);
        $this->assertEquals([SubRole::Flanker, SubRole::Sharpshooter, SubRole::Specialist, SubRole::Recon], $damageSubRoles);
    }

    public function test_sub_role_for_role_support(): void
    {
        $supportSubRoles = SubRole::forRole(Role::Support);
        $this->assertCount(3, $supportSubRoles);
        $this->assertEquals([SubRole::Medic, SubRole::Survivor, SubRole::Tactician], $supportSubRoles);
    }

    public function test_sub_role_role_method(): void
    {
        $this->assertEquals(Role::Tank, SubRole::Bruiser->role());
        $this->assertEquals(Role::Tank, SubRole::Initiator->role());
        $this->assertEquals(Role::Tank, SubRole::Stalwart->role());
        $this->assertEquals(Role::Damage, SubRole::Flanker->role());
        $this->assertEquals(Role::Damage, SubRole::Sharpshooter->role());
        $this->assertEquals(Role::Damage, SubRole::Specialist->role());
        $this->assertEquals(Role::Damage, SubRole::Recon->role());
        $this->assertEquals(Role::Support, SubRole::Medic->role());
        $this->assertEquals(Role::Support, SubRole::Survivor->role());
        $this->assertEquals(Role::Support, SubRole::Tactician->role());
    }
}
