<?php

namespace App\Http\Controllers\Session;

use App\Http\Controllers\Controller;
use App\Http\Repositories\BattletagRepository;
use App\Http\Repositories\SessionRepository;
use App\Http\Requests\Session\ShowRequest;
use App\Http\Resources\BattletagResource;
use App\Http\Resources\Session\SessionCollection;
use App\Http\Resources\SessionResource;
use Illuminate\Http\Request;

class ShowController extends Controller
{
    protected SessionRepository $sessionRepository;
    protected BattletagRepository $battletagRepository;

    public function __construct(SessionRepository $sessionRepository, BattletagRepository $battletagRepository)
    {
        $this->sessionRepository = $sessionRepository;
        $this->battletagRepository = $battletagRepository;
    }

    public function __invoke(string $battletagId, string $sessionId, Request $request)
    {
//        $session = $this->sessionRepository->getById($sessionId, $request->all());
      $session = $this->battletagRepository->getBattletagSessions($battletagId);


        return response()->json($session);
    }
}
