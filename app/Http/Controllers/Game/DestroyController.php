<?php

namespace App\Http\Controllers\Game;

use App\Http\Controllers\Controller;
use App\Http\Controllers\NotFoundResponseTrait;
use App\Http\Controllers\ServerErrorResponseTrait;
use App\Http\Repositories\BattletagRepository;
use App\Http\Repositories\GameRepository;
use App\Http\Repositories\SessionRepository;
use Illuminate\Http\JsonResponse;

class DestroyController extends Controller
{
    use NotFoundResponseTrait;
    use ServerErrorResponseTrait;
    protected GameRepository $gameRepository;

    public function __construct(GameRepository $gameRepository,)
    {
        $this->gameRepository = $gameRepository;
    }

    public function __invoke(string $battletagId, string $sessionId, string $gameId)
    {
        $game = $this->gameRepository->getById($battletagId, $sessionId, $gameId);

        if (!$game) {
            return $this->resourceNotFound('Game');
        }

        try {
            $this->gameRepository->destroy($game);
        } catch (\Throwable $exception) {
            return $this->internalServerError('Game could not be deleted');
        }

        return new JsonResponse(null, 204);
    }
}
