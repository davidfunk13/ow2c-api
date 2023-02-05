<?php

namespace App\Http\Controllers\Session;

use App\Http\Controllers\Controller;
use App\Http\Repositories\SessionRepository;
use App\Http\Resources\Session\SessionCollection;
use Illuminate\Http\Request;
use App\Http\Controllers\ServerErrorResponseTrait;

class IndexController extends Controller

{
    protected SessionRepository $sessionRepository;
    use ServerErrorResponseTrait;

    public function __construct(SessionRepository $sessionRepository)
    {
        $this->sessionRepository = $sessionRepository;
    }

    public function __invoke(int $battletagId, Request $request): SessionCollection
    {
        try {
            $session = $this->sessionRepository->getByBattletagId($battletagId, $request->all());
        } catch (\Throwable $exception) {
            return $this->internalServerError('Something went wrong');
        }

        return new SessionCollection($session);
    }
}
