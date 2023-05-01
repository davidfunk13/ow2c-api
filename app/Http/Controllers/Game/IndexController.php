<?php

namespace App\Http\Controllers\Game;

use App\Http\Controllers\Controller;
use App\Http\Repositories\GameRepository;
use App\Http\Resources\GameCollection;

class IndexController extends Controller
{
    protected GameRepository $gameRepository;

    public function __construct(GameRepository $gameRepository)
    {
        $this->gameRepository = $gameRepository;
    }

    public function __invoke(int $battletag_id, int $session_id){
        try {
            $games = $this->gameRepository->getAll($battletag_id, $session_id);
        } catch (\Throwable $exception) {
            return $this->internalServerError('Something went getting your Games.');
        }

        return new GameCollection($games);
    }
}
