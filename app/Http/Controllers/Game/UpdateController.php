<?php

namespace App\Http\Controllers\Game;

use App\Http\Controllers\Controller;
use App\Http\Repositories\GameRepository;
use App\Http\Resources\GameResource;
use Illuminate\Http\Request;

class UpdateController extends Controller
{
    protected GameRepository $gameRepository;

    public function __construct(GameRepository $gameRepository)
    {
        $this->gameRepository = $gameRepository;
    }

    public function __invoke(string $battletag_id, string $session_id, string $game_id,  Request $request)
    {
        $game = $this->gameRepository->getById($battletag_id, $session_id, $game_id);

        if (!$game) {
            return $this->resourceNotFound('Session');
        }

        try {
            $options = $request->all();

            $options['battletag_id'] = $battletag_id;

            $updated = $this->gameRepository->updateGame($game, $options);
        } catch (\Throwable $exception) {
            return $this->internalServerError('Game could not be updated');
        }

        if (!$updated) {
            return $this->internalServerError('Game could not be updated');
        }

        return new GameResource($updated);
    }
}
