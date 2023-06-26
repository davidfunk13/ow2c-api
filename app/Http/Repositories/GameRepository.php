<?php

declare(strict_types=1);

namespace App\Http\Repositories;

use App\Http\Controllers\NotFoundResponseTrait;
use App\Http\Resources\GameResource;
use App\Models\Battletag;
use App\Models\Game;
use App\Models\Session;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;

class GameRepository
{
    use NotFoundResponseTrait;

    public function setFields(Game &$game, array $options): void
    {
        $game->location = $options['location'];
        $game->result = $options['result'];
    }
    public function store(string $battletag_id, string $session_id, array $options): ?Game
    {
        $session = Session::find($session_id)->where('battletag_id', $battletag_id)->first();

        if (!$session) {
            $this->resourceNotFound("Session");
        }

        $game = new Game();
        $game->battletag_id = $session->battletag_id;
        $game->session_id = $session->id;

        $this->setFields($game, $options);

        if ($game->save()) {
            return $game;
        }

        return null;
    }

    public function getById(string $battletag_id, string $session_id, string $game_id): ?Game
    {
        $game = Game::find($game_id);        

        if (!$game) {
            return null;
        }

        return $game;
    }
    public function getAll(string $battletag_id, string $session_id): Collection | JsonResponse
    {
        $session = Session::find($session_id)->where('battletag_id', $battletag_id)->first();

        if (!$session) {
            return $this->resourceNotFound("Session");
        }

        return $session->games;
    }

    public function updateGame(Game $game, array $options): ?Game
    {
        $this->setFields($game, $options);

        if ($game->save()) {
            return $game;
        }

        return null;
    }
    
    public function destroy(Game $game): ?bool
    {
        return $game->delete();
    }
}
