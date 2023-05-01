<?php

namespace App\Http\Controllers\Session;

use App\Http\Collections\SessionCollection;
use App\Http\Controllers\Controller;
use App\Http\Repositories\SessionRepository;
use App\Http\Controllers\ServerErrorResponseTrait;
use Symfony\Component\HttpFoundation\Response;

class IndexController extends Controller

{
    protected SessionRepository $sessionRepository;
    use ServerErrorResponseTrait;

    public function __construct(SessionRepository $sessionRepository)
    {
        $this->sessionRepository = $sessionRepository;
    }

    public function __invoke(string $id): SessionCollection | Response
    {
        try {
            $sessions = $this->sessionRepository->getSessionsByBattletagId($id);
        } catch (\Throwable $exception) {
            return $this->internalServerError('Something went wrong');
        }

        return new SessionCollection($sessions);
    }
}
