<?php

namespace App\Http\Controllers\Session;

use App\Http\Controllers\Controller;
use App\Http\Controllers\NotFoundResponseTrait;
use App\Http\Repositories\BattletagRepository;
use App\Http\Repositories\SessionRepository;
use App\Http\Resources\SessionResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ShowController extends Controller
{
    use NotFoundResponseTrait;
    protected SessionRepository $sessionRepository;
    protected BattletagRepository $battletagRepository;

    public function __construct(SessionRepository $sessionRepository, BattletagRepository $battletagRepository)
    {
        $this->sessionRepository = $sessionRepository;
        $this->battletagRepository = $battletagRepository;
    }

    public function __invoke(string $battletagId, string $sessionId, Request $request): SessionResource | JsonResponse
    {
        $session = $this->sessionRepository->getById($battletagId, $sessionId);
        
        if(!$session){
           return $this->resourceNotFound('Session');
        }

        return new SessionResource($session);
    }
}
