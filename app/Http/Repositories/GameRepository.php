<?php

declare(strict_types=1);

namespace App\Http\Repositories;

use App\Models\Game;

class GameRepository
{
    public function setFields(Game &$game, array $options): void
    {
        // $game->game_id = $options['game_id'];
    }
}
