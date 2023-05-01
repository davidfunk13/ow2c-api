<?php

namespace App\Http\Controllers\Game;

use App\Http\Controllers\Controller;
use App\Http\Controllers\ServerErrorResponseTrait;
use App\Http\Repositories\GameRepository;
use App\Http\Requests\Game\StoreRequest;
use App\Http\Resources\GameResource;
use Symfony\Component\HttpFoundation\Response;

class StoreController extends Controller
{
    use ServerErrorResponseTrait;
    protected GameRepository $gameRepository;

    public function __construct(GameRepository $gameRepository)
    {
        $this->gameRepository = $gameRepository;
    }

    public function __invoke(int $battletag_id, int $session_id, StoreRequest $request): Response | GameResource {
        try {
            $game = $this->gameRepository->store($battletag_id, $session_id, $request->all());
        } catch (\Throwable $exception) {
            return $this->internalServerError($exception->getMessage());
        }

        return new GameResource($game);
    }
}
