<?php

namespace App\Enums;

enum RankTier: string
{
    case Bronze = 'bronze';
    case Silver = 'silver';
    case Gold = 'gold';
    case Platinum = 'platinum';
    case Diamond = 'diamond';
    case Master = 'master';
    case Grandmaster = 'grandmaster';
    case Champion = 'champion';

    public function rankValue(?int $division): int
    {
        $tierBase = match ($this) {
            self::Bronze => 100,
            self::Silver => 200,
            self::Gold => 300,
            self::Platinum => 400,
            self::Diamond => 500,
            self::Master => 600,
            self::Grandmaster => 700,
            self::Champion => 800,
        };

        if ($this === self::Champion || $division === null) {
            return $tierBase;
        }

        return $tierBase + (6 - $division) * 5;
    }
}
