<?php

namespace App\Http\Controllers\Game;

use App\Http\Controllers\Controller;
use App\Http\Repositories\GameRepository;
use App\Http\Resources\GameResource;

class ShowController extends Controller
{
    protected GameRepository $gameRepository;

    public function __construct(GameRepository $gameRepository)
    {
        $this->gameRepository = $gameRepository;
    }

    public function __invoke(string $battletag_id, string $session_id, string $game_id)
    {
        $game = $this->gameRepository->getById($battletag_id, $session_id, $game_id);
        
        if(!$game){
           return $this->resourceNotFound('Session');
        }

        return new GameResource($game);
    }
}
