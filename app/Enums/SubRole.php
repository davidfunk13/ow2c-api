<?php

namespace App\Enums;

enum SubRole: string
{
    // Tank
    case Bruiser = 'bruiser';
    case Initiator = 'initiator';
    case Stalwart = 'stalwart';

    // Damage
    case Flanker = 'flanker';
    case Sharpshooter = 'sharpshooter';
    case Specialist = 'specialist';
    case Recon = 'recon';

    // Support
    case Medic = 'medic';
    case Survivor = 'survivor';
    case Tactician = 'tactician';

    public function role(): Role
    {
        return match ($this) {
            self::Bruiser, self::Initiator, self::Stalwart => Role::Tank,
            self::Flanker, self::Sharpshooter, self::Specialist, self::Recon => Role::Damage,
            self::Medic, self::Survivor, self::Tactician => Role::Support,
        };
    }

    /**
     * @return array<SubRole>
     */
    public static function forRole(Role $role): array
    {
        return match ($role) {
            Role::Tank => [self::Bruiser, self::Initiator, self::Stalwart],
            Role::Damage => [self::Flanker, self::Sharpshooter, self::Specialist, self::Recon],
            Role::Support => [self::Medic, self::Survivor, self::Tactician],
        };
    }
}
