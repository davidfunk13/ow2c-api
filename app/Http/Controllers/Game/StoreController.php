<?php

namespace App\Http\Controllers\Game;

use App\Http\Controllers\Controller;
use App\Http\Repositories\GameRepository;
use App\Http\Resources\GameResource;
use GuzzleHttp\Psr7\Response;
use Illuminate\Http\Request;

class StoreController extends Controller
{
    protected GameRepository $gameRepository;

    public function __construct(GameRepository $gameRepository)
    {
        $this->gameRepository = $gameRepository;
    }

    public function __invoke(int $battletag_id, int $session_id, Request $request): Response | GameResource {
        try {
            $game = $this->gameRepository->store($battletag_id, $session_id, $request->all());
        } catch (\Throwable $exception) {
            return $this->internalServerError('Game could not save to DB');
        }

        return new GameResource($game);
    }
}
