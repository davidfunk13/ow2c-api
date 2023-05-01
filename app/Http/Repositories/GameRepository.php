<?php

declare(strict_types=1);

namespace App\Http\Repositories;

use App\Http\Controllers\NotFoundResponseTrait;
use App\Http\Resources\GameResource;
use App\Models\Battletag;
use App\Models\Game;
use App\Models\Session;
use Illuminate\Http\JsonResponse;

class GameRepository
{
    use NotFoundResponseTrait;

    public function setFields(Game &$game, array $options): void
    {
        $game->location = $options['location'];
        $game->outcome = $options['outcome'];
    }
    public function store(string $battletag_id, string $session_id, array $options): ?Game
    {
        $game = new Game();

        $session = new Session();

        $battletag = new Battletag();

        $battletag = $battletag->get($battletag_id);

        $session = $battletag->get($session_id);

        if (!$battletag) {
            $this->resourceNotFound("Battletag");
        }

        if (!$session) {
            $this->resourceNotFound("Battletag");
        }

        $game->battletag_id = $battletag->id;
        $game->session_id = $session->id;

        $this->setFields($game, $options);

        if ($game->save()) {
            return $game;
        }

        return null;
    }

    public function getById(string $battletag_id, string $session_id, string $game_id): ?Game
    {
        $battletag_query_builder = Battletag::query();

        $battletag = $battletag_query_builder->where('id', $battletag_id)->first();

        if (!$battletag) {
            return null;
        }

        $session_query_builder = Session::query();

        $session = $session_query_builder->where('battletag_id', $battletag_id)->where('id', $session_id)->first();

        if (!$session) {
            return null;
        }

        $game_query_builder = Game::query();

        $game = $game_query_builder->where('battletag_id', $battletag_id)->where('session_id', $session_id)->where('id', $game_id)->first();

        if (!$game) {
            return null;
        }

        return $game;
    }


    public function destroy(Game $game): ?bool
    {
        return $game->delete();
    }
}
