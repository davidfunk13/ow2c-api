<?php

namespace App\Http\Controllers\Session;

use App\Http\Controllers\Controller;
use App\Http\Controllers\NotFoundResponseTrait;
use App\Http\Controllers\ServerErrorResponseTrait;
use App\Http\Repositories\BattletagRepository;
use App\Http\Repositories\SessionRepository;
use App\Http\Resources\BattletagResource;
use Illuminate\Http\Client\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DestroyController extends Controller
{
    use NotFoundResponseTrait;
    use ServerErrorResponseTrait;

    protected SessionRepository $sessionRepository;
    protected BattletagRepository $battletagRepository;

    public function __construct(SessionRepository $sessionRepository, BattletagRepository $battletagRepository)
    {
        $this->sessionRepository = $sessionRepository;
        $this->battletagRepository = $battletagRepository;
    }

    public function __invoke(string $battletagId, string $sessionId)
    {
        $battletag = $this->battletagRepository->getById($battletagId);

        if (!$battletag) {
            return $this->resourceNotFound('Battletag');
        }

        $session = $this->sessionRepository->getById($battletagId, $sessionId);

        if (!$session) {
            return $this->resourceNotFound('Session');
        }

        try {
            $destroyed = $this->sessionRepository->destroy($session);
        } catch (\Throwable $exception) {
            return $this->internalServerError('Session could not be deleted');
        }

        if (!$destroyed) {
            return $this->internalServerError('Session could not be deleted');
        }

        return new JsonResponse(null, 204);
    }
}
